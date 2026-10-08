<?php

namespace App\Services\Examinations;

use App\Enums\AttemptType;
use App\Enums\ResultStatus;
use App\Models\AcademicSemester;
use App\Models\AcademicSession;
use App\Models\AssessmentScore;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Student;
use App\Models\StudentResult;
use App\Models\StudentSemesterResult;
use App\Models\User;
use App\Services\Audit\AuditService;
use Exception;
use Illuminate\Support\Facades\DB;

class ResultProcessingService
{
    public function calculateGradeAndPoints(float $total): array
    {
        if ($total >= 70.0) {
            return ['grade' => 'A', 'grade_point' => 5.0];
        } elseif ($total >= 60.0) {
            return ['grade' => 'B', 'grade_point' => 4.0];
        } elseif ($total >= 50.0) {
            return ['grade' => 'C', 'grade_point' => 3.0];
        } elseif ($total >= 45.0) {
            return ['grade' => 'D', 'grade_point' => 2.0];
        } elseif ($total >= 40.0) {
            return ['grade' => 'E', 'grade_point' => 1.0];
        } else {
            return ['grade' => 'F', 'grade_point' => 0.0];
        }
    }

    /**
     * Find or provision the CourseOffering corresponding to this course, session, and semester.
     */
    public function getOrCreateCourseOffering(Course $course, AcademicSession $session, int $semester): CourseOffering
    {
        $sem = AcademicSemester::where('academic_session_id', $session->id)
            ->where('semester', $semester)
            ->first();

        if (! $sem) {
            $sem = AcademicSemester::create([
                'academic_session_id' => $session->id,
                'semester' => $semester,
                'name' => $semester === 1 ? 'First Semester' : 'Second Semester',
                'code' => ($session->code ?? 'SES')."-S{$semester}",
                'sequence' => $semester,
                'status' => 'active',
            ]);
        }

        return CourseOffering::firstOrCreate(
            [
                'course_id' => $course->id,
                'academic_session_id' => $session->id,
                'academic_semester_id' => $sem->id,
            ],
            [
                'level' => $course->level,
                'department_id' => $course->department_id,
                'lecturer_id' => $course->lecturer_id,
                'status' => 'open',
            ]
        );
    }

    /**
     * Save lecturer scores for a course in a session/semester.
     * Enforces session lifecycle and attempt tracking (First Attempt vs Carryover/Repeat).
     */
    public function saveLecturerScores(
        int $courseId,
        int $sessionId,
        int $semester,
        array $studentScores,
        User $lecturer
    ): void {
        $session = AcademicSession::findOrFail($sessionId);
        if (! $session->allowsScoreEntry()) {
            throw new Exception("Score entry is closed for academic session {$session->name} (Status: ".($session->status?->value ?? 'unknown').').');
        }

        $course = Course::findOrFail($courseId);

        // Layer 3 (Scope): If lecturer, must be assigned to this course
        if ($lecturer && ! $lecturer->hasRole('super_admin') && $lecturer->hasRole('lecturer')) {
            if ($course->lecturer_id !== $lecturer->id) {
                abort(403, "Access Denied: Lecturer is not assigned to course {$course->code}.");
            }
        }

        DB::transaction(function () use ($course, $session, $semester, $studentScores, $lecturer) {
            $offering = $this->getOrCreateCourseOffering($course, $session, $semester);

            foreach ($studentScores as $studentId => $scores) {
                $ca = (float) ($scores['ca'] ?? 0);
                $exam = (float) ($scores['exam'] ?? 0);
                $total = round($ca + $exam, 2);

                $calc = $this->calculateGradeAndPoints($total);
                $units = $course->credit_units;
                $creditPoints = round($units * $calc['grade_point'], 2);

                // Detect previous attempts in earlier sessions to preserve academic history
                $pastAttempts = StudentResult::where('student_id', $studentId)
                    ->where('course_id', $course->id)
                    ->where('academic_session_id', '!=', $session->id)
                    ->orderBy('created_at', 'asc')
                    ->get();

                $attemptNumber = 1;
                $attemptType = AttemptType::FIRST_ATTEMPT;

                if ($pastAttempts->isNotEmpty()) {
                    $attemptNumber = $pastAttempts->count() + 1;
                    $lastAttempt = $pastAttempts->last();
                    if ($lastAttempt->grade_point < 2.0) {
                        $attemptType = AttemptType::CARRYOVER;
                    } else {
                        $attemptType = AttemptType::REPEAT;
                    }
                }

                // Update or create current session result (never mutating past session results!)
                StudentResult::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'course_id' => $course->id,
                        'academic_session_id' => $session->id,
                        'semester' => $semester,
                    ],
                    [
                        'course_offering_id' => $offering->id,
                        'attempt_number' => $attemptNumber,
                        'attempt_type' => $attemptType,
                        'ca_score' => $ca,
                        'exam_score' => $exam,
                        'total_score' => $total,
                        'grade' => $calc['grade'],
                        'grade_point' => $calc['grade_point'],
                        'credit_unit' => $units,
                        'quality_point' => $creditPoints,
                        'credit_points' => $creditPoints,
                        'status' => ResultStatus::SUBMITTED->value,
                        'submitted_by' => $lecturer->id,
                    ]
                );

                // Record continuous assessment breakdowns in assessment_scores
                AssessmentScore::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'course_id' => $course->id,
                        'academic_session_id' => $session->id,
                        'semester' => $semester,
                        'assessment_name' => 'Continuous Assessment',
                    ],
                    [
                        'course_offering_id' => $offering->id,
                        'score' => $ca,
                        'max_score' => 30.00,
                        'entered_by' => $lecturer->id,
                        'submitted_at' => now(),
                        'status' => 'submitted',
                    ]
                );

