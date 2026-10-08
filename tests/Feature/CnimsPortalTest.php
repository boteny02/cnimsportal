<?php

use App\Livewire\Applicant\ApplicantLogin;
use App\Models\AcademicSession;
use App\Models\Application;
use App\Models\Programme;
use App\Models\Student;
use App\Models\User;
use App\Services\Admissions\ApplicationService;
use App\Services\Examinations\ResultProcessingService;
use App\Services\Finance\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('public pages load successfully', function () {
    $this->get(route('home'))->assertOk();
    $this->get(route('public.programmes'))->assertOk();
    $this->get(route('public.apply'))->assertOk();
    $this->get(route('public.application-status'))->assertOk();
    $this->get(route('public.verify-certificate'))->assertOk();
});

test('demo persona switcher allows switching to staff and student', function () {
    $admin = User::where('email', 'admin@cnims.edu.ng')->first();
    expect($admin)->not->toBeNull();

    $response = $this->post(route('demo.switch-user'), ['email' => 'admin@cnims.edu.ng']);
    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($admin);

    $studentUser = User::where('email', 'student@cnims.edu.ng')->first();
    expect($studentUser)->not->toBeNull();

    $response = $this->post(route('demo.switch-user'), ['email' => 'student@cnims.edu.ng']);
    $response->assertRedirect(route('student.dashboard'));
    $this->assertAuthenticatedAs($studentUser);
});

test('staff portal modules load successfully for authenticated staff', function () {
    $admin = User::where('email', 'admin@cnims.edu.ng')->first();
    $this->actingAs($admin);

    $this->get(route('admin.dashboard'))->assertOk();
    $this->get(route('admin.admissions'))->assertOk();
    $this->get(route('admin.students'))->assertOk();
    $this->get(route('admin.academics'))->assertOk();
    $this->get(route('admin.examinations'))->assertOk();
    $this->get(route('admin.clinical'))->assertOk();
    $this->get(route('admin.clinical.logbook'))->assertOk();
    $this->get(route('admin.osce'))->assertOk();
    $this->get(route('admin.finance'))->assertOk();
    $this->get(route('admin.audit-logs'))->assertOk();
});

test('student portal modules load successfully for authenticated student', function () {
    $studentUser = User::where('email', 'student@cnims.edu.ng')->first();
    $this->actingAs($studentUser);

    $this->get(route('student.dashboard'))->assertOk();
    $this->get(route('student.courses'))->assertOk();
    $this->get(route('student.results'))->assertOk();
    $this->get(route('student.clinical'))->assertOk();
    $this->get(route('student.fees'))->assertOk();
});

test('domain service: admission and matriculation workflow generates student, credentials, and invoice', function () {
    $appService = app(ApplicationService::class);
    $programme = Programme::first();
    $session = AcademicSession::first();

    $app = Application::create([
        'application_number' => $appService->generateApplicationNumber(),
        'programme_id' => $programme->id,
        'academic_session_id' => $session->id,
        'first_name' => 'Chioma',
        'last_name' => 'Nwosu',
        'email' => 'chioma.nwosu.test@example.com',
        'phone' => '08039998877',
        'gender' => 'female',
        'date_of_birth' => '2004-05-15',
        'status' => 'submitted',
        'submitted_at' => now(),
    ]);

    $admin = User::where('email', 'admin@cnims.edu.ng')->first();

    // Screen
    $appService->screen($app, 82.5, 'Candidate met all 5 credits including science', $admin);
    expect($app->fresh()->status)->toBe('shortlisted');

    // Admit and matriculate
    $student = $appService->admit($app, $admin);

    expect($student)->toBeInstanceOf(Student::class);
    expect($student->student_number)->toStartWith('CON/');
    expect($student->user)->not->toBeNull();
    expect($student->user->hasRole('student'))->toBeTrue();
    expect($student->invoices()->count())->toBeGreaterThan(0);
});

test('domain service: result processing computes letter grade and grade points accurately', function () {
    $resultService = app(ResultProcessingService::class);

    $aGrade = $resultService->calculateGradeAndPoints(85.0);
    expect($aGrade['grade'])->toBe('A');
    expect($aGrade['grade_point'])->toBe(5.0);

    $bGrade = $resultService->calculateGradeAndPoints(64.5);
    expect($bGrade['grade'])->toBe('B');
    expect($bGrade['grade_point'])->toBe(4.0);

    $cGrade = $resultService->calculateGradeAndPoints(52.0);
    expect($cGrade['grade'])->toBe('C');
    expect($cGrade['grade_point'])->toBe(3.0);

    $fGrade = $resultService->calculateGradeAndPoints(35.0);
    expect($fGrade['grade'])->toBe('F');
    expect($fGrade['grade_point'])->toBe(0.0);
});

test('applicant login page loads and authenticates via application number credentials', function () {
    $this->get(route('applicant.login'))->assertOk();

    // Verify authentication using Application Number
    Livewire::test(ApplicantLogin::class)
        ->set('identifier', 'APP/2026/00101')
        ->set('password', 'password123')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('applicant.dashboard'));

    $applicantUser = User::where('email', 'applicant@cnims.edu.ng')->first();
    $this->assertAuthenticatedAs($applicantUser);
});

test('demo persona switcher switches to applicant persona and redirects to applicant dashboard', function () {
    $applicant = User::where('email', 'applicant@cnims.edu.ng')->first();
    expect($applicant)->not->toBeNull();

    $response = $this->post(route('demo.switch-user'), ['email' => 'applicant@cnims.edu.ng']);
    $response->assertRedirect(route('applicant.dashboard'));
    $this->assertAuthenticatedAs($applicant);
});

