<?php

use App\Enums\AttemptType;
use App\Enums\ResultStatus;
use App\Enums\SessionStatus;
use App\Livewire\Admin\Academics\SessionManager;
use App\Models\AcademicSession;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Student;
use App\Models\StudentResult;
use App\Models\StudentSemesterResult;
use App\Models\User;
use App\Services\Academics\AcademicSessionTransitionService;
use App\Services\Academics\CourseRegistrationService;
use App\Services\Examinations\ResultProcessingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('academic session lifecycle: creation creates DRAFT session with automatic semesters', function () {
    $service = app(AcademicSessionTransitionService::class);
    $admin = User::where('email', 'admin@cnims.edu.ng')->first();

    $session = $service->create([
        'name' => '2026/2027',
        'code' => '2026-2027',
        'start_date' => '2026-10-01',
        'end_date' => '2027-09-30',
    ], $admin);

    expect($session)->toBeInstanceOf(AcademicSession::class);
    expect($session->status)->toBe(SessionStatus::DRAFT);
    expect($session->is_current)->toBeFalse();
    expect($session->semesters()->count())->toBe(2);

    $sem1 = $session->semesters()->where('semester', 1)->first();
    $sem2 = $session->semesters()->where('semester', 2)->first();

    expect($sem1->name)->toBe('First Semester');
    expect($sem1->code)->toBe('2026-2027-S1');
    expect($sem1->sequence)->toBe(1);

    expect($sem2->name)->toBe('Second Semester');
    expect($sem2->code)->toBe('2026-2027-S2');
    expect($sem2->sequence)->toBe(2);
});

test('academic session activation enforces single active session rule and provisions course offerings', function () {
    $service = app(AcademicSessionTransitionService::class);
    $admin = User::where('email', 'admin@cnims.edu.ng')->first();

    // Previous active session seeded: 2025/2026
    $prevActive = AcademicSession::where('is_current', true)->first();
    expect($prevActive)->not->toBeNull();

    // Create new session 2026/2027
    $newSession = $service->create([
        'name' => '2026/2027',
        'code' => '2026-2027',
        'start_date' => '2026-10-01',
        'end_date' => '2027-09-30',
    ], $admin);

    // Activate the new session
    $service->activate($newSession, $admin);

    // Assert previous session is automatically closed and no longer current
    expect($prevActive->fresh()->status)->toBe(SessionStatus::CLOSED);
    expect($prevActive->fresh()->is_current)->toBeFalse();

    // Assert new session is active and current
    expect($newSession->fresh()->status)->toBe(SessionStatus::ACTIVE);
    expect($newSession->fresh()->is_current)->toBeTrue();
    expect($newSession->fresh()->activated_by)->toBe($admin->id);
    expect($newSession->fresh()->activated_at)->not->toBeNull();

    // Assert course offerings were automatically provisioned for active courses
    expect($newSession->offerings()->count())->toBeGreaterThan(0);
});

test('session transition: beginResultProcessing locks course registration while enabling grading', function () {
    $service = app(AcademicSessionTransitionService::class);
    $courseRegService = app(CourseRegistrationService::class);
    $admin = User::where('email', 'admin@cnims.edu.ng')->first();

    $session = AcademicSession::where('is_current', true)->first();
    $student = Student::first();

    // In ACTIVE status: registration is permitted (allowsRegistration is true)
    expect($session->allowsRegistration())->toBeTrue();

    // Transition session to RESULT_PROCESSING
    $service->beginResultProcessing($session, $admin);

    expect($session->fresh()->status)->toBe(SessionStatus::RESULT_PROCESSING);
    expect($session->fresh()->allowsRegistration())->toBeFalse();
    expect($session->fresh()->allowsResultProcessing())->toBeTrue();
    expect($session->fresh()->allowsScoreEntry())->toBeTrue();

    // Course registration validation must reject when session is in RESULT_PROCESSING
    $validation = $courseRegService->validateRegistration(
        $student,
        [$student->programme->courses->first()?->id ?? 1],
        $session->fresh(),
        1
    );

    expect($validation['valid'])->toBeFalse();
    expect(implode(' ', $validation['errors']))->toContain('registration is locked for academic session');
});

