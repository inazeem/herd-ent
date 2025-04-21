<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Encounter extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'appointment_id',
        'clinician_id',
        'encounter_date',
        'subjective',
        'objective',
        'assessment',
        'plan',
        'ear_exam_performed',
        'ear_exam_notes',
        'nasal_exam_performed',
        'nasal_exam_notes',
        'throat_exam_performed',
        'throat_exam_notes',
        'additional_notes',
        'status'
    ];

    protected $casts = [
        'encounter_date' => 'date',
        'ear_exam_performed' => 'boolean',
        'nasal_exam_performed' => 'boolean',
        'throat_exam_performed' => 'boolean',
    ];

    /**
     * Get the patient associated with this encounter.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the appointment associated with this encounter.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the clinician (user) associated with this encounter.
     */
    public function clinician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'clinician_id');
    }

    /**
     * Get all files associated with this encounter.
     */
    public function files(): HasMany
    {
        return $this->hasMany(MedicalFile::class);
    }

    /**
     * Get the invoice associated with this encounter.
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }
    
    /**
     * Check if this encounter has an invoice.
     */
    public function hasInvoice(): bool
    {
        return $this->invoice()->exists();
    }
    
    /**
     * Mark encounter as completed.
     */
    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->save();
        
        if ($this->appointment) {
            $this->appointment->status = 'completed';
            $this->appointment->save();
        }
        
        return $this;
    }
    
    /**
     * Mark encounter as billed.
     */
    public function markAsBilled()
    {
        $this->status = 'billed';
        $this->save();
        
        return $this;
    }
}
