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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('application_number')->unique(); // e.g., APP/2026/0012
            $table->string('access_code')->nullable(); // credentials passcode for applicant login
            $table->foreignId('programme_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();

            // Personal Details
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->enum('gender', ['male', 'female', 'other'])->default('female');
            $table->date('date_of_birth');
            $table->string('state_of_origin')->nullable();
            $table->string('lga')->nullable();
            $table->text('address')->nullable();
            $table->string('passport_photo')->nullable();

            // Educational background & 2-Sitting O'Level
            $table->string('secondary_school')->nullable();
            $table->string('secondary_school_sitting_1')->nullable();
            $table->string('secondary_school_sitting_2')->nullable();
            $table->year('graduation_year')->nullable();
            $table->unsignedTinyInteger('o_level_sittings')->default(1); // 1 or 2 sittings
            $table->json('o_level_sitting_1')->nullable(); // [exam_body, exam_year, exam_number, school_name, subjects => [[subject, grade]]]
            $table->json('o_level_sitting_2')->nullable(); // [exam_body, exam_year, exam_number, school_name, subjects => [[subject, grade]]]
            $table->boolean('o_level_verified')->default(false);
            $table->unsignedTinyInteger('o_level_credits_count')->default(0);
            $table->string('previous_qualification')->nullable();

            // JAMB UTME Profile
            $table->string('jamb_reg_number')->nullable();
            $table->unsignedSmallInteger('jamb_score')->nullable(); // e.g. 215
            $table->json('jamb_subjects')->nullable(); // [[subject => 'Use of English', score => 62], ...]

            // Step 4: Application Fee
            $table->boolean('application_fee_paid')->default(false);
            $table->timestamp('application_fee_paid_at')->nullable();

            // Step 5: Entrance Exam Invitation
            $table->boolean('entrance_exam_invited')->default(false);
            $table->dateTime('entrance_exam_date')->nullable();
            $table->string('entrance_exam_venue')->nullable();
            $table->string('entrance_exam_seat_number')->nullable();
            $table->timestamp('entrance_exam_invited_at')->nullable();

            // Step 6: Computer-Based Entrance Scoring
            $table->decimal('entrance_exam_score', 5, 2)->nullable();
            $table->text('entrance_exam_remarks')->nullable();
            $table->timestamp('entrance_exam_scored_at')->nullable();
            $table->foreignId('entrance_exam_scored_by')->nullable()->constrained('users')->nullOnDelete();

            // Step 7: Admission Offer
            $table->timestamp('admission_offered_at')->nullable();
            $table->date('acceptance_deadline')->nullable();
            $table->string('admission_letter_ref')->nullable();

            // Step 8: Acceptance Fee Payment & Matriculation
            $table->boolean('acceptance_fee_paid')->default(false);
            $table->timestamp('acceptance_fee_paid_at')->nullable();
            $table->timestamp('matriculated_at')->nullable();

            // Status & Screening
            $table->string('status')->default('submitted')->index();
            // Statuses: submitted, fee_paid, shortlisted, exam_invited, exam_scored, offered, acceptance_paid, admitted, rejected
            $table->decimal('screening_score', 5, 2)->nullable();
            $table->text('screening_remarks')->nullable();
            $table->foreignId('screened_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('student_number')->unique(); // e.g., CON/2026/00125
            $table->foreignId('programme_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('entry_session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->foreignId('current_level_id')->constrained('levels')->cascadeOnDelete();

            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->enum('gender', ['male', 'female', 'other'])->default('female');
            $table->date('date_of_birth')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('state_of_origin')->nullable();
            $table->string('lga')->nullable();
            $table->string('blood_group', 5)->nullable(); // A+, O+, etc.
            $table->string('genotype', 5)->nullable(); // AA, AS, etc.
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('avatar')->nullable();

            $table->enum('status', ['active', 'graduated', 'suspended', 'withdrawn', 'on_leave'])->default('active');
            $table->date('admitted_at')->nullable();
            $table->date('graduated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('type'); // o_level, birth_cert, admission_letter, medical_fitness, indemnity_form
            $table->string('file_path');
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_documents');
        Schema::dropIfExists('students');
        Schema::dropIfExists('applications');
    }
};
