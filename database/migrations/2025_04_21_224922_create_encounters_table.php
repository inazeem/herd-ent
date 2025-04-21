<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('encounters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('clinician_id')->constrained('users');
            $table->date('encounter_date');
            
            // SOAP Note Structure
            $table->text('subjective')->nullable(); // Patient's complaints and history
            $table->text('objective')->nullable(); // Physical examination findings
            $table->text('assessment')->nullable(); // Diagnosis
            $table->text('plan')->nullable(); // Treatment plan

            // ENT-specific examination fields
            $table->boolean('ear_exam_performed')->default(false);
            $table->text('ear_exam_notes')->nullable();
            
            $table->boolean('nasal_exam_performed')->default(false);
            $table->text('nasal_exam_notes')->nullable();
            
            $table->boolean('throat_exam_performed')->default(false);
            $table->text('throat_exam_notes')->nullable();

            // Additional fields
            $table->text('additional_notes')->nullable();
            $table->enum('status', ['draft', 'completed', 'billed'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encounters');
    }
};
