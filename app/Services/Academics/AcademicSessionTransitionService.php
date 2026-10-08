<?php

namespace App\Services\Academics;

use App\Enums\ResultStatus;
use App\Enums\SessionStatus;
use App\Models\AcademicSemester;
use App\Models\AcademicSession;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Student;
use App\Models\StudentResult;
use App\Models\StudentSemesterResult;
use App\Models\User;
use App\Services\Audit\AuditService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AcademicSessionTransitionService
{
    /**
     * Create a new Academic Session in DRAFT status with initial semesters.
     */
    public function create(array $data, ?User $user = null): AcademicSession
    {
        return DB::transaction(function () use ($data, $user) {
            $name = trim($data['name']); // e.g. "2026/2027"
            $code = $data['code'] ?? Str::slug($name);

            $session = AcademicSession::create([
                'name' => $name,
                'code' => $code,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'status' => SessionStatus::DRAFT,
                'is_current' => false,
                'created_by' => $user?->id,
            ]);

            // Seed First and Second Semesters
            AcademicSemester::create([
                'academic_session_id' => $session->id,
                'semester' => 1,
                'name' => 'First Semester',
                'code' => "{$code}-S1",
                'sequence' => 1,
                'start_date' => $data['start_date'],
                'end_date' => date('Y-m-d', strtotime($data['start_date'].' + 5 months')),
                'status' => 'draft',
                'is_current' => false,
            ]);

            AcademicSemester::create([
                'academic_session_id' => $session->id,
                'semester' => 2,
                'name' => 'Second Semester',
                'code' => "{$code}-S2",
                'sequence' => 2,
                'start_date' => date('Y-m-d', strtotime($data['start_date'].' + 6 months')),
                'end_date' => $data['end_date'],
                'status' => 'draft',
                'is_current' => false,
            ]);

            AuditService::log(
                'sessions.created',
                $session,
                null,
                ['name' => $session->name, 'code' => $session->code],
                "Academic Session {$session->name} created in DRAFT state by ".($user?->name ?? 'System'),
                $user
            );

            return $session;
        });
    }

    /**
     * Activate an Academic Session (Enforces single active session rule).
     */
    public function activate(AcademicSession $session, ?User $user = null): AcademicSession
    {
        if ($session->isClosed() || $session->isArchived()) {
            throw new Exception("Cannot activate a {$session->status->value} academic session.");
        }

        return DB::transaction(function () use ($session, $user) {
            // Step 1: Deactivate/Close any currently active session
            $previousActive = AcademicSession::where('id', '!=', $session->id)
                ->where(function ($q) {
                    $q->where('status', SessionStatus::ACTIVE->value)
                        ->orWhere('is_current', true);
                })
                ->get();

            foreach ($previousActive as $prev) {
                $prev->update([
                    'status' => SessionStatus::CLOSED,
                    'is_current' => false,
                    'closed_by' => $user?->id,
                    'closed_at' => now(),
                ]);

                AuditService::log(
                    'sessions.auto_closed_on_activation',
                    $prev,
                    null,
                    ['activated_session' => $session->name],
                    "Academic Session {$prev->name} automatically closed due to activation of {$session->name}",
                    $user
                );
            }

            // Step 2: Activate the requested session
            $session->update([
                'status' => SessionStatus::ACTIVE,
                'is_current' => true,
                'activated_by' => $user?->id,
                'activated_at' => now(),
            ]);

            // Set Semester 1 as current & active
            $sem1 = $session->semesters()->where('semester', 1)->first();
            if ($sem1) {
                $session->semesters()->update(['is_current' => false]);
                $sem1->update(['status' => 'active', 'is_current' => true]);
            }

            // Step 3: Provision Course Offerings for active courses
            $this->provisionCourseOfferings($session, 1);

            AuditService::log(
                'sessions.activated',
                $session,
                null,
                ['name' => $session->name],
                "Academic Session {$session->name} activated by ".($user?->name ?? 'System'),
                $user
            );

            return $session->fresh();
        });
    }

    /**
     * Transition session to RESULT_PROCESSING.
     * Freezes course registration while allowing assessment scoring, verifications, and approvals.
     */
    public function beginResultProcessing(AcademicSession $session, ?User $user = null): AcademicSession
    {
        if ($session->status !== SessionStatus::ACTIVE) {
            throw new Exception("Only ACTIVE academic sessions can transition to RESULT_PROCESSING. Current status: {$session->status->value}.");
        }

        $session->update([
            'status' => SessionStatus::RESULT_PROCESSING,
        ]);

        AuditService::log(
            'sessions.result_processing_started',
            $session,
            null,
            ['name' => $session->name],
            "Academic Session {$session->name} transitioned to RESULT_PROCESSING by ".($user?->name ?? 'System').'. Course registration is now locked.',
            $user
        );

        return $session;
    }

    /**
     * Comprehensive validation check before closing a session.
     */
    public function validateClosing(AcademicSession $session): array
    {
        $errors = [];

        // 1. Pending result corrections
        $pendingCorrections = StudentResult::where('academic_session_id', $session->id)
            ->where('status', ResultStatus::CORRECTION_REQUESTED->value)
            ->count();
        if ($pendingCorrections > 0) {
            $errors[] = "{$pendingCorrections} result correction request(s) remain pending resolution.";
        }

        // 2. Unsubmitted results (results in draft)
        $draftResults = StudentResult::where('academic_session_id', $session->id)
            ->where('status', ResultStatus::DRAFT->value)
            ->count();
        if ($draftResults > 0) {
            $errors[] = "{$draftResults} course result(s) are still in DRAFT status and have not been submitted by lecturers.";
        }

        // 3. Unverified results (submitted but not verified by HOD)
        $unverifiedResults = StudentResult::where('academic_session_id', $session->id)
            ->where('status', ResultStatus::SUBMITTED->value)
            ->count();
        if ($unverifiedResults > 0) {
            $errors[] = "{$unverifiedResults} course result(s) await HOD departmental verification.";
        }

        // 4. Unapproved results (verified or processed but not approved by board)
        $unapprovedResults = StudentResult::where('academic_session_id', $session->id)
            ->whereIn('status', [ResultStatus::VERIFIED->value, ResultStatus::PROCESSED->value])
            ->count();
        if ($unapprovedResults > 0) {
            $errors[] = "{$unapprovedResults} result(s) await final Academic Board approval.";
        }

        // 5. Unpublished results (approved by board but not published)
        $unpublishedResults = StudentResult::where('academic_session_id', $session->id)
            ->where('status', ResultStatus::APPROVED->value)
            ->count();
        if ($unpublishedResults > 0) {
            $errors[] = "{$unpublishedResults} result(s) have been approved by the Board but not yet published to the Student Portal.";
        }

        $totalResults = StudentResult::where('academic_session_id', $session->id)->count();
        $totalOfferings = CourseOffering::where('academic_session_id', $session->id)->count();

        return [
            'can_close' => empty($errors),
            'errors' => $errors,
            'checks' => [
                'total_offerings' => $totalOfferings,
                'total_results' => $totalResults,
                'draft_results' => $draftResults,
                'unverified_results' => $unverifiedResults,
                'unapproved_results' => $unapprovedResults,
                'unpublished_results' => $unpublishedResults,
                'pending_corrections' => $pendingCorrections,
            ],
        ];
    }

    /**
     * Close the Academic Session.
     * Enforces pre-flight checks unless $force override is permitted.
     * Computes student end-of-session academic standing and progression.
     */
    public function close(AcademicSession $session, ?User $user = null, bool $force = false): AcademicSession
    {
        if ($session->isClosed() || $session->isArchived()) {
            throw new Exception("Session {$session->name} is already closed or archived.");
        }

        if (! $force) {
            $validation = $this->validateClosing($session);
            if (! $validation['can_close']) {
                throw new Exception("Cannot close session {$session->name}: ".implode(' ', $validation['errors']));
            }
        }

        return DB::transaction(function () use ($session, $user, $force) {
            // Process Progression for Students with results in this session
            $this->processStudentProgression($session);

            $session->update([
                'status' => SessionStatus::CLOSED,
                'is_current' => false,
                'closed_by' => $user?->id,
                'closed_at' => now(),
            ]);

            // Close all semesters
            $session->semesters()->update([
                'status' => 'closed',
                'is_current' => false,
            ]);

            AuditService::log(
                'sessions.closed',
                $session,
                null,
                ['name' => $session->name, 'forced' => $force],
                "Academic Session {$session->name} officially closed and finalized by ".($user?->name ?? 'System'),
                $user
            );

            return $session->fresh();
        });
    }

    /**
     * Archive the closed session (Historical read-only container).
     */
    public function archive(AcademicSession $session, ?User $user = null): AcademicSession
    {
        if ($session->status !== SessionStatus::CLOSED) {
            throw new Exception("Only CLOSED academic sessions can be archived. Current status: {$session->status->value}.");
        }

        $session->update([
            'status' => SessionStatus::ARCHIVED,
            'archived_by' => $user?->id,
            'archived_at' => now(),
        ]);

        AuditService::log(
            'sessions.archived',
            $session,
            null,
            ['name' => $session->name],
            "Academic Session {$session->name} archived as historical read-only data by ".($user?->name ?? 'System'),
            $user
        );

        return $session;
    }

    /**
     * Auto-provisions course offerings for all active courses in this session and semester.
     */
    public function provisionCourseOfferings(AcademicSession $session, ?int $semester = null): int
    {
        $semesters = $semester
            ? $session->semesters()->where('semester', $semester)->get()
            : $session->semesters()->get();

        $count = 0;
        foreach ($semesters as $sem) {
            $courses = Course::where('is_active', true)
                ->where('semester', $sem->semester)
                ->get();

            foreach ($courses as $c) {
                $offering = CourseOffering::firstOrCreate(
                    [
                        'course_id' => $c->id,
                        'academic_session_id' => $session->id,
                        'academic_semester_id' => $sem->id,
                    ],
                    [
                        'level' => $c->level,
                        'department_id' => $c->department_id,
                        'lecturer_id' => $c->lecturer_id,
                        'status' => 'open',
                    ]
                );
                if ($offering->wasRecentlyCreated) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Evaluates student progression and standing across all completed terms.
     */
    protected function processStudentProgression(AcademicSession $session): void
    {
        $studentIds = StudentResult::where('academic_session_id', $session->id)
            ->pluck('student_id')
            ->unique();

        foreach ($studentIds as $studentId) {
            $student = Student::find($studentId);
            if (! $student) {
                continue;
            }

            // Fetch all past and current results across all sessions
            $allResults = StudentResult::where('student_id', $studentId)
                ->with('course')
                ->get();

            $totalUnits = 0;
            $totalQualityPoints = 0.0;
            $failedCoursesCount = 0;

            foreach ($allResults as $res) {
                $u = $res->course->credit_units ?? ($res->credit_unit ?? 0);
                $totalUnits += $u;
                $totalQualityPoints += ($u * $res->grade_point);
                if ($res->grade_point < 2.0) { // Failed grade (E or F depending on grade threshold)
                    $failedCoursesCount++;
                }
            }

            $cgpa = $totalUnits > 0 ? round($totalQualityPoints / $totalUnits, 2) : 0.00;

            // Determine Nursing Progression Standing
            if ($cgpa >= 2.50 && $failedCoursesCount === 0) {
                $standing = 'PROMOTED';
            } elseif ($cgpa >= 2.00 && $failedCoursesCount > 0) {
                $standing = 'PROMOTED_WITH_CARRYOVER';
            } elseif ($cgpa >= 1.50) {
                $standing = 'PROBATION';
            } elseif ($cgpa >= 1.00) {
                $standing = 'REPEAT_LEVEL';
            } else {
                $standing = 'WITHDRAWN';
            }

            // Update semester result records with final academic standing
            StudentSemesterResult::where('student_id', $studentId)
                ->where('academic_session_id', $session->id)
                ->update([
                    'cgpa' => $cgpa,
                    'academic_standing' => $standing,
                ]);
        }
    }
}
