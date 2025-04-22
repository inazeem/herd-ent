<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view appointments')->only(['index', 'show']);
        $this->middleware('permission:create appointments')->only(['create', 'store']);
        $this->middleware('permission:edit appointments')->only(['edit', 'update']);
        $this->middleware('permission:delete appointments')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Appointment::query()
            ->with(['patient', 'clinician'])
            ->when($request->input('search'), function ($query, $search) {
                $query->whereHas('patient', function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('patient_id', 'like', "%{$search}%");
                });
            })
            ->when($request->input('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->input('date_from'), function ($query, $date) {
                $query->whereDate('date', '>=', $date);
            })
            ->when($request->input('date_to'), function ($query, $date) {
                $query->whereDate('date', '<=', $date);
            })
            ->when($request->input('sort_field') && $request->input('sort_direction'), function ($query) use ($request) {
                $query->orderBy($request->input('sort_field'), $request->input('sort_direction'));
            }, function ($query) {
                $query->orderBy('date', 'desc')->orderBy('start_time', 'desc');
            });

        return Inertia::render('Appointments/Index', [
            'appointments' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['search', 'status', 'date_from', 'date_to', 'sort_field', 'sort_direction']),
            'statuses' => [
                'scheduled' => 'Scheduled',
                'confirmed' => 'Confirmed',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
                'no-show' => 'No Show'
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = Patient::orderBy('last_name')->get();
        $clinicians = User::role('clinician')->get();
        
        return Inertia::render('Appointments/Create', [
            'patients' => $patients,
            'clinicians' => $clinicians,
            'appointmentTypes' => [
                'Initial Consultation',
                'Follow-up',
                'Hearing Test',
                'Pre-operative',
                'Post-operative',
                'Procedure',
                'Emergency'
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'clinician_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'appointment_type' => 'required|string|max:255',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        $validated['status'] = 'scheduled';
        $validated['created_by'] = $request->user()->id;
        
        Appointment::create($validated);
        
        return redirect()->route('appointments.index')
            ->with('message', 'Appointment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'clinician', 'encounter']);
        
        return Inertia::render('Appointments/Show', [
            'appointment' => $appointment
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        $patients = Patient::orderBy('last_name')->get();
        $clinicians = User::role('clinician')->get();
        
        return Inertia::render('Appointments/Edit', [
            'appointment' => $appointment,
            'patients' => $patients,
            'clinicians' => $clinicians,
            'appointmentTypes' => [
                'Initial Consultation',
                'Follow-up',
                'Hearing Test',
                'Pre-operative',
                'Post-operative',
                'Procedure',
                'Emergency'
            ],
            'statuses' => [
                'scheduled' => 'Scheduled',
                'confirmed' => 'Confirmed',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
                'no-show' => 'No Show'
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'clinician_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|in:scheduled,confirmed,completed,cancelled,no-show',
            'appointment_type' => 'required|string|max:255',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        $appointment->update($validated);
        
        return redirect()->route('appointments.index')
            ->with('message', 'Appointment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        
        return redirect()->route('appointments.index')
            ->with('message', 'Appointment deleted successfully.');
    }
}