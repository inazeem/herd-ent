<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users and assign roles
        $this->call([
            // First create users and establish roles/permissions
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            
            // Then create core ENT clinic data
            PatientSeeder::class,
            BillingCodeSeeder::class,
            AppointmentSeeder::class,
            EncounterSeeder::class,
            MedicalFileSeeder::class,
            InvoiceSeeder::class,
        ]);
    }
}
