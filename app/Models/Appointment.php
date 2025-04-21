<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'clinician_id', 'date', 'start_time', 'end_time',
        'status', 'appointment_type', 'reason', 'notes',
        'reminder_sent', 'reminder_sent_at', 'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'reminder_sent' => 'boolean',
        'reminder_sent_at' => 'datetime',
    ];

    /**
     * Get the patient associated with this appointment.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the clinician (user) associated with this appointment.
     */
    public function clinician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'clinician_id');
    }

    /**
     * Get the user who created the appointment.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the encounter associated with this appointment.
     */
    public function encounter(): HasOne
    {
        return $this->hasOne(Encounter::class);
    }

    /**
     * Scope a query to only include upcoming appointments.
     */
    public function scopeUpcoming($query)
    {
        return $query->whereDate('date', '>=', now())
            ->orderBy('date')
            ->orderBy('start_time');
    }

    /**
     * Scope a query to only include today's appointments.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date', now())
            ->orderBy('start_time');
    }

    /**
     * Determine if the appointment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Determine if the appointment is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
    
    /**
     * Get formatted start time
     */
    public function getFormattedStartTimeAttribute(): string
    {
        return date('h:i A', strtotime($this->start_time));
    }
    
    /**
     * Get formatted end time
     */
    public function getFormattedEndTimeAttribute(): string
    {
        return date('h:i A', strtotime($this->end_time));
    }
}
