<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Encounter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EncounterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('encounters')->truncate();
        
        // Get completed appointments to create encounters for
        $completedAppointments = Appointment::where('status', 'completed')->get();
        
        // Sample SOAP templates for various ENT conditions
        $soapTemplates = [
            'ear_infection' => [
                'subjective' => 'Patient presents with ear pain, described as sharp and constant. Reports onset 3 days ago. Pain rated 6/10. Also experiencing decreased hearing and feeling of fullness in the affected ear. Denies fever or discharge.',
                'objective' => 'Right tympanic membrane erythematous and bulging. Left ear appears normal. No discharge noted. External auditory canal shows mild erythema. Temperature 98.6°F.',
                'assessment' => 'Acute otitis media, right ear.',
                'plan' => 'Prescribed amoxicillin 500mg TID for 10 days. Recommended OTC pain relief as needed. Patient to return if symptoms worsen or do not improve within 48-72 hours. Follow-up appointment in 2 weeks.'
            ],
            'sinusitis' => [
                'subjective' => 'Patient reports facial pain and pressure, worse when bending forward. Nasal congestion and thick yellow-green discharge for past 7 days. Headache and postnasal drip. Failed OTC decongestants. No fever reported.',
                'objective' => 'Nasal mucosa erythematous with purulent discharge noted. Tenderness on palpation of maxillary sinuses bilaterally. Oropharynx with postnasal drainage. No cervical lymphadenopathy.',
                'assessment' => 'Acute bacterial sinusitis',
                'plan' => 'Prescribed amoxicillin-clavulanate 875mg BID for 10 days. Recommended saline nasal rinses and intranasal steroid spray. Advised to increase fluid intake and use humidifier. Follow-up in 2 weeks if symptoms persist.'
            ],
            'tonsillitis' => [
                'subjective' => 'Patient complains of severe sore throat for 2 days, pain rated 8/10. Reports difficulty swallowing, fever, and malaise. Denies cough or rhinorrhea. No known sick contacts.',
                'objective' => 'Tonsils are erythematous, enlarged (3+), and with exudates. Anterior cervical lymphadenopathy present. Temperature 101.2°F. Soft palate and uvula normal in appearance.',
                'assessment' => 'Acute exudative tonsillitis, likely bacterial (strep) in origin.',
                'plan' => 'Rapid strep test performed: positive. Prescribed penicillin V 500mg BID for 10 days. Recommended salt water gargles and adequate hydration. OTC pain relievers as directed. Rest advised. Return if symptoms worsen.'
            ],
            'hearing_loss' => [
                'subjective' => 'Patient reports gradual hearing loss in both ears over the past year. Having difficulty in group conversations and frequently asking others to repeat themselves. No ear pain, dizziness, or tinnitus. Family history of hearing loss (father).',
                'objective' => 'Otoscopy reveals normal tympanic membranes bilaterally. Weber test: no lateralization. Rinne test: AC>BC bilaterally. Audiometry performed: bilateral sensorineural hearing loss, moderate in high frequencies.',
                'assessment' => 'Age-related sensorineural hearing loss (presbycusis)',
                'plan' => 'Discussed hearing test results. Referral to audiology for hearing aid evaluation. Patient information provided regarding communication strategies and assistive listening devices. Follow-up in 6 months.'
            ],
            'nasal_obstruction' => [
                'subjective' => 'Patient complains of chronic nasal obstruction, worse on the left side. Reports breathing through mouth at night and dry mouth in morning. Snores according to partner. Occasional nosebleeds when blowing nose forcefully.',
                'objective' => 'External nose appears normal. Anterior rhinoscopy reveals deviated nasal septum to the left with approx. 70% obstruction. Nasal mucosa pink, no polyps or masses noted. Nasal endoscopy confirms septal deviation without other pathology.',
                'assessment' => 'Deviated nasal septum with nasal airway obstruction',
                'plan' => 'Discussed treatment options including surgical (septoplasty) and non-surgical approaches. Patient interested in surgical correction. Ordered preoperative labs. Scheduled for septoplasty on [future date]. Provided postoperative instructions and risks/benefits discussed.'
            ],
        ];
        
        foreach ($completedAppointments as $appointment) {
            // Select a random condition based on appointment reason
            $condition = array_rand($soapTemplates);
            if (stripos($appointment->reason, 'ear') !== false) {
                $condition = 'ear_infection';
            } elseif (stripos($appointment->reason, 'sinus') !== false) {
                $condition = 'sinusitis';
            } elseif (stripos($appointment->reason, 'throat') !== false) {
                $condition = 'tonsillitis';
            } elseif (stripos($appointment->reason, 'hearing') !== false) {
                $condition = 'hearing_loss';
            } elseif (stripos($appointment->reason, 'nasal') !== false) {
                $condition = 'nasal_obstruction';
            }
            
            $template = $soapTemplates[$condition];
            
            // Determine which ENT exams were performed based on condition
            $ear_exam = in_array($condition, ['ear_infection', 'hearing_loss']);
            $nasal_exam = in_array($condition, ['sinusitis', 'nasal_obstruction']);
            $throat_exam = in_array($condition, ['tonsillitis']);
            
            // Create encounter for completed appointment
            Encounter::create([
                'patient_id' => $appointment->patient_id,
                'appointment_id' => $appointment->id,
                'clinician_id' => $appointment->clinician_id,
                'encounter_date' => $appointment->date,
                'subjective' => $template['subjective'],
                'objective' => $template['objective'],
                'assessment' => $template['assessment'],
                'plan' => $template['plan'],
                'ear_exam_performed' => $ear_exam,
                'ear_exam_notes' => $ear_exam ? 'Examination performed with otoscope. ' . (strpos($template['objective'], 'tympanic membrane') !== false ? $template['objective'] : '') : null,
                'nasal_exam_performed' => $nasal_exam,
                'nasal_exam_notes' => $nasal_exam ? 'Nasal examination performed. ' . (strpos($template['objective'], 'nasal') !== false ? $template['objective'] : '') : null,
                'throat_exam_performed' => $throat_exam,
                'throat_exam_notes' => $throat_exam ? 'Throat examination performed. ' . (strpos($template['objective'], 'tonsil') !== false ? $template['objective'] : '') : null,
                'additional_notes' => rand(0, 1) ? 'Patient advised on follow-up care and warning signs.' : null,
                'status' => rand(0, 2) == 0 ? 'billed' : 'completed',
                'created_at' => $appointment->updated_at,
                'updated_at' => $appointment->updated_at,
            ]);
        }
    }
}
