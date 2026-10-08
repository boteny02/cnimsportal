<?php

namespace App\Services\Academics;

use App\Models\AcademicSession;
use App\Models\Course;
use App\Models\FinancialClearance;
use App\Models\Student;
use App\Models\StudentCourseRegistration;
use App\Models\StudentCourseRegistrationItem;
use App\Models\User;
use App\Services\Audit\AuditService;
use Exception;
use Illuminate\Support\Facades\DB;

class CourseRegistrationService
{
    public function getAvailableCourses(Student $student, AcademicSession $session, int $semester)
    {
        $currentLevelNumeric = $student->currentLevel?->numeric_level ?? 100;

        return Course::where(function ($query) use ($student) {
            $query->where('programme_id', $student->programme_id)
                ->orWhereNull('programme_id');
        })
            ->where('semester', $semester)
            ->where('level', '<=', $currentLevelNumeric)
            ->where('is_active', true)
            ->with('prerequisites')
            ->orderBy('level')
            ->orderBy('code')
            ->get();
    }

    public function validateRegistration(Student $student, array $courseIds, AcademicSession $session, int $semester): array
    {
        $errors = [];

        // 0. Check session lifecycle status
        if (! $session->allowsRegistration()) {
            $errors[] = "Course registration is locked for academic session {$session->name} (Status: ".($session->status?->value ?? 'inactive').').';
        }

        // 1. Check financial clearance
        $clearance = FinancialClearance::where('student_id', $student->id)
            ->where('academic_session_id', $session->id)
            ->where('semester', $semester)
            ->where('clearance_type', 'course_registration')
            ->where('is_cleared', true)
            ->first();

        // If no financial clearance record, check if student has overdue invoices
        $unpaidInvoices = $student->invoices()
            ->where('status', '!=', 'paid')
            ->where('balance', '>', 0)
            ->count();

        if ($unpaidInvoices > 0 && ! $clearance) {
            $errors[] = 'You have pending fee payments. Please clear your outstanding balance or request financial clearance before registering courses.';
        }

        // 2. Fetch courses
        $courses = Course::whereIn('id', $courseIds)->with('prerequisites')->get();
        $totalCredits = $courses->sum('credit_units');

        if ($totalCredits < 12) {
            $errors[] = "Minimum semester credit load is 12 units. Selected load: {$totalCredits} units.";
        }

        if ($totalCredits > 26) {
            $errors[] = "Maximum permissible semester credit load is 26 units. Selected load: {$totalCredits} units.";
        }

        // 3. Check prerequisites
        $passedCourseIds = $student->results()
            ->where('grade_point', '>=', 2.0) // Passed grades
            ->pluck('course_id')
            ->toArray();

        foreach ($courses as $course) {
            foreach ($course->prerequisites as $prereq) {
                if (! in_array($prereq->id, $passedCourseIds)) {
                    $errors[] = "Course {$course->code} requires prerequisite {$prereq->code} ({$prereq->title}) which has not been passed.";
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'total_credits' => $totalCredits,
            'courses' => $courses,
        ];
    }

    public function registerCourses(Student $student, array $courseIds, AcademicSession $session, int $semester): StudentCourseRegistration
    {
        $validation = $this->validateRegistration($student, $courseIds, $session, $semester);
        if (! $validation['valid']) {
            throw new Exception(implode(' ', $validation['errors']));
        }

        return DB::transaction(function () use ($student, $courseIds, $session, $semester, $validation) {
            $registration = StudentCourseRegistration::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'academic_session_id' => $session->id,
                    'semester' => $semester,
                ],
                [
                    'level' => $student->currentLevel?->numeric_level ?? 100,
                    'total_credits' => $validation['total_credits'],
                    'status' => 'submitted',
                ]
            );

            // Sync items
            $registration->items()->delete();
            foreach ($courseIds as $cId) {
                StudentCourseRegistrationItem::create([
                    'registration_id' => $registration->id,
                    'course_id' => $cId,
                    'status' => 'registered',
                ]);
            }

            AuditService::log(
                'academics.courses_registered',
                $registration,
                null,
                ['total_credits' => $validation['total_credits'], 'courses_count' => count($courseIds)],
                "Course registration submitted by Student {$student->student_number} ({$validation['total_credits']} units)",
                $student->user
            );

            return $registration;
        });
    }

    public function approveRegistration(StudentCourseRegistration $registration, User $officer): void
    {
        $old = ['status' => $registration->status];

        $registration->update([
            'status' => 'approved',
            'approved_by' => $officer->id,
            'approved_at' => now(),
        ]);

        $registration->items()->update(['status' => 'approved']);

        AuditService::log(
            'academics.registration_approved',
            $registration,
            $old,
            ['status' => 'approved'],
            "Course registration for {$registration->student->student_number} approved by {$officer->name}",
            $officer
        );
    }
}
