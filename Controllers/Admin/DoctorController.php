<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\DoctorLeave;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Doctor::with('user');

        // 🔍 Search by doctor name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // 🏥 Filter by specialization
        if ($request->filled('specialization')) {
            $query->where('specialization', $request->specialization);
        }

        // 🧮 Dashboard stats based on filtered query
        $totalDoctors = $query->count();
        $availableDoctors = (clone $query)->where('availability_status', 'Available')->count();
        $unavailableDoctors = (clone $query)->where('availability_status', 'Unavailable')->count();
        $onLeaveDoctors = (clone $query)->where('availability_status', 'On Leave')->count();
        $newDoctorsThisWeek = (clone $query)->where('created_at', '>=', now()->subWeek())->count();

        // ✅ New approved leaves this week
        $filteredDoctorIds = $query->pluck('doctor_id'); // IDs of filtered doctors
        $newLeavesThisWeek = DoctorLeave::whereIn('doctor_id', $filteredDoctorIds)
            ->where('status', 'Approved') // only approved leaves
            ->where('start_date', '>=', now()->startOfWeek())
            ->count();

        // 📊 Paginate results
        $doctors = $query->paginate(10)->withQueryString();

        return view('admin.admin_manageDoctors', compact(
            'doctors',
            'totalDoctors',
            'availableDoctors',
            'unavailableDoctors',
            'onLeaveDoctors',
            'newDoctorsThisWeek',
            'newLeavesThisWeek'
        ));
    }




    // Other methods (create, store, edit, update, destroy) remain unchanged



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
