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
        // 1. Enhance academic_sessions
        Schema::table('academic_sessions', function (Blueprint $table) {
            $table->string('code')->nullable()->after('name');
            $table->string('status')->default('draft')->after('end_date'); // draft, active, result_processing, closed, archived
            $table->foreignId('created_by')->nullable()->after('is_current')->constrained('users')->nullOnDelete();
            $table->foreignId('activated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            $table->timestamp('activated_at')->nullable()->after('activated_by');
            $table->foreignId('closed_by')->nullable()->after('activated_at')->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable()->after('closed_by');
            $table->foreignId('archived_by')->nullable()->after('closed_at')->constrained('users')->nullOnDelete();
            $table->timestamp('archived_at')->nullable()->after('archived_by');
        });

        // 2. Enhance academic_semesters (Academic Terms)
        Schema::table('academic_semesters', function (Blueprint $table) {
            $table->string('code')->nullable()->after('name'); // e.g. 2026-2027-S1
            $table->unsignedTinyInteger('sequence')->default(1)->after('code');
            $table->string('status')->default('draft')->after('is_current'); // draft, active, closed
        });

        // 3. Create course_offerings table (Course Offering: Course + Session + Semester)
        Schema::create('course_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->foreignId('academic_semester_id')->constrained('academic_semesters')->cascadeOnDelete();
            $table->integer('level')->default(100);
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->foreignId('lecturer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('capacity')->nullable();
            $table->string('status')->default('open'); // open, closed, cancelled
            $table->timestamps();

            $table->unique(['course_id', 'academic_session_id', 'academic_semester_id'], 'course_session_sem_unique');
        });

        // 4. Create assessment_scores table (Continuous Assessment item scores)
        Schema::create('assessment_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('course_offering_id')->nullable()->constrained('course_offerings')->nullOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->unsignedTinyInteger('semester'); // 1 or 2
            $table->string('assessment_name'); // e.g., 'CA 1', 'CA 2', 'Assignment', 'Exam'
            $table->decimal('score', 5, 2)->default(0);
            $table->decimal('max_score', 5, 2)->default(100);
            $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
        });

        // 5. Enhance student_results
        Schema::table('student_results', function (Blueprint $table) {
            $table->foreignId('course_offering_id')->nullable()->after('course_id')->constrained('course_offerings')->nullOnDelete();
            $table->unsignedTinyInteger('attempt_number')->default(1)->after('semester');
            $table->string('attempt_type')->default('FIRST_ATTEMPT')->after('attempt_number'); // FIRST_ATTEMPT, REPEAT, CARRYOVER, RESIT, SPECIAL_EXAM
            $table->integer('credit_unit')->nullable()->after('grade_point');
            $table->decimal('quality_point', 6, 2)->nullable()->after('credit_unit');
            $table->text('correction_reason')->nullable()->after('published_at');
            $table->foreignId('correction_requested_by')->nullable()->after('correction_reason')->constrained('users')->nullOnDelete();
            $table->timestamp('correction_requested_at')->nullable()->after('correction_requested_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
            $table->dropForeign(['course_offering_id']);
            $table->dropForeign(['correction_requested_by']);
            $table->dropColumn([
                'course_offering_id',
                'attempt_number',
                'attempt_type',
                'credit_unit',
                'quality_point',
                'correction_reason',
                'correction_requested_by',
                'correction_requested_at',
            ]);
        });

        Schema::dropIfExists('assessment_scores');
        Schema::dropIfExists('course_offerings');

        Schema::table('academic_semesters', function (Blueprint $table) {
            $table->dropColumn(['code', 'sequence', 'status']);
        });

        Schema::table('academic_sessions', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['activated_by']);
            $table->dropForeign(['closed_by']);
            $table->dropForeign(['archived_by']);
            $table->dropColumn([
                'code',
                'status',
                'created_by',
                'activated_by',
                'activated_at',
                'closed_by',
                'closed_at',
                'archived_by',
                'archived_at',
            ]);
        });
    }
};