                AssessmentScore::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'course_id' => $course->id,
                        'academic_session_id' => $session->id,
                        'semester' => $semester,
                        'assessment_name' => 'Semester Examination',
                    ],
                    [
                        'course_offering_id' => $offering->id,
                        'score' => $exam,
                        'max_score' => 70.00,
                        'entered_by' => $lecturer->id,
                        'submitted_at' => now(),
                        'status' => 'submitted',
                    ]
                );
            }

            AuditService::log(
                'examinations.scores_submitted',
                $course,
                null,
                [
                    'count' => count($studentScores),
                    'course' => $course->code,
                    'session' => $session->name,
                    'semester' => $semester,
                ],
                "Scores for {$course->code} ({$session->name} Sem {$semester}) submitted by Lecturer {$lecturer->name}",
                $lecturer
            );
        });
    }

    public function verifyScores(int $courseId, int $sessionId, int $semester, User $hod): void
    {
        $session = AcademicSession::findOrFail($sessionId);
        if ($session->isClosed() || $session->isArchived()) {
            throw new Exception("Cannot verify results in closed or archived academic session {$session->name}.");
        }

        $course = Course::findOrFail($courseId);

        // Layer 3 (Scope): HOD can only verify scores within their department
        if ($hod && ! $hod->hasRole('super_admin') && $hod->hasRole('hod')) {
            if ($hod->department_id && $course->department_id !== $hod->department_id) {
                abort(403, 'Access Denied: HOD can only verify results for courses within their department.');
            }
        }

        StudentResult::where('course_id', $courseId)
            ->where('academic_session_id', $sessionId)
            ->where('semester', $semester)
            ->update([
                'status' => ResultStatus::VERIFIED->value,
                'verified_by' => $hod->id,
            ]);

        AuditService::log(
            'examinations.verified_by_hod',
            $course,
            null,
            ['course' => $course->code, 'session_id' => $sessionId, 'semester' => $semester],
            "Scores for {$course->code} verified by HOD {$hod->name}",
            $hod
        );
    }

    public function processResults(int $sessionId, int $semester, User $examOfficer): void
    {
        $session = AcademicSession::findOrFail($sessionId);
        if ($session->isClosed() || $session->isArchived()) {
            throw new Exception("Cannot process results in closed or archived academic session {$session->name}.");
        }

        DB::transaction(function () use ($sessionId, $semester, $examOfficer, $session) {
            // Update course results status
            StudentResult::where('academic_session_id', $sessionId)
                ->where('semester', $semester)
                ->whereIn('status', [ResultStatus::SUBMITTED->value, ResultStatus::VERIFIED->value])
                ->update([
                    'status' => ResultStatus::PROCESSED->value,
                ]);

            // Get all students with results in this session and semester
            $studentIds = StudentResult::where('academic_session_id', $sessionId)
                ->where('semester', $semester)
                ->pluck('student_id')
                ->unique();

            foreach ($studentIds as $studentId) {
                $student = Student::find($studentId);
                if (! $student) {
                    continue;
                }

                $semesterResults = StudentResult::where('student_id', $studentId)
                    ->where('academic_session_id', $sessionId)
                    ->where('semester', $semester)
                    ->with('course')
                    ->get();

                $regUnits = 0;
                $earnedUnits = 0;
                $totalQualityPoints = 0.0;
                $failedCount = 0;

                foreach ($semesterResults as $res) {
                    $units = $res->course->credit_units ?? ($res->credit_unit ?? 0);
                    $regUnits += $units;
                    if ($res->grade_point >= 2.0) { // Passed
                        $earnedUnits += $units;
                    } else {
                        $failedCount++;
                    }
                    $totalQualityPoints += ($units * $res->grade_point);
                }

                $gpa = $regUnits > 0 ? round($totalQualityPoints / $regUnits, 2) : 0.00;

                // Cumulative calculations across all completed terms
                $allPastResults = StudentResult::where('student_id', $studentId)
                    ->with('course')
                    ->get();

                $cumUnits = 0;
                $cumEarned = 0;
                $cumQP = 0.0;
                $cumFailed = 0;

                foreach ($allPastResults as $past) {
                    $u = $past->course->credit_units ?? ($past->credit_unit ?? 0);
                    $cumUnits += $u;
                    if ($past->grade_point >= 2.0) {
                        $cumEarned += $u;
                    } else {
                        $cumFailed++;
                    }
                    $cumQP += ($u * $past->grade_point);
                }
                $cgpa = $cumUnits > 0 ? round($cumQP / $cumUnits, 2) : $gpa;

                // Academic standing determination
                if ($cgpa >= 2.50 && $cumFailed === 0) {
                    $standing = 'Good Standing';
                } elseif ($cgpa >= 2.00) {
                    $standing = 'Good Standing (With Carryover)';
                } elseif ($cgpa >= 1.50) {
                    $standing = 'Academic Warning';
                } else {
                    $standing = 'Academic Probation';
                }

                StudentSemesterResult::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'academic_session_id' => $sessionId,
                        'semester' => $semester,
                    ],
                    [
                        'level' => $student->currentLevel?->numeric_level ?? 100,
                        'credits_registered' => $regUnits,
                        'credits_earned' => $earnedUnits,
                        'quality_points' => $totalQualityPoints,
                        'gpa' => $gpa,
                        'cgpa' => $cgpa,
                        'academic_standing' => $standing,
                        'is_published' => false,
                    ]
                );
            }

            AuditService::log(
                'examinations.results_processed',
                $session,
                null,
                ['session_id' => $sessionId, 'semester' => $semester, 'students_count' => $studentIds->count()],
                "Semester {$semester} examination results processed by Exam Officer {$examOfficer->name}",
                $examOfficer
            );
        });
    }

    public function approveResults(int $sessionId, int $semester, User $boardUser): void
    {
        $session = AcademicSession::findOrFail($sessionId);
        if ($session->isClosed() || $session->isArchived()) {
            throw new Exception("Cannot approve results in closed or archived academic session {$session->name}.");
        }

        StudentResult::where('academic_session_id', $sessionId)
            ->where('semester', $semester)
            ->update([
                'status' => ResultStatus::APPROVED->value,
                'approved_by' => $boardUser->id,
            ]);

        AuditService::log(
            'examinations.approved_by_board',
            $session,
            null,
            ['session_id' => $sessionId, 'semester' => $semester],
            "Semester {$semester} results approved by Academic Board ({$boardUser->name})",
            $boardUser
        );
    }

    public function publishResults(int $sessionId, int $semester, User $officer): void
    {
        $session = AcademicSession::findOrFail($sessionId);

        StudentResult::where('academic_session_id', $sessionId)
            ->where('semester', $semester)
            ->update([
                'status' => ResultStatus::PUBLISHED->value,
                'published_at' => now(),
            ]);

        StudentSemesterResult::where('academic_session_id', $sessionId)
            ->where('semester', $semester)
            ->update([
                'is_published' => true,
            ]);

        AuditService::log(
            'examinations.results_published',
            $session,
            null,
            ['session_id' => $sessionId, 'semester' => $semester],
            "Semester {$semester} results published to student portal by {$officer->name}",
            $officer
        );
    }

    /**
     * Request a correction on a result.
     */
    public function requestCorrection(int $resultId, string $reason, User $requestingUser): StudentResult
    {
        $result = StudentResult::findOrFail($resultId);
        $result->update([
            'status' => ResultStatus::CORRECTION_REQUESTED->value,
            'correction_reason' => $reason,
            'correction_requested_by' => $requestingUser->id,
            'correction_requested_at' => now(),
        ]);

        AuditService::log(
            'examinations.correction_requested',
            $result,
            null,
            ['reason' => $reason],
            "Correction requested for {$result->course?->code} (Student: {$result->student?->student_number}) by {$requestingUser->name}",
            $requestingUser
        );

        return $result;
    }

    /**
     * Amend an existing result with justification.
     */
    public function amendResult(
        int $resultId,
        float $newCa,
        float $newExam,
        string $justification,
        User $amendingUser
    ): StudentResult {
        $result = StudentResult::findOrFail($resultId);
        $course = $result->course;
        $total = round($newCa + $newExam, 2);
        $calc = $this->calculateGradeAndPoints($total);
        $units = $course->credit_units ?? 3;
        $qualityPoints = round($units * $calc['grade_point'], 2);

        $result->update([
            'ca_score' => $newCa,
            'exam_score' => $newExam,
            'total_score' => $total,
            'grade' => $calc['grade'],
            'grade_point' => $calc['grade_point'],
            'credit_unit' => $units,
            'quality_point' => $qualityPoints,
            'credit_points' => $qualityPoints,
            'status' => ResultStatus::AMENDED->value,
            'correction_reason' => "Amended: {$justification}",
        ]);

        AuditService::log(
            'examinations.result_amended',
            $result,
            null,
            ['new_total' => $total, 'justification' => $justification],
            "Result for {$course->code} amended by {$amendingUser->name}: {$justification}",
            $amendingUser
        );

        return $result;
    }
}
