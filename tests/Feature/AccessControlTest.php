<?php

use App\Models\AcademicSession;
use App\Models\Application;
use App\Models\ClinicalFacility;
use App\Models\ClinicalLogbook;
use App\Models\ClinicalPosting;
use App\Models\ClinicalProcedure;
use App\Models\ClinicalWard;
use App\Models\Course;
use App\Models\Department;
use App\Models\FinancialClearance;
use App\Models\Programme;
use App\Models\Student;
use App\Models\StudentResult;
use App\Models\User;
use App\Services\Admissions\ApplicationService;
use App\Services\Finance\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('2-sitting O-Level system: evaluates 5 core mandatory subjects and 6th custom subject across 2 different secondary schools', function () {
    $appService = app(ApplicationService::class);
    $programme = Programme::first();
    $session = AcademicSession::first();

    $sitting1 = [
        'exam_type' => 'WAEC',
        'exam_year' => 2023,
        'school_name' => 'Queen Amina College, Kaduna',
        'subjects' => [
            'English Language' => 'B3',
            'Mathematics' => 'C4',
            'Biology' => 'B2',
            'Chemistry' => 'D7', // Deficient in Sitting 1
            'Physics' => 'C5',
            'Civic Education' => 'A1', // 6th custom subject passed
        ],
    ];

    $sitting2 = [
        'exam_type' => 'NECO',
        'exam_year' => 2024,
        'school_name' => 'Government Secondary School, Zaria',
        'subjects' => [
            'English Language' => 'C5',
            'Mathematics' => 'C4',
            'Biology' => 'C5',
            'Chemistry' => 'B3', // Cleared Chemistry deficiency in Sitting 2
            'Physics' => 'C4',
            'Civic Education' => 'B2', // 6th custom subject
        ],
    ];

    // Evaluate credits across the 2 sittings
    $evaluation = $appService->evaluateOLevelCredits($sitting1, $sitting2);

    expect($evaluation['verified'])->toBeTrue();
    expect($evaluation['core_credits_count'])->toBe(5);
    expect($evaluation['credits_count'])->toBe(6);
    expect($evaluation['details']['Chemistry']['credit'])->toBeTrue();
    expect($evaluation['details']['Chemistry']['grade'])->toBe('B3');
    expect($evaluation['details']['Chemistry']['sitting'])->toBe(2);
    expect($evaluation['schools_attended']['sitting_1'])->toBe('Queen Amina College, Kaduna');
    expect($evaluation['schools_attended']['sitting_2'])->toBe('Government Secondary School, Zaria');

    // Create application with both secondary schools and verify model persistence
    $application = Application::create([
        'application_number' => $appService->generateApplicationNumber(),
        'programme_id' => $programme->id,
        'academic_session_id' => $session->id,
        'first_name' => 'Fatima',
        'last_name' => 'Bello',
        'email' => 'fatima.bello.test@example.com',
        'phone' => '+234 812 345 6789',
        'gender' => 'female',
        'date_of_birth' => '2005-08-20',
        'secondary_school' => 'Queen Amina College, Kaduna',
        'secondary_school_sitting_1' => 'Queen Amina College, Kaduna',
        'secondary_school_sitting_2' => 'Government Secondary School, Zaria',
        'graduation_year' => 2024,
        'o_level_sittings' => 2,
        'o_level_sitting_1' => $sitting1,
        'o_level_sitting_2' => $sitting2,
        'o_level_credits_count' => $evaluation['credits_count'],
        'o_level_verified' => $evaluation['verified'],
        'status' => 'draft',
    ]);

    expect($application->secondary_school_sitting_1)->toBe('Queen Amina College, Kaduna');
    expect($application->secondary_school_sitting_2)->toBe('Government Secondary School, Zaria');
    expect($application->o_level_credits_count)->toBe(6);
});

test('layer 1 authorization: super admin bypasses gate checks across all modules', function () {
    $superAdmin = User::where('email', 'admin@cnims.edu.ng')->first();
    expect($superAdmin)->not->toBeNull();
    expect($superAdmin->hasRole('super_admin'))->toBeTrue();

    $course = Course::first();
    $student = Student::first();
    $application = Application::first();

    // Super Admin should be granted any ability via Gate::before
    expect(Gate::forUser($superAdmin)->allows('enterScores', $course))->toBeTrue();
    expect(Gate::forUser($superAdmin)->allows('view', $student))->toBeTrue();
    expect(Gate::forUser($superAdmin)->allows('screen', $application))->toBeTrue();
    expect(Gate::forUser($superAdmin)->allows('clearance.override'))->toBeTrue();
});

