<?php
// app/Http/Controllers/Doctor/DoctorPrescriptionController.php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\PrescriptionDispensing; // ✅ ADD THIS
use App\Models\Doctor;
use Illuminate\Support\Facades\DB;

class DoctorPrescriptionController extends Controller
{
    public function store(Request $request)
    {
        $doctor = Doctor::where('user_id', auth()->id())->first();

        if (!$doctor) {
            return redirect()->back()->with('error', 'Doctor profile not found');
        }

        // Validate the request
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'appointment_id' => 'required|exists:appointments,appointment_id',
            'prescribed_date' => 'required|date',
            'notes' => 'nullable|string',
            'medicines' => 'required|array|min:1',
            'medicines.*.medicine_name' => 'required|string|max:255',
            'medicines.*.dosage' => 'required|string|max:255',
            'medicines.*.frequency' => 'required|string|max:255',
        ]);

        try {
            // Use database transaction to ensure data integrity
            DB::beginTransaction();

            // ========================================
            // STEP 1: Create prescription
            // ========================================
            $prescription = Prescription::create([
                'appointment_id' => $validated['appointment_id'],
                'doctor_id' => $doctor->doctor_id,
                'patient_id' => $validated['patient_id'],
                'prescribed_date' => $validated['prescribed_date'],
                'notes' => $validated['notes'],
            ]);

            // ========================================
            // STEP 2: Create prescription items (medicines)
            // ========================================
            foreach ($validated['medicines'] as $medicine) {
                PrescriptionItem::create([
                    'prescription_id' => $prescription->prescription_id,
                    'medicine_name' => $medicine['medicine_name'],
                    'dosage' => $medicine['dosage'],
                    'frequency' => $medicine['frequency'],
                ]);
            }

            // ========================================
            // ✅ STEP 3: CREATE DISPENSING RECORD FOR PHARMACIST
            // This sends the prescription to the pharmacist queue
            // ========================================
            PrescriptionDispensing::create([
                'prescription_id' => $prescription->prescription_id,
                'pharmacist_id' => null, // Will be assigned when pharmacist verifies
                'patient_id' => $validated['patient_id'],
                'verification_status' => 'Pending', // Pharmacist needs to verify
                'allergy_checked' => false,
                'interaction_checked' => false,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Prescription created successfully and sent to pharmacy for verification');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error creating prescription: ' . $e->getMessage())
                ->withInput();
        }
    }
}