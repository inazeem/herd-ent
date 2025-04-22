<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EncounterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view encounters')->only(['index', 'show']);
        $this->middleware('permission:create encounters')->only(['create', 'store']);
        $this->middleware('permission:edit encounters')->only(['edit', 'update']);
        $this->middleware('permission:delete encounters')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Encounter::query()
            ->with(['patient', 'clinician', 'appointment'])
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
                $query->whereDate('created_at', '>=', $date);
            })
            ->when($request->input('date_to'), function ($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            })
            ->when($request->input('clinician'), function ($query, $clinicianId) {
                $query->where('clinician_id', $clinicianId);
            })
            ->when($request->input('sort_field') && $request->input('sort_direction'), function ($query) use ($request) {
                $query->orderBy($request->input('sort_field'), $request->input('sort_direction'));
            }, function ($query) {
                $query->latest('created_at');
            });

        return Inertia::render('Encounters/Index', [
            'encounters' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['search', 'status', 'date_from', 'date_to', 'clinician', 'sort_field', 'sort_direction']),
            'statuses' => [
                'in-progress' => 'In Progress',
                'completed' => 'Completed',
                'billed' => 'Billed',
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $appointment = null;
        $patient = null;

        // Check if we're creating from an appointment
        if ($request->has('appointment_id')) {
            $appointment = Appointment::with('patient')->find($request->appointment_id);
            if ($appointment) {
                $patient = $appointment->patient;
            }
        } 
        // Check if we're creating directly for a patient
        elseif ($request->has('patient_id')) {
            $patient = Patient::find($request->patient_id);
        }

        return Inertia::render('Encounters/Create', [
            'appointment' => $appointment,
            'patient' => $patient,
            'patients' => Patient::orderBy('last_name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'chief_complaint' => 'required|string',
            'subjective' => 'required|string',
            'objective' => 'required|string',
            'assessment' => 'required|string',
            'plan' => 'required|string',
            'ear_exam_performed' => 'boolean',
            'ear_exam_notes' => 'nullable|string',
            'nasal_exam_performed' => 'boolean',
            'nasal_exam_notes' => 'nullable|string',
            'throat_exam_performed' => 'boolean',
            'throat_exam_notes' => 'nullable|string',
            'additional_notes' => 'nullable|string',
        ]);
        
        // Set the clinician to the current user
        $validated['clinician_id'] = $request->user()->id;
        $validated['status'] = 'in-progress';
        
        // Create the encounter
        $encounter = Encounter::create($validated);
        
        // Update appointment status if this encounter came from an appointment
        if ($request->has('appointment_id')) {
            $appointment = Appointment::find($request->appointment_id);
            if ($appointment) {
                $appointment->update(['status' => 'completed']);
            }
        }
        
        return redirect()->route('encounters.show', $encounter)
            ->with('message', 'Encounter created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Encounter $encounter)
    {
        $encounter->load(['patient', 'clinician', 'appointment', 'files']);
        
        return Inertia::render('Encounters/Show', [
            'encounter' => $encounter
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Encounter $encounter)
    {
        $encounter->load(['patient', 'clinician', 'appointment']);
        
        return Inertia::render('Encounters/Edit', [
            'encounter' => $encounter,
            'patients' => Patient::orderBy('last_name')->get(),
            'statuses' => [
                'in-progress' => 'In Progress',
                'completed' => 'Completed',
                'billed' => 'Billed',
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Encounter $encounter)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'chief_complaint' => 'required|string',
            'subjective' => 'required|string',
            'objective' => 'required|string',
            'assessment' => 'required|string',
            'plan' => 'required|string',
            'ear_exam_performed' => 'boolean',
            'ear_exam_notes' => 'nullable|string',
            'nasal_exam_performed' => 'boolean',
            'nasal_exam_notes' => 'nullable|string',
            'throat_exam_performed' => 'boolean',
            'throat_exam_notes' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'status' => 'required|in:in-progress,completed,billed',
        ]);
        
        $encounter->update($validated);
        
        return redirect()->route('encounters.show', $encounter)
            ->with('message', 'Encounter updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Encounter $encounter)
    {
        $encounter->delete();
        
        return redirect()->route('encounters.index')
            ->with('message', 'Encounter deleted successfully.');
    }
}