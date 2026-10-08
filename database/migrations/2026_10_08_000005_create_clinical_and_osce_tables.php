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
        // Clinical Facilities & Wards
        Schema::create('clinical_facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., University Teaching Hospital
            $table->enum('type', ['teaching_hospital', 'specialist_hospital', 'general_hospital', 'phc_center'])->default('teaching_hospital');
            $table->string('address');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('clinical_wards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinical_facility_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., Male Medical Ward, Labour Ward, Paediatric Ward
            $table->string('unit_code')->nullable();
            $table->integer('bed_capacity')->default(30);
            $table->integer('student_capacity')->default(10);
            $table->string('ward_in_charge')->nullable();
            $table->timestamps();
        });

        // Clinical Postings
        Schema::create('clinical_postings', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g., Level 300 Medical-Surgical Posting Batch A
            $table->foreignId('programme_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->integer('level')->default(200);
            $table->foreignId('facility_id')->constrained('clinical_facilities')->cascadeOnDelete();
            $table->foreignId('ward_id')->nullable()->constrained('clinical_wards')->nullOnDelete();
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete(); // Clinical Instructor / Coordinator
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('max_capacity')->default(20);
            $table->text('learning_objectives')->nullable();
            $table->enum('status', ['planned', 'active', 'completed', 'cancelled'])->default('planned');
            $table->timestamps();
        });

        Schema::create('clinical_posting_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinical_posting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->decimal('attendance_rate', 5, 2)->default(100.00); // percentage
            $table->string('performance_rating')->nullable(); // Excellent, Very Good, Satisfactory, Unsatisfactory
            $table->text('supervisor_comments')->nullable();
            $table->timestamps();

            $table->unique(['clinical_posting_id', 'student_id'], 'posting_student_unique');
        });

        // Clinical Procedures Library
        Schema::create('clinical_procedures', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., PRC-001
            $table->string('title'); // e.g., Wound Dressing, Catheterisation, Vital Signs
            $table->string('category')->default('General Nursing'); // e.g. Medical-Surgical, Paediatrics, Midwifery, Critical Care
            $table->text('description')->nullable();
            $table->integer('minimum_required_count')->default(5);
            $table->timestamps();
        });

        // Digital Clinical Logbook
        Schema::create('clinical_logbooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('clinical_posting_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('procedure_id')->constrained('clinical_procedures')->cascadeOnDelete();
            $table->date('procedure_date');
            $table->string('patient_reference_code')->nullable(); // Anonymized, e.g. PT-392
            $table->enum('competency_level', [
                'observed',
                'assisted',
                'supervised',
                'competent',
                'independent',
            ])->default('observed');
            $table->text('student_reflection')->nullable(); // Student's clinical reflection
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('submitted');
            $table->text('supervisor_remarks')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        // Nursing Competency Engine
        Schema::create('competency_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Patient Care, Medication Administration, Emergency & Resuscitation, Infection Control
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('competency_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('competency_categories')->cascadeOnDelete();
            $table->string('name'); // e.g. IV Cannulation, Blood Transfusion, CPR
            $table->string('target_level')->default('competent');
            $table->text('rubric_criteria')->nullable();
            $table->timestamps();
        });

        Schema::create('student_competencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained('competency_skills')->cascadeOnDelete();
            $table->enum('current_level', [
                'not_started',
                'observed',
                'assisted',
                'supervised',
                'competent',
                'independent',
            ])->default('not_started');
            $table->foreignId('assessed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assessed_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'skill_id'], 'student_skill_unique');
        });

        // OSCE (Objective Structured Clinical Examination)
        Schema::create('osce_examinations', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g., 2026 Qualifying Professional OSCE
            $table->foreignId('programme_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->date('exam_date');
            $table->integer('total_stations')->default(6);
            $table->decimal('total_possible_score', 6, 2)->default(100.00);
            $table->enum('status', ['upcoming', 'in_progress', 'completed'])->default('upcoming');
            $table->timestamps();
        });

        Schema::create('osce_stations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('osce_examination_id')->constrained()->cascadeOnDelete();
            $table->integer('station_number'); // Station 1, 2, 3...
            $table->string('title'); // e.g. Wound Care, Patient Assessment, Resuscitation
            $table->text('scenario'); // Scenario description for student & simulated patient
            $table->integer('allocated_time_minutes')->default(10);
            $table->decimal('max_score', 5, 2)->default(20.00);
            $table->foreignId('assessor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('osce_station_rubrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('osce_station_id')->constrained()->cascadeOnDelete();
            $table->string('criterion'); // e.g., Hand Hygiene & PPE, Identifies Patient, Correct Technique
            $table->decimal('max_score', 5, 2)->default(4.00);
            $table->timestamps();
        });

        Schema::create('osce_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('osce_station_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessor_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('score_awarded', 5, 2);
            $table->decimal('max_possible', 5, 2);
            $table->json('rubric_breakdown')->nullable();
            $table->text('examiner_comments')->nullable();
            $table->timestamps();

            $table->unique(['osce_station_id', 'student_id'], 'osce_station_student_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('osce_scores');
        Schema::dropIfExists('osce_station_rubrics');
        Schema::dropIfExists('osce_stations');
        Schema::dropIfExists('osce_examinations');
        Schema::dropIfExists('student_competencies');
        Schema::dropIfExists('competency_skills');
        Schema::dropIfExists('competency_categories');
        Schema::dropIfExists('clinical_logbooks');
        Schema::dropIfExists('clinical_procedures');
        Schema::dropIfExists('clinical_posting_students');
        Schema::dropIfExists('clinical_postings');
        Schema::dropIfExists('clinical_wards');
        Schema::dropIfExists('clinical_facilities');
    }
};
