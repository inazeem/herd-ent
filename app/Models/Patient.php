<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'first_name', 'last_name', 'date_of_birth',
        'gender', 'email', 'phone', 'address', 'city', 'state',
        'postal_code', 'emergency_contact_name', 'emergency_contact_phone',
        'medical_history', 'allergies', 'current_medications',
        'insurance_provider', 'insurance_policy_number', 'qr_code'
    ];

    /**
     * Get the appointments associated with this patient.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the medical encounters associated with this patient.
     */
    public function encounters(): HasMany
    {
        return $this->hasMany(Encounter::class);
    }

    /**
     * Get the medical files associated with this patient.
     */
    public function files(): HasMany
    {
        return $this->hasMany(MedicalFile::class);
    }

    /**
     * Get the invoices associated with this patient.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the full name of the patient.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the age of the patient.
     */
    public function getAgeAttribute(): int
    {
        return date_diff(date_create($this->date_of_birth), date_create())->y;
    }
    
    /**
     * Generate a unique patient ID.
     */
    public static function generatePatientId(): string
    {
        $prefix = 'ENT';
        $year = date('y');
        $lastPatient = self::latest()->first();
        
        if ($lastPatient) {
            $lastId = (int)substr($lastPatient->patient_id, -5);
            $newId = $lastId + 1;
        } else {
            $newId = 1;
        }
        
        return $prefix . $year . str_pad($newId, 5, '0', STR_PAD_LEFT);
    }
}
