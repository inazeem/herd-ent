<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('patients')->truncate();
        
        $patients = [
            [
                'patient_id' => 'ENT25-00001',
                'first_name' => 'John',
                'last_name' => 'Smith',
                'date_of_birth' => '1975-06-15',
                'gender' => 'male',
                'email' => 'john.smith@example.com',
                'phone' => '555-123-4567',
                'address' => '123 Main St',
                'city' => 'Springfield',
                'state' => 'IL',
                'postal_code' => '62704',
                'emergency_contact_name' => 'Jane Smith',
                'emergency_contact_phone' => '555-123-4568',
                'medical_history' => 'Previous tonsillectomy, No known allergies',
                'allergies' => 'Penicillin',
                'current_medications' => 'Lisinopril 10mg daily',
                'insurance_provider' => 'Blue Cross Blue Shield',
                'insurance_policy_number' => 'BCBS123456789',
            ],
            [
                'patient_id' => 'ENT25-00002',
                'first_name' => 'Mary',
                'last_name' => 'Johnson',
                'date_of_birth' => '1982-03-24',
                'gender' => 'female',
                'email' => 'mary.johnson@example.com',
                'phone' => '555-234-5678',
                'address' => '456 Elm St',
                'city' => 'Springfield',
                'state' => 'IL',
                'postal_code' => '62701',
                'emergency_contact_name' => 'Robert Johnson',
                'emergency_contact_phone' => '555-234-5679',
                'medical_history' => 'Chronic sinusitis, Asthma',
                'allergies' => 'Dust, Pollen',
                'current_medications' => 'Flonase daily, Albuterol as needed',
                'insurance_provider' => 'Aetna',
                'insurance_policy_number' => 'AET987654321',
            ],
            [
                'patient_id' => 'ENT25-00003',
                'first_name' => 'David',
                'last_name' => 'Williams',
                'date_of_birth' => '1990-11-08',
                'gender' => 'male',
                'email' => 'david.williams@example.com',
                'phone' => '555-345-6789',
                'address' => '789 Oak St',
                'city' => 'Springfield',
                'state' => 'IL',
                'postal_code' => '62702',
                'emergency_contact_name' => 'Sarah Williams',
                'emergency_contact_phone' => '555-345-6780',
                'medical_history' => 'Recurrent ear infections',
                'allergies' => 'None',
                'current_medications' => 'None',
                'insurance_provider' => 'UnitedHealthcare',
                'insurance_policy_number' => 'UHC456789123',
            ],
            [
                'patient_id' => 'ENT25-00004',
                'first_name' => 'Elizabeth',
                'last_name' => 'Brown',
                'date_of_birth' => '1965-07-30',
                'gender' => 'female',
                'email' => 'elizabeth.brown@example.com',
                'phone' => '555-456-7890',
                'address' => '101 Pine St',
                'city' => 'Springfield',
                'state' => 'IL',
                'postal_code' => '62703',
                'emergency_contact_name' => 'Michael Brown',
                'emergency_contact_phone' => '555-456-7891',
                'medical_history' => 'Hearing loss, Hypertension',
                'allergies' => 'Sulfa drugs',
                'current_medications' => 'Hydrochlorothiazide 25mg daily',
                'insurance_provider' => 'Medicare',
                'insurance_policy_number' => 'MCR123456789',
            ],
            [
                'patient_id' => 'ENT25-00005',
                'first_name' => 'Michael',
                'last_name' => 'Davis',
                'date_of_birth' => '1988-09-12',
                'gender' => 'male',
                'email' => 'michael.davis@example.com',
                'phone' => '555-567-8901',
                'address' => '202 Cedar St',
                'city' => 'Springfield',
                'state' => 'IL',
                'postal_code' => '62704',
                'emergency_contact_name' => 'Jennifer Davis',
                'emergency_contact_phone' => '555-567-8902',
                'medical_history' => 'Deviated septum, Sleep apnea',
                'allergies' => 'Latex',
                'current_medications' => 'None',
                'insurance_provider' => 'Cigna',
                'insurance_policy_number' => 'CIG234567890',
            ],
        ];
        
        // Insert patient data
        foreach ($patients as $patientData) {
            Patient::create($patientData);
        }
    }
}