test('layer 2 authorization: permissions prevent unauthorized roles from restricted actions', function () {
    $studentUser = User::where('email', 'student@cnims.edu.ng')->first();
    $lecturerUser = User::where('email', 'lecturer@cnims.edu.ng')->first();
    $application = Application::first();

    // Student has no admissions or results management permissions
    expect(Gate::forUser($studentUser)->allows('admissions.screen'))->toBeFalse();
    expect(Gate::forUser($studentUser)->allows('screen', $application))->toBeFalse();
    expect(Gate::forUser($studentUser)->allows('results.enter'))->toBeFalse();
    expect(Gate::forUser($studentUser)->allows('finance.manage'))->toBeFalse();

    // Lecturer cannot manage finance or override clearance
    expect(Gate::forUser($lecturerUser)->allows('clearance.override'))->toBeFalse();
    expect(Gate::forUser($lecturerUser)->allows('finance.manage'))->toBeFalse();
});

test('layer 3 policy scope: lecturer can enter scores for assigned course, but forbidden for other courses', function () {
    $lecturerUser = User::where('email', 'lecturer@cnims.edu.ng')->first();

    // Find course assigned to this lecturer
    $assignedCourse = Course::where('lecturer_id', $lecturerUser->id)->first();
    expect($assignedCourse)->not->toBeNull();

    // Create an unassigned course
    $otherCourse = Course::create([
        'code' => 'NUR-TEST-SCOPE',
        'title' => 'Unassigned Nursing Specialty',
        'department_id' => $assignedCourse->department_id,
        'credit_units' => 3,
        'level_id' => $assignedCourse->level_id,
        'semester' => 1,
        'lecturer_id' => null,
        'status' => 'active',
    ]);

    // Policy check
    expect(Gate::forUser($lecturerUser)->allows('enterScores', $assignedCourse))->toBeTrue();
    expect(Gate::forUser($lecturerUser)->allows('enterScores', $otherCourse))->toBeFalse();
});

test('layer 3 policy scope: HOD can verify results for own department, but forbidden for other departments', function () {
    $hodUser = User::where('email', 'hod.nursing@cnims.edu.ng')->first();
    expect($hodUser)->not->toBeNull();
    $hodDeptId = $hodUser->department_id;
    expect($hodDeptId)->not->toBeNull();

    // Course in HOD department
    $deptCourse = Course::create([
        'code' => 'NUR-HOD-VERIFY',
        'title' => 'Advanced Clinical Decision Making',
        'department_id' => $hodDeptId,
        'credit_units' => 3,
        'level_id' => 1,
        'semester' => 1,
        'status' => 'active',
    ]);

    // Create a different department and course
    $otherDept = Department::create([
        'name' => 'Department of Public Health',
        'code' => 'DPH',
    ]);

    $otherCourse = Course::create([
        'code' => 'DPH101',
        'title' => 'Intro to Community Health',
        'department_id' => $otherDept->id,
        'credit_units' => 2,
        'level_id' => 1,
        'semester' => 1,
        'status' => 'active',
    ]);

    // Create student results for both courses
    $student = Student::first();
    $session = AcademicSession::first();

    $deptResult = StudentResult::create([
        'student_id' => $student->id,
        'course_id' => $deptCourse->id,
        'academic_session_id' => $session->id,
        'semester' => 1,
        'ca_score' => 28.0,
        'exam_score' => 45.0,
        'total_score' => 73.0,
        'grade' => 'A',
        'grade_point' => 5.0,
        'credit_points' => 15.0,
        'status' => 'submitted_by_lecturer',
    ]);

    $otherResult = StudentResult::create([
        'student_id' => $student->id,
        'course_id' => $otherCourse->id,
        'academic_session_id' => $session->id,
        'semester' => 1,
        'ca_score' => 25.0,
        'exam_score' => 40.0,
        'total_score' => 65.0,
        'grade' => 'B',
        'grade_point' => 4.0,
        'credit_points' => 8.0,
        'status' => 'submitted_by_lecturer',
    ]);

    // HOD should be allowed to verify departmental result, but denied for other department
    expect(Gate::forUser($hodUser)->allows('verify', $deptResult))->toBeTrue();
    expect(Gate::forUser($hodUser)->allows('verify', $otherResult))->toBeFalse();
});