test('applicant dashboard renders all 8 workflow stages for blessing danjuma', function () {
    $applicant = User::where('email', 'applicant@cnims.edu.ng')->first();
    $this->actingAs($applicant);

    $response = $this->get(route('applicant.dashboard'));
    $response->assertOk();
    $response->assertSee('Blessing Chiamaka Danjuma');
    $response->assertSee('APP/2026/00101');
    $response->assertSee('224');
    $response->assertSee('JAMB UTME Score Profile');
    $response->assertSee('5/5 NMCN Credits');
    $response->assertSee('78.5%');
    $response->assertSee('CBT Score');
    $response->assertSee('Provisional Admission Offer');
});

test('domain service: evaluateOLevelCredits handles 2 sittings aggregation correctly', function () {
    $appService = app(ApplicationService::class);

    $sitting1 = [
        'exam_type' => 'WAEC',
        'exam_year' => 2023,
        'subjects' => [
            'English Language' => 'B3',
            'Mathematics' => 'C4',
            'Biology' => 'B2',
            'Chemistry' => 'E8', // Deficiency in sitting 1
            'Physics' => 'C5',
        ],
    ];

    $sitting2 = [
        'exam_type' => 'NECO',
        'exam_year' => 2024,
        'subjects' => [
            'Chemistry' => 'B2', // Cleared deficiency in sitting 2
            'Mathematics' => 'B3',
        ],
    ];

    $evaluation = $appService->evaluateOLevelCredits($sitting1, $sitting2);

    expect($evaluation['verified'])->toBeTrue();
    expect($evaluation['credits_count'])->toBe(5);
    expect($evaluation['details']['Chemistry']['credit'])->toBeTrue();
    expect($evaluation['details']['Chemistry']['grade'])->toBe('B2');
    expect($evaluation['details']['Chemistry']['sitting'])->toBe(2);
});

test('end-to-end applicant lifecycle: CBT invitation, CBT scoring, admission offer, and acceptance payment matriculation', function () {
    $appService = app(ApplicationService::class);
    $admin = User::where('email', 'admin@cnims.edu.ng')->first();
    $programme = Programme::first();
    $session = AcademicSession::first();

    // 1. Submit Application
    $app = Application::create([
        'application_number' => $appService->generateApplicationNumber(),
        'programme_id' => $programme->id,
        'academic_session_id' => $session->id,
        'first_name' => 'Tolu',
        'last_name' => 'Adeyemi',
        'email' => 'tolu.adeyemi.test@example.com',
        'phone' => '+234 803 111 2233',
        'gender' => 'female',
        'date_of_birth' => '2005-02-14',
        'o_level_sittings' => 1,
        'o_level_sitting_1' => [
            'exam_type' => 'WAEC',
            'exam_year' => 2023,
            'subjects' => [
                'English Language' => 'A1',
                'Mathematics' => 'B3',
                'Biology' => 'B2',
                'Chemistry' => 'A1',
                'Physics' => 'B3',
            ],
        ],
        'jamb_reg_number' => '202699887766AA',
        'jamb_score' => 242,
    ]);
    $app = $appService->submit($app);

    expect($app->status)->toBe('submitted');
    expect($app->o_level_verified)->toBeTrue();
    expect($app->invoices()->where('invoice_type', 'application_fee')->count())->toBe(1);

    // 2. Pay Application Fee
    $appInvoice = $app->invoices()->where('invoice_type', 'application_fee')->first();
    app(PaymentService::class)->recordPayment(
        $appInvoice,
        15000.00,
        'card',
        'TST-REF-APP-001',
        $admin,
        'paystack'
    );
    expect($app->fresh()->application_fee_paid)->toBeTrue();

    // 3. Step 5: Invite for Entrance Exam
    $appService->inviteForEntranceExam(
        $app,
        now()->addDays(5)->toDateTimeString(),
        'ICT CBT Center Hall 1',
        'CBT-089',
        $admin
    );
    expect($app->fresh()->entrance_exam_invited)->toBeTrue();
    expect($app->fresh()->entrance_exam_venue)->toBe('ICT CBT Center Hall 1');

    // 4. Step 6: Computer-based Entrance Scoring
    $appService->recordEntranceExamScore(
        $app,
        86.50,
        'Strong clinical aptitude demonstrated.',
        $admin
    );
    expect((float) $app->fresh()->entrance_exam_score)->toBe(86.50);

    // 5. Step 7: Offer Admission (Generates Acceptance Fee Invoice)
    $appService->offerAdmission(
        $app,
        $admin,
        now()->addDays(14)
    );
    expect($app->fresh()->status)->toBe('offered');
    expect($app->fresh()->admission_letter_ref)->not->toBeNull();
    $accInvoice = $app->fresh()->invoices()->where('invoice_type', 'acceptance_fee')->first();
    expect($accInvoice)->not->toBeNull();
    expect($accInvoice->status)->toBe('unpaid');

    // 6. Step 8: Pay Acceptance Fee
    $payment = app(PaymentService::class)->recordPayment(
        $accInvoice,
        35000.00,
        'bank_transfer',
        'TST-REF-ACC-001'
    );

    expect($app->fresh()->acceptance_fee_paid)->toBeTrue();
    expect($app->fresh()->status)->toBe('acceptance_paid');

    // Matriculate into Student
    $student = $appService->admit($app, $admin);

    expect($student)->toBeInstanceOf(Student::class);
    expect($student->student_number)->toStartWith('CON/');
    expect($student->user->hasRole('student'))->toBeTrue();
    expect($app->fresh()->status)->toBe('admitted');
});
