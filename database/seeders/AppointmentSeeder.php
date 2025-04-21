<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('appointments')->truncate();
        
        // Get clinician IDs (users with clinician role)
        $clinicianIds = User::role('clinician')->pluck('id')->toArray();
        if (empty($clinicianIds)) {
            $clinicianIds = [1]; // Fallback to first user if no clinicians
        }
        
        // Get staff ID for appointment creator
        $staffId = User::role('frontdesk')->first()?->id ?? 1;
        
        // Get patient IDs
        $patientIds = Patient::pluck('id')->toArray();
        
        // Appointment types for ENT practice
        $appointmentTypes = [
            'Initial Consultation',
            'Follow-up',
            'Hearing Test',
            'Pre-operative',
            'Post-operative',
            'Procedure',
            'Emergency'
        ];
        
        // Create some upcoming appointments (next 14 days)
        $appointments = [];
        
        // Today's appointments
        for ($i = 0; $i < 3; $i++) {
            $startTime = Carbon::now()->startOfDay()->addHours(9)->addMinutes($i * 30);
            $appointments[] = [
                'patient_id' => $patientIds[array_rand($patientIds)],
                'clinician_id' => $clinicianIds[array_rand($clinicianIds)],
                'date' => $startTime->format('Y-m-d'),
                'start_time' => $startTime->format('H:i:s'),
                'end_time' => $startTime->addMinutes(30)->format('H:i:s'),
                'status' => 'scheduled',
                'appointment_type' => $appointmentTypes[array_rand($appointmentTypes)],
                'reason' => 'Patient experiencing ' . ['ear pain', 'sinus pressure', 'hearing loss', 'sore throat', 'nasal congestion'][array_rand([0,1,2,3,4])],
                'created_by' => $staffId,
                'created_at' => Carbon::now()->subDays(rand(1, 5)),
                'updated_at' => Carbon::now(),
            ];
        }
        
        // Future appointments
        for ($day = 1; $day <= 14; $day++) {
            $date = Carbon::now()->addDays($day);
            
            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }
            
            // 3-5 appointments per day
            $dailyAppointments = rand(3, 5);
            
            for ($i = 0; $i < $dailyAppointments; $i++) {
                // Appointments between 9am and 4pm
                $hour = rand(9, 16);
                $minute = rand(0, 1) * 30; // Either 0 or 30 minutes
                
                $startTime = Carbon::create($date->year, $date->month, $date->day, $hour, $minute);
                $endTime = (clone $startTime)->addMinutes(30);
                
                $appointments[] = [
                    'patient_id' => $patientIds[array_rand($patientIds)],
                    'clinician_id' => $clinicianIds[array_rand($clinicianIds)],
                    'date' => $startTime->format('Y-m-d'),
                    'start_time' => $startTime->format('H:i:s'),
                    'end_time' => $endTime->format('H:i:s'),
                    'status' => 'scheduled',
                    'appointment_type' => $appointmentTypes[array_rand($appointmentTypes)],
                    'reason' => 'Patient experiencing ' . ['ear pain', 'sinus pressure', 'hearing loss', 'sore throat', 'nasal congestion'][array_rand([0,1,2,3,4])],
                    'created_by' => $staffId,
                    'created_at' => Carbon::now()->subDays(rand(1, 5)),
                    'updated_at' => Carbon::now(),
                ];
            }
        }
        
        // Past appointments (last 30 days)
        for ($day = 1; $day <= 30; $day++) {
            $date = Carbon::now()->subDays($day);
            
            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }
            
            // 0-3 appointments per past day
            $dailyAppointments = rand(0, 3);
            
            for ($i = 0; $i < $dailyAppointments; $i++) {
                // Appointments between 9am and 4pm
                $hour = rand(9, 16);
                $minute = rand(0, 1) * 30; // Either 0 or 30 minutes
                
                $startTime = Carbon::create($date->year, $date->month, $date->day, $hour, $minute);
                $endTime = (clone $startTime)->addMinutes(30);
                
                // Past appointments are mostly completed
                $status = rand(1, 10) <= 8 ? 'completed' : ['cancelled', 'no-show'][array_rand([0,1])];
                
                $appointments[] = [
                    'patient_id' => $patientIds[array_rand($patientIds)],
                    'clinician_id' => $clinicianIds[array_rand($clinicianIds)],
                    'date' => $startTime->format('Y-m-d'),
                    'start_time' => $startTime->format('H:i:s'),
                    'end_time' => $endTime->format('H:i:s'),
                    'status' => $status,
                    'appointment_type' => $appointmentTypes[array_rand($appointmentTypes)],
                    'reason' => 'Patient experiencing ' . ['ear pain', 'sinus pressure', 'hearing loss', 'sore throat', 'nasal congestion'][array_rand([0,1,2,3,4])],
                    'created_by' => $staffId,
                    'created_at' => Carbon::now()->subDays(rand($day, $day + 5)),
                    'updated_at' => Carbon::now()->subDays($day),
                ];
            }
        }
        
        // Insert appointment data
        foreach ($appointments as $appointmentData) {
            Appointment::create($appointmentData);
        }
    }
}
