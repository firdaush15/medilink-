<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReceptionistDoctorAvailabilityController extends Controller
{
    public function index(Request $request)
    {
        // Get the selected date or default to today
        $selectedDate = $request->get('date', today()->format('Y-m-d'));

        // Get all doctors with their relationships
        $doctors = Doctor::with(['user', 'appointments', 'leaves'])
            ->get();

        // Calculate statistics
        $availableDoctors = $doctors->where('availability_status', 'Available')->count();
        $onLeaveDoctors = $doctors->where('availability_status', 'On Leave')->count();
        $busyDoctors = $doctors->where('availability_status', 'Unavailable')->count();
        $totalDoctors = $doctors->count();

        return view('receptionist.receptionist_doctorAvailability', compact(
            'doctors',
            'availableDoctors',
            'onLeaveDoctors',
            'busyDoctors',
            'totalDoctors',
            'selectedDate'
        ));
    }
}