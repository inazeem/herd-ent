<?php

namespace Database\Seeders;

use App\Models\Encounter;
use App\Models\MedicalFile;
use App\Models\Patient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MedicalFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('medical_files')->truncate();
        
        // We'll simulate file records without actually creating physical files
        
        // Get all encounters that might have files
        $encounters = Encounter::all();
        
        // Get clinician IDs for who might upload files
        $clinicianIds = Encounter::distinct()->pluck('clinician_id')->toArray();
        
        // Sample file details for ENT
        $fileTypes = [
            'image' => [
                'types' => ['image/jpeg', 'image/png'],
                'extensions' => ['jpg', 'png'],
                'names' => [
                    'ear' => ['Tympanic membrane', 'Ear canal', 'External ear', 'Ear exam'],
                    'nasal' => ['Nasal cavity', 'Nasal endoscopy', 'Septum view', 'Turbinate image'],
                    'throat' => ['Oropharynx', 'Tonsils', 'Larynx', 'Vocal cords'],
                    'other' => ['CT scan', 'MRI image', 'X-ray image']
                ],
            ],
            'document' => [
                'types' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
                'extensions' => ['pdf', 'doc', 'docx'],
                'names' => [
                    'Audiogram result', 'Tympanogram', 'Referral letter', 'Previous medical records',
                    'Lab results', 'Surgical report', 'Discharge instructions', 'Consent form'
                ]
            ],
            'video' => [
                'types' => ['video/mp4', 'video/quicktime'],
                'extensions' => ['mp4', 'mov'],
                'names' => [
                    'Endoscopic procedure', 'Nasal endoscopy', 'Laryngoscopy video', 'Hearing test recording'
                ]
            ]
        ];
        
        // Create 1-3 files for some encounters
        foreach ($encounters as $encounter) {
            // About 70% of encounters have files
            if (rand(1, 10) <= 7) {
                $numFiles = rand(1, 3);
                
                for ($i = 0; $i < $numFiles; $i++) {
                    // Randomly select file category
                    $fileCategory = array_rand($fileTypes);
                    $fileTypeData = $fileTypes[$fileCategory];
                    
                    // Determine file details
                    $fileType = $fileTypeData['types'][array_rand($fileTypeData['types'])];
                    $extension = $fileTypeData['extensions'][array_rand($fileTypeData['extensions'])];
                    
                    // Select an appropriate name based on the exam type
                    $fileName = '';
                    if ($fileCategory === 'image') {
                        if ($encounter->ear_exam_performed) {
                            $names = $fileTypeData['names']['ear'];
                        } elseif ($encounter->nasal_exam_performed) {
                            $names = $fileTypeData['names']['nasal'];
                        } elseif ($encounter->throat_exam_performed) {
                            $names = $fileTypeData['names']['throat'];
                        } else {
                            $names = $fileTypeData['names']['other'];
                        }
                        $fileName = $names[array_rand($names)];
                    } else {
                        $fileName = $fileTypeData['names'][array_rand($fileTypeData['names'])];
                    }
                    
                    // Generate a unique file path (simulated)
                    $uniqueId = uniqid();
                    $originalFilename = $fileName . '.' . $extension;
                    $filePath = "medical_files/{$encounter->patient_id}/{$uniqueId}.{$extension}";
                    $fileSize = rand(50000, 5000000); // 50KB to 5MB
                    
                    // Create the medical file record
                    MedicalFile::create([
                        'patient_id' => $encounter->patient_id,
                        'encounter_id' => $encounter->id,
                        'uploaded_by' => $clinicianIds[array_rand($clinicianIds)],
                        'file_name' => $fileName,
                        'file_path' => $filePath,
                        'file_type' => $fileType,
                        'file_size' => $fileSize,
                        'original_filename' => $originalFilename,
                        'description' => "Medical file related to {$encounter->assessment} - {$fileName}",
                        'file_category' => $fileCategory,
                        'created_at' => $encounter->created_at,
                        'updated_at' => $encounter->updated_at,
                    ]);
                }
            }
        }
        
        // Create a few general patient files not linked to encounters
        $patients = Patient::all();
        foreach ($patients as $patient) {
            // About 50% of patients have standalone files
            if (rand(1, 10) <= 5) {
                $fileCategory = 'document';
                $fileType = 'application/pdf';
                $extension = 'pdf';
                $fileName = 'Patient history form';
                
                // Generate a unique file path (simulated)
                $uniqueId = uniqid();
                $originalFilename = $fileName . '.' . $extension;
                $filePath = "medical_files/{$patient->id}/{$uniqueId}.{$extension}";
                $fileSize = rand(50000, 200000); // 50KB to 200KB
                
                // Create the medical file record
                MedicalFile::create([
                    'patient_id' => $patient->id,
                    'encounter_id' => null,
                    'uploaded_by' => $clinicianIds[array_rand($clinicianIds)],
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_type' => $fileType,
                    'file_size' => $fileSize,
                    'original_filename' => $originalFilename,
                    'description' => "Initial patient history form for {$patient->first_name} {$patient->last_name}",
                    'file_category' => $fileCategory,
                    'created_at' => now()->subMonths(rand(1, 6)),
                    'updated_at' => now()->subMonths(rand(1, 6)),
                ]);
            }
        }
    }
}