test('layer 3 policy scope: clinical instructor can only review logbooks for assigned clinical postings', function () {
    $instructorUser = User::where('email', 'clinical.instructor@cnims.edu.ng')->first();
    $otherInstructor = User::where('email', 'clinical.coordinator@cnims.edu.ng')->first();
    $student = Student::first();
    $facility = ClinicalFacility::first();
    $ward = ClinicalWard::first();
    $procedure = ClinicalProcedure::first();
    $session = AcademicSession::first();

    // Posting supervised by instructor
    $assignedPosting = ClinicalPosting::create([
        'title' => 'Paediatric Clinical Posting',
        'programme_id' => $student->programme_id,
        'academic_session_id' => $session->id,
        'level' => 200,
        'facility_id' => $facility->id,
        'ward_id' => $ward->id,
        'supervisor_id' => $instructorUser->id,
        'start_date' => now()->subWeeks(3),
        'end_date' => now()->addWeeks(3),
        'max_capacity' => 10,
        'status' => 'active',
    ]);

    // Posting supervised by coordinator
    $otherPosting = ClinicalPosting::create([
        'title' => 'ICU Clinical Posting',
        'programme_id' => $student->programme_id,
        'academic_session_id' => $session->id,
        'level' => 200,
        'facility_id' => $facility->id,
        'ward_id' => $ward->id,
        'supervisor_id' => $otherInstructor->id,
        'start_date' => now()->subWeeks(3),
        'end_date' => now()->addWeeks(3),
        'max_capacity' => 10,
        'status' => 'active',
    ]);

    // Submitted logbooks
    $logbookAssigned = ClinicalLogbook::create([
        'clinical_posting_id' => $assignedPosting->id,
        'student_id' => $student->id,
        'procedure_id' => $procedure->id,
        'procedure_date' => now()->subDays(2),
        'patient_reference_code' => 'PT-PAED-01',
        'competency_level' => 'competent',
        'student_reflection' => 'Assisted in establishing peripheral venous access.',
        'supervisor_id' => $instructorUser->id,
        'status' => 'submitted',
    ]);

    $logbookOther = ClinicalLogbook::create([
        'clinical_posting_id' => $otherPosting->id,
        'student_id' => $student->id,
        'procedure_id' => $procedure->id,
        'procedure_date' => now()->subDays(2),
        'patient_reference_code' => 'PT-ICU-02',
        'competency_level' => 'supervised',
        'student_reflection' => 'Observed endotracheal suctioning in ICU.',
        'supervisor_id' => $otherInstructor->id,
        'status' => 'submitted',
    ]);

    // Instructor can review logbook for assigned posting, but forbidden for other posting
    expect(Gate::forUser($instructorUser)->allows('review', $logbookAssigned))->toBeTrue();
    expect(Gate::forUser($instructorUser)->allows('review', $logbookOther))->toBeFalse();
});

test('layer 3 policy scope: student can only view own published results and own profile', function () {
    $student1User = User::where('email', 'student@cnims.edu.ng')->first();
    $student1 = $student1User->student;

    // Create a second student
    $student2User = User::create([
        'name' => 'Amina Sanusi',
        'email' => 'amina.sanusi.test@cnims.edu.ng',
        'password' => bcrypt('password123'),
    ]);
    $student2User->assignRole('student');
    $student2 = Student::create([
        'user_id' => $student2User->id,
        'student_number' => 'CON/2026/00999',
        'programme_id' => $student1->programme_id,
        'department_id' => $student1->department_id,
        'entry_session_id' => $student1->entry_session_id,
        'current_level_id' => $student1->current_level_id,
        'first_name' => 'Amina',
        'last_name' => 'Sanusi',
        'gender' => 'female',
        'date_of_birth' => '2004-03-12',
        'phone' => '08031234567',
        'address' => 'Kaduna',
        'state_of_origin' => 'Kaduna',
        'lga' => 'Zaria',
        'status' => 'active',
        'admitted_at' => now(),
    ]);

    $course = Course::create([
        'code' => 'NUR-STU-SCOPE',
        'title' => 'Nursing Ethics and Jurisprudence',
        'department_id' => $student1->department_id,
        'credit_units' => 2,
        'level_id' => 1,
        'semester' => 1,
        'status' => 'active',
    ]);
    $session = AcademicSession::first();

    // Published results
    $res1 = StudentResult::create([
        'student_id' => $student1->id,
        'course_id' => $course->id,
        'academic_session_id' => $session->id,
        'semester' => 1,
        'ca_score' => 27.0,
        'exam_score' => 48.0,
        'total_score' => 75.0,
        'grade' => 'A',
        'grade_point' => 5.0,
        'credit_points' => 10.0,
        'status' => 'published',
    ]);

    $res2 = StudentResult::create([
        'student_id' => $student2->id,
        'course_id' => $course->id,
        'academic_session_id' => $session->id,
        'semester' => 1,
        'ca_score' => 20.0,
        'exam_score' => 40.0,
        'total_score' => 60.0,
        'grade' => 'B',
        'grade_point' => 4.0,
        'credit_points' => 8.0,
        'status' => 'published',
    ]);

    // Student 1 can view own student record and own published result, but not student 2's
    expect(Gate::forUser($student1User)->allows('view', $student1))->toBeTrue();
    expect(Gate::forUser($student1User)->allows('view', $student2))->toBeFalse();
    expect(Gate::forUser($student1User)->allows('view', $res1))->toBeTrue();
    expect(Gate::forUser($student1User)->allows('view', $res2))->toBeFalse();
});