test('session closure pre-flight checklist blocks closure when unverified or unpublished results exist', function () {
    $sessionService = app(AcademicSessionTransitionService::class);
    $admin = User::where('email', 'admin@cnims.edu.ng')->first();

    $session = AcademicSession::where('is_current', true)->first();
    $student = Student::first();
    $course = Course::first();

    // Create an unverified draft result in this session
    StudentResult::updateOrCreate(
        [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'academic_session_id' => $session->id,
            'semester' => 1,
        ],
        [
            'ca_score' => 20,
            'exam_score' => 40,
            'total_score' => 60,
            'grade' => 'B',
            'grade_point' => 4.0,
            'credit_unit' => 3,
            'status' => ResultStatus::SUBMITTED->value, // Awaiting HOD verification
        ]
    );

    // Validate closing: should be blocked
    $validation = $sessionService->validateClosing($session);
    expect($validation['can_close'])->toBeFalse();
    expect($validation['errors'])->not->toBeEmpty();
    expect(implode(' ', $validation['errors']))->toContain('HOD departmental verification');

    // Attempting to close without force throws exception
    expect(fn () => $sessionService->close($session, $admin, false))
        ->toThrow(Exception::class);
});

test('session closure succeeds when all checks pass and evaluates student progression standing', function () {
    $sessionService = app(AcademicSessionTransitionService::class);
    $admin = User::where('email', 'admin@cnims.edu.ng')->first();

    $session = AcademicSession::where('is_current', true)->first();

    // Ensure all results in this session are published
    StudentResult::where('academic_session_id', $session->id)->update([
        'status' => ResultStatus::PUBLISHED->value,
    ]);

    $validation = $sessionService->validateClosing($session);
    expect($validation['can_close'])->toBeTrue();

    // Close the session
    $closedSession = $sessionService->close($session, $admin);

    expect($closedSession->status)->toBe(SessionStatus::CLOSED);
    expect($closedSession->is_current)->toBeFalse();
    expect($closedSession->closed_by)->toBe($admin->id);
    expect($closedSession->closed_at)->not->toBeNull();

    // Assert student progression standing was determined
    $semesterResults = StudentSemesterResult::where('academic_session_id', $session->id)->get();
    foreach ($semesterResults as $res) {
        expect(['PROMOTED', 'PROMOTED_WITH_CARRYOVER', 'PROBATION', 'REPEAT_LEVEL', 'WITHDRAWN'])
            ->toContain($res->academic_standing);
    }
});

test('retake / carryover design: retaking course in subsequent session preserves historical records and increments attempt_number', function () {
    $sessionService = app(AcademicSessionTransitionService::class);
    $resultService = app(ResultProcessingService::class);
    $admin = User::where('email', 'admin@cnims.edu.ng')->first();
    $lecturer = User::where('email', 'lecturer@cnims.edu.ng')->first();
    $student = Student::first();
    $course = Course::where('code', 'NUR 201')->first();

    // Session 1: 2024/2025
    $session1 = $sessionService->create([
        'name' => '2024/2025',
        'code' => '2024-2025',
        'start_date' => '2024-10-01',
        'end_date' => '2025-09-30',
    ], $admin);
    $sessionService->activate($session1, $admin);

    // Student fails Course in Session 1 (Attempt 1: Score 38 -> F)
    $resultService->saveLecturerScores(
        $course->id,
        $session1->id,
        1,
        [$student->id => ['ca' => 15.0, 'exam' => 23.0]],
        $lecturer
    );

    $attempt1 = StudentResult::where('student_id', $student->id)
        ->where('course_id', $course->id)
        ->where('academic_session_id', $session1->id)
        ->first();

    expect($attempt1)->not->toBeNull();
    expect((float) $attempt1->total_score)->toBe(38.0);
    expect($attempt1->grade)->toBe('F');
    expect($attempt1->attempt_number)->toBe(1);
    expect($attempt1->attempt_type)->toBe(AttemptType::FIRST_ATTEMPT);

    // Session 2: 2026/2027 starts
    $session2 = $sessionService->create([
        'name' => '2026/2027',
        'code' => '2026-2027',
        'start_date' => '2026-10-01',
        'end_date' => '2027-09-30',
    ], $admin);
    $sessionService->activate($session2, $admin);

    // Student retakes Course in Session 2 (Attempt 2: Score 65 -> B)
    $resultService->saveLecturerScores(
        $course->id,
        $session2->id,
        1,
        [$student->id => ['ca' => 25.0, 'exam' => 40.0]],
        $lecturer
    );

    // CRITICAL ARCHITECTURAL CHECK: Attempt 1 must remain completely UNTOUCHED
    $attempt1Reloaded = StudentResult::where('student_id', $student->id)
        ->where('course_id', $course->id)
        ->where('academic_session_id', $session1->id)
        ->first();

    expect((float) $attempt1Reloaded->total_score)->toBe(38.0);
    expect($attempt1Reloaded->grade)->toBe('F');
    expect($attempt1Reloaded->academic_session_id)->toBe($session1->id);

    // Attempt 2 exists in Session 2 as CARRYOVER with attempt_number = 2
    $attempt2 = StudentResult::where('student_id', $student->id)
        ->where('course_id', $course->id)
        ->where('academic_session_id', $session2->id)
        ->first();

    expect($attempt2)->not->toBeNull();
    expect((float) $attempt2->total_score)->toBe(65.0);
    expect($attempt2->grade)->toBe('B');
    expect($attempt2->attempt_number)->toBe(2);
    expect($attempt2->attempt_type)->toBe(AttemptType::CARRYOVER);
});

test('course offering architecture: Course is permanent and CourseOffering is session-bound', function () {
    $session = AcademicSession::where('is_current', true)->first();
    $course = Course::first();

    expect($course)->not->toBeNull();
    expect($course->offerings()->count())->toBeGreaterThan(0);

    $offering = $course->offerings()->where('academic_session_id', $session->id)->first();
    expect($offering)->toBeInstanceOf(CourseOffering::class);
    expect($offering->course_id)->toBe($course->id);
    expect($offering->academic_session_id)->toBe($session->id);
    expect($offering->level)->toBe($course->level);
    expect($offering->department_id)->toBe($course->department_id);
});

test('result correction request and amendment workflow', function () {
    $resultService = app(ResultProcessingService::class);
    $admin = User::where('email', 'admin@cnims.edu.ng')->first();
    $hod = User::where('email', 'hod.nursing@cnims.edu.ng')->first();
    $result = StudentResult::first();

    expect($result)->not->toBeNull();
    expect($hod)->not->toBeNull();

    // Step 1: HOD requests correction
    $resultService->requestCorrection($result->id, 'Verification recheck: script missing 5 marks in Section B.', $hod);
    expect($result->fresh()->status)->toBe(ResultStatus::CORRECTION_REQUESTED);
    expect($result->fresh()->correction_reason)->toContain('Section B');
    expect($result->fresh()->correction_requested_by)->toBe($hod->id);

    // Step 2: Lecturer / Admin amends result
    $amended = $resultService->amendResult($result->id, 28.0, 52.0, 'Recalculated Section B marks +5', $admin);
    expect($amended->fresh()->status)->toBe(ResultStatus::AMENDED);
    expect((float) $amended->fresh()->total_score)->toBe(80.0);
    expect($amended->fresh()->grade)->toBe('A');
    expect((float) $amended->fresh()->grade_point)->toBe(5.0);
});

test('Livewire SessionManager component renders sessions and handles creation', function () {
    $admin = User::where('email', 'admin@cnims.edu.ng')->first();
    $this->actingAs($admin);

    Livewire::test(SessionManager::class)
        ->assertSee('Academic Sessions')
        ->assertSee('2025/2026')
        ->call('openCreateModal')
        ->assertSet('showCreateModal', true)
        ->set('name', '2027/2028')
        ->set('code', '2027-2028')
        ->set('start_date', '2027-10-01')
        ->set('end_date', '2028-09-30')
        ->call('createSession')
        ->assertHasNoErrors()
        ->assertSet('showCreateModal', false);

    expect(AcademicSession::where('name', '2027/2028')->exists())->toBeTrue();
});

test('role-based access control (ACL) for Academic Sessions adheres to ChatGPT specification', function () {
    $admin = User::where('email', 'admin@cnims.edu.ng')->first();
    $registrar = User::where('email', 'registrar@cnims.edu.ng')->first();
    $academicOfficer = User::where('email', 'academic.officer@cnims.edu.ng')->first();
    $hod = User::where('email', 'hod.nursing@cnims.edu.ng')->first();
    $studentUser = User::where('email', 'student@cnims.edu.ng')->first();

    $session = AcademicSession::where('is_current', true)->first();

    // View: Admin, Registrar, Academic Officer, HOD all can view
    expect($admin->can('view', $session))->toBeTrue();
    expect($registrar->can('view', $session))->toBeTrue();
    expect($academicOfficer->can('view', $session))->toBeTrue();
    expect($hod->can('view', $session))->toBeTrue();

    // Create & Activate: Admin, Registrar, Academic Officer can; HOD cannot
    expect($admin->can('create', AcademicSession::class))->toBeTrue();
    expect($registrar->can('create', AcademicSession::class))->toBeTrue();
    expect($academicOfficer->can('create', AcademicSession::class))->toBeTrue();
    expect($hod->can('create', AcademicSession::class))->toBeFalse();

    expect($admin->can('activate', $session))->toBeTrue();
    expect($registrar->can('activate', $session))->toBeTrue();
    expect($academicOfficer->can('activate', $session))->toBeTrue();
    expect($hod->can('activate', $session))->toBeFalse();

    // Student cannot view admin sessions nor activate
    expect($studentUser->can('create', AcademicSession::class))->toBeFalse();
    expect($studentUser->can('activate', $session))->toBeFalse();
});
