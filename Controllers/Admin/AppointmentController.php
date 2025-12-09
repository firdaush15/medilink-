<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;


class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['doctor.user', 'patient.user']);

        // 🔍 Search by doctor or patient name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('doctor.user', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                })
                    ->orWhereHas('patient.user', function ($q3) use ($search) {
                        $q3->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // 📅 Filter by date
        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        // 🧾 Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 🔄 Sort by date (default = newest → oldest)
        $sortOrder = $request->get('sort', 'desc'); // default: 'desc'
        $query->orderBy('appointment_date', $sortOrder)
            ->orderBy('appointment_time', $sortOrder);

        // 📊 Pagination (10 per page)
        $appointments = $query->paginate(10);

        // ✅ Appointment statistics for dashboard cards
        $today = now()->toDateString();

        $todayCount = Appointment::whereDate('appointment_date', $today)->count();
        $confirmCount = Appointment::where('status', 'confirmed')->count();
        $completedCount = Appointment::where('status', 'completed')->count();
        $cancelledCount = Appointment::where('status', 'cancelled')->count();

        // Optional: trends (difference vs yesterday)
        $yesterday = now()->subDay()->toDateString();
        $yesterdayCount = Appointment::whereDate('appointment_date', $yesterday)->count();
        $todayDiff = $todayCount - $yesterdayCount;

        // Weekly stats
        $completedThisWeek = Appointment::whereBetween('appointment_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('status', 'completed')
            ->count();

        $newCancelled = Appointment::whereBetween('appointment_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('status', 'cancelled')
            ->count();

        // ✅ Return to view
        return view('admin.admin_manageAppointments', compact(
            'appointments',
            'todayCount',
            'todayDiff',
            'confirmCount',
            'completedCount',
            'completedThisWeek',
            'cancelledCount',
            'newCancelled'
        ));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}