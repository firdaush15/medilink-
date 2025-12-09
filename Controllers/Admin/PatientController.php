<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Patient::with('user');

        // Search filter by name
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $patients = $query->paginate(10);

        return view('admin.admin_managePatients', compact('patients'));
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
    public function show($id)
    {
        $patient = \App\Models\Patient::with('user')->findOrFail($id);

        // Get all medical records related to the patient
        $medicalRecords = \App\Models\MedicalRecord::with(['doctor.user'])
            ->where('patient_id', $id)
            ->get();

        // Get all prescriptions (and their items)
        $prescriptions = \App\Models\Prescription::with(['doctor.user', 'items'])
            ->where('patient_id', $id)
            ->get();

        return view('admin.admin_patientDetails', compact('patient', 'medicalRecords', 'prescriptions'));
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
