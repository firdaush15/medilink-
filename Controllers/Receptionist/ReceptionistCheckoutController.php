<?php
// app/Http/Controllers/Receptionist/ReceptionistCheckOutController.php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReceptionistCheckOutController extends Controller
{
    /**
     * Show checkout page - list completed appointments awaiting payment
     */
    public function index(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        
        // Get completed appointments that haven't been checked out
        $pendingCheckouts = Appointment::with(['patient.user', 'doctor.user', 'prescriptions'])
            ->whereDate('appointment_date', $date)
            ->where('status', Appointment::STATUS_COMPLETED)
            ->whereNull('checked_out_at')
            ->orderBy('consultation_ended_at', 'desc')
            ->get();
        
        // Get today's checkout history
        $checkedOutToday = Appointment::with(['patient.user', 'doctor.user', 'checkedOutBy'])
            ->whereDate('appointment_date', $date)
            ->whereNotNull('checked_out_at')
            ->orderBy('checked_out_at', 'desc')
            ->take(10)
            ->get();
        
        // Statistics
        $stats = [
            'pending_checkout' => $pendingCheckouts->count(),
            'checked_out_today' => Appointment::whereDate('checked_out_at', today())->count(),
            'total_collected_today' => Appointment::whereDate('checked_out_at', today())
                ->where('payment_collected', true)
                ->sum('payment_amount'),
        ];
        
        return view('receptionist.receptionist_checkout', compact(
            'pendingCheckouts',
            'checkedOutToday',
            'stats',
            'date'
        ));
    }
    
    /**
     * Show checkout form for specific appointment
     */
    public function show($appointmentId)
    {
        $appointment = Appointment::with([
            'patient.user',
            'doctor.user',
            'prescriptions.items',
            'prescriptions.dispensing'
        ])->findOrFail($appointmentId);
        
        // Verify appointment is completed
        if ($appointment->status !== Appointment::STATUS_COMPLETED) {
            return redirect()->back()->with('error', 'Appointment is not completed yet.');
        }
        
        // Calculate consultation fee (based on doctor specialization)
        $consultationFee = $this->calculateConsultationFee($appointment->doctor);
        
        // Calculate prescription/pharmacy fees if any
        $pharmacyFee = 0;
        if ($appointment->prescriptions->isNotEmpty()) {
            foreach ($appointment->prescriptions as $prescription) {
                if ($prescription->dispensing) {
                    $pharmacyFee += $prescription->dispensing->total_amount ?? 0;
                }
            }
        }
        
        // Calculate total
        $subtotal = $consultationFee + $pharmacyFee;
        $tax = $subtotal * 0.06; // 6% SST (Malaysia)
        $total = $subtotal + $tax;
        
        return view('receptionist.receptionist_checkoutForm', compact(
            'appointment',
            'consultationFee',
            'pharmacyFee',
            'subtotal',
            'tax',
            'total'
        ));
    }
    
    /**
     * Process checkout and payment
     */
    public function processCheckout(Request $request, $appointmentId)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,card,insurance,online',
            'amount_paid' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);
        
        $appointment = Appointment::findOrFail($appointmentId);
        
        // Verify appointment can be checked out
        if ($appointment->status !== Appointment::STATUS_COMPLETED) {
            return redirect()->back()->with('error', 'Cannot checkout: Appointment not completed.');
        }
        
        if ($appointment->checked_out_at) {
            return redirect()->back()->with('error', 'Appointment already checked out.');
        }
        
        DB::beginTransaction();
        
        try {
            // Update appointment with checkout info
            $appointment->update([
                'checked_out_at' => now(),
                'checked_out_by' => Auth::id(),
                'payment_collected' => true,
                'payment_amount' => $validated['amount_paid'],
                'checkout_notes' => $validated['notes'] ?? null,
            ]);
            
            // Update patient's last visit
            $appointment->patient->update([
                'last_visit_date' => now()
            ]);
            
            DB::commit();
            
            // Redirect to receipt
            return redirect()->route('receptionist.checkout.receipt', $appointment->appointment_id)
                ->with('success', 'Checkout completed successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Checkout failed: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Display receipt (printable)
     */
    public function receipt($appointmentId)
    {
        $appointment = Appointment::with([
            'patient.user',
            'doctor.user',
            'prescriptions.items',
            'prescriptions.dispensing',
            'checkedOutBy'
        ])->findOrFail($appointmentId);
        
        // Verify checked out
        if (!$appointment->checked_out_at) {
            return redirect()->back()->with('error', 'Appointment has not been checked out yet.');
        }
        
        // Calculate breakdown
        $consultationFee = $this->calculateConsultationFee($appointment->doctor);
        $pharmacyFee = 0;
        
        if ($appointment->prescriptions->isNotEmpty()) {
            foreach ($appointment->prescriptions as $prescription) {
                if ($prescription->dispensing) {
                    $pharmacyFee += $prescription->dispensing->total_amount ?? 0;
                }
            }
        }
        
        $subtotal = $consultationFee + $pharmacyFee;
        $tax = $subtotal * 0.06;
        $total = $subtotal + $tax;
        
        return view('receptionist.receptionist_receipt', compact(
            'appointment',
            'consultationFee',
            'pharmacyFee',
            'subtotal',
            'tax',
            'total'
        ));
    }
    
    /**
     * Calculate consultation fee based on doctor specialization
     */
    private function calculateConsultationFee($doctor): float
    {
        $fees = [
            'General Medicine' => 50.00,
            'Pediatrics' => 60.00,
            'Cardiology' => 80.00,
            'Orthopedics' => 75.00,
            'Dermatology' => 65.00,
            'Psychiatry' => 90.00,
            'Neurology' => 85.00,
            'Gastroenterology' => 75.00,
            'Endocrinology' => 70.00,
            'default' => 50.00,
        ];
        
        return $fees[$doctor->specialization] ?? $fees['default'];
    }
    
    /**
     * Search patient history for checkout
     */
    public function searchPatient(Request $request)
    {
        $query = $request->get('query');
        
        $patients = Patient::with(['user', 'appointments' => function($q) {
                $q->where('status', Appointment::STATUS_COMPLETED)
                  ->whereNull('checked_out_at')
                  ->orderBy('appointment_date', 'desc');
            }])
            ->whereHas('user', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->orWhere('phone_number', 'LIKE', "%{$query}%")
            ->take(10)
            ->get();
        
        return response()->json($patients);
    }
}