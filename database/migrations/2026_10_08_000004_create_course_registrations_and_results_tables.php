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
        Schema::create('student_course_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('semester'); // 1 or 2
            $table->integer('level')->default(100);
            $table->integer('total_credits')->default(0);
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'academic_session_id', 'semester'], 'student_session_sem_reg_unique');
        });

        Schema::create('student_course_registration_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('student_course_registrations')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['registered', 'dropped', 'approved'])->default('registered');
            $table->timestamps();

            $table->unique(['registration_id', 'course_id'], 'reg_course_unique');
        });

        Schema::create('student_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('semester'); // 1 or 2

            $table->decimal('ca_score', 5, 2)->default(0); // Continuous Assessment (max 30 or 40)
            $table->decimal('exam_score', 5, 2)->default(0); // Exam score (max 70 or 60)
            $table->decimal('total_score', 5, 2)->default(0);
            $table->string('grade', 3)->nullable(); // A, B, C, D, E, F
            $table->decimal('grade_point', 4, 2)->default(0); // 5.0, 4.0, 3.0, etc.
            $table->decimal('credit_points', 6, 2)->default(0); // credit_units * grade_point

            // Workflow status: draft -> submitted_by_lecturer -> verified_by_hod -> processed_by_exam_officer -> approved_by_board -> published
            $table->enum('status', [
                'draft',
                'submitted_by_lecturer',
                'verified_by_hod',
                'processed_by_exam_officer',
                'approved_by_board',
                'published',
            ])->default('draft');

            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'course_id', 'academic_session_id', 'semester'], 'student_course_session_result_unique');
        });

        Schema::create('student_semester_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('semester'); // 1 or 2
            $table->integer('level')->default(100);

            $table->integer('credits_registered')->default(0);
            $table->integer('credits_earned')->default(0);
            $table->decimal('quality_points', 8, 2)->default(0);
            $table->decimal('gpa', 4, 2)->default(0.00);
            $table->decimal('cgpa', 4, 2)->default(0.00);
            $table->string('academic_standing')->default('Good Standing'); // Good Standing, Warning, Probation
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->unique(['student_id', 'academic_session_id', 'semester'], 'student_sem_summary_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_semester_results');
        Schema::dropIfExists('student_results');
        Schema::dropIfExists('student_course_registration_items');
        Schema::dropIfExists('student_course_registrations');
    }
};