test('layer 4 workflow state: result cannot be edited once submitted by lecturer', function () {
    $lecturerUser = User::where('email', 'lecturer@cnims.edu.ng')->first();
    $assignedCourse = Course::create([
        'code' => 'NUR-WF-EDIT',
        'title' => 'Pharmacology in Nursing',
        'department_id' => $lecturerUser->department_id,
        'credit_units' => 3,
        'level_id' => 1,
        'semester' => 1,
        'lecturer_id' => $lecturerUser->id,
        'status' => 'active',
    ]);
    $student = Student::first();
    $session = AcademicSession::first();

    // Draft result: editable by assigned lecturer
    $draftResult = StudentResult::create([
        'student_id' => $student->id,
        'course_id' => $assignedCourse->id,
        'academic_session_id' => $session->id,
        'semester' => 1,
        'ca_score' => 20.0,
        'exam_score' => 40.0,
        'total_score' => 60.0,
        'grade' => 'B',
        'grade_point' => 4.0,
        'credit_points' => 12.0,
        'status' => 'draft',
    ]);

    expect(Gate::forUser($lecturerUser)->allows('update', $draftResult))->toBeTrue();

    // Change status to submitted_by_lecturer: locked from lecturer editing
    $draftResult->update(['status' => 'submitted_by_lecturer']);
    expect(Gate::forUser($lecturerUser)->allows('update', $draftResult->fresh()))->toBeFalse();
});

test('layer 4 workflow state: exam officer cannot publish results that have not been approved by board', function () {
    $examOfficer = User::where('email', 'exam.officer@cnims.edu.ng')->first();
    $course = Course::create([
        'code' => 'NUR-WF-PUB',
        'title' => 'Mental Health Nursing',
        'department_id' => 1,
        'credit_units' => 3,
        'level_id' => 1,
        'semester' => 1,
        'status' => 'active',
    ]);
    $student = Student::first();
    $session = AcademicSession::first();

    $result = StudentResult::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'academic_session_id' => $session->id,
        'semester' => 1,
        'ca_score' => 25.0,
        'exam_score' => 45.0,
        'total_score' => 70.0,
        'grade' => 'A',
        'grade_point' => 5.0,
        'credit_points' => 15.0,
        'status' => 'verified_by_hod', // Not yet approved by board
    ]);

    // Cannot publish verified_by_hod result
    expect(Gate::forUser($examOfficer)->allows('publish', $result))->toBeFalse();

    // Transition to approved_by_board
    $result->update(['status' => 'approved_by_board']);

    // Now publishing is permitted
    expect(Gate::forUser($examOfficer)->allows('publish', $result->fresh()))->toBeTrue();
});

test('layer 4 and institutional authority: bursar and registrar can override financial clearance with audit logging, while unauthorized staff are denied', function () {
    $bursarUser = User::where('email', 'bursar@cnims.edu.ng')->first();
    $registrarUser = User::where('email', 'registrar@cnims.edu.ng')->first();
    $lecturerUser = User::where('email', 'lecturer@cnims.edu.ng')->first();
    $student = Student::first();
    $session = AcademicSession::first();

    $clearance = FinancialClearance::create([
        'student_id' => $student->id,
        'academic_session_id' => $session->id,
        'semester' => 1,
        'clearance_type' => 'exam_clearance',
        'is_cleared' => false,
    ]);

    // Bursar and Registrar can override via policy
    expect(Gate::forUser($bursarUser)->allows('override', $clearance))->toBeTrue();
    expect(Gate::forUser($registrarUser)->allows('override', $clearance))->toBeTrue();
    expect(Gate::forUser($lecturerUser)->allows('override', $clearance))->toBeFalse();

    // Lecturer attempting domain service override is denied (403)
    $paymentService = app(PaymentService::class);
    $overrideReason = 'Executive compassionate clearance granted for outstanding lab surcharge pending bursary installment reconciliation.';

    expect(fn () => $paymentService->overrideFinancialClearance(
        $student,
        $session,
        1,
        'exam_clearance',
        $lecturerUser,
        $overrideReason
    ))->toThrow(HttpException::class);

    // Bursar override succeeds
    $updatedClearance = $paymentService->overrideFinancialClearance(
        $student,
        $session,
        1,
        'exam_clearance',
        $bursarUser,
        $overrideReason
    );

    expect($updatedClearance->is_cleared)->toBeTrue();
    expect($updatedClearance->is_overridden)->toBeTrue();
    expect($updatedClearance->overridden_by)->toBe($bursarUser->id);
    expect($updatedClearance->override_reason)->toBe($overrideReason);
    expect($updatedClearance->overridden_at)->not->toBeNull();

    // Verify audit log creation
    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $bursarUser->id,
        'action' => 'finance.clearance_override',
        'model_type' => FinancialClearance::class,
        'model_id' => $updatedClearance->id,
    ]);
});
