<?php

namespace Database\Seeders;

use App\Models\BillingCode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BillingCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('billing_codes')->truncate();
        
        $billingCodes = [
            // CPT Codes for ENT
            ['code' => '99201', 'description' => 'Office visit, new patient (Level 1)', 'type' => 'cpt', 'default_price' => 75.00],
            ['code' => '99202', 'description' => 'Office visit, new patient (Level 2)', 'type' => 'cpt', 'default_price' => 110.00],
            ['code' => '99203', 'description' => 'Office visit, new patient (Level 3)', 'type' => 'cpt', 'default_price' => 160.00],
            ['code' => '99204', 'description' => 'Office visit, new patient (Level 4)', 'type' => 'cpt', 'default_price' => 225.00],
            ['code' => '99205', 'description' => 'Office visit, new patient (Level 5)', 'type' => 'cpt', 'default_price' => 275.00],
            
            ['code' => '99211', 'description' => 'Office visit, established patient (Level 1)', 'type' => 'cpt', 'default_price' => 40.00],
            ['code' => '99212', 'description' => 'Office visit, established patient (Level 2)', 'type' => 'cpt', 'default_price' => 75.00],
            ['code' => '99213', 'description' => 'Office visit, established patient (Level 3)', 'type' => 'cpt', 'default_price' => 95.00],
            ['code' => '99214', 'description' => 'Office visit, established patient (Level 4)', 'type' => 'cpt', 'default_price' => 140.00],
            ['code' => '99215', 'description' => 'Office visit, established patient (Level 5)', 'type' => 'cpt', 'default_price' => 190.00],
            
            // ENT Specific Procedures
            ['code' => '69210', 'description' => 'Removal of impacted earwax', 'type' => 'cpt', 'default_price' => 85.00],
            ['code' => '31231', 'description' => 'Nasal endoscopy, diagnostic', 'type' => 'cpt', 'default_price' => 225.00],
            ['code' => '31575', 'description' => 'Laryngoscopy, flexible', 'type' => 'cpt', 'default_price' => 210.00],
            ['code' => '30520', 'description' => 'Septoplasty', 'type' => 'cpt', 'default_price' => 3500.00],
            ['code' => '69433', 'description' => 'Tympanostomy tubes', 'type' => 'cpt', 'default_price' => 1200.00],
            ['code' => '69436', 'description' => 'Tympanostomy tubes, general anesthesia', 'type' => 'cpt', 'default_price' => 1800.00],
            ['code' => '42820', 'description' => 'Tonsillectomy, under age 12', 'type' => 'cpt', 'default_price' => 2400.00],
            ['code' => '42821', 'description' => 'Tonsillectomy, age 12+', 'type' => 'cpt', 'default_price' => 2700.00],
            ['code' => '42826', 'description' => 'Tonsillectomy, primary or secondary; age 12+', 'type' => 'cpt', 'default_price' => 2800.00],
            
            // Diagnostic/ICD-10 codes for ENT
            ['code' => 'H60.3', 'description' => 'Otitis externa', 'type' => 'diagnostic', 'default_price' => null],
            ['code' => 'H66.9', 'description' => 'Otitis media, unspecified', 'type' => 'diagnostic', 'default_price' => null],
            ['code' => 'H61.2', 'description' => 'Impacted cerumen', 'type' => 'diagnostic', 'default_price' => null],
            ['code' => 'J01.90', 'description' => 'Acute sinusitis, unspecified', 'type' => 'diagnostic', 'default_price' => null],
            ['code' => 'J32.9', 'description' => 'Chronic sinusitis, unspecified', 'type' => 'diagnostic', 'default_price' => null],
            ['code' => 'J34.2', 'description' => 'Deviated nasal septum', 'type' => 'diagnostic', 'default_price' => null],
            ['code' => 'J35.1', 'description' => 'Hypertrophy of tonsils', 'type' => 'diagnostic', 'default_price' => null],
            ['code' => 'J35.3', 'description' => 'Hypertrophy of tonsils with hypertrophy of adenoids', 'type' => 'diagnostic', 'default_price' => null],
            ['code' => 'H90.3', 'description' => 'Sensorineural hearing loss, bilateral', 'type' => 'diagnostic', 'default_price' => null],
            ['code' => 'H91.9', 'description' => 'Unspecified hearing loss', 'type' => 'diagnostic', 'default_price' => null],
            
            // Other common codes
            ['code' => '99000', 'description' => 'Specimen handling', 'type' => 'other', 'default_price' => 15.00],
            ['code' => '99024', 'description' => 'Post-op follow-up visit', 'type' => 'other', 'default_price' => 0.00],
        ];
        
        // Insert billing code data
        foreach ($billingCodes as $codeData) {
            BillingCode::create($codeData);
        }
    }
}
