<?php

namespace App\Services\Admissions;

use App\Models\Application;
use App\Models\Level;
use App\Models\Student;
use App\Models\User;
use App\Services\Audit\AuditService;
use App\Services\Finance\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApplicationService
{
    public const CORE_SCIENCE_SUBJECTS = [
        'English Language',
        'Mathematics',
        'Biology',
        'Chemistry',
        'Physics',
    ];

    public const CREDIT_GRADES = ['A1', 'B2', 'B3', 'C4', 'C5', 'C6'];

    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function generateApplicationNumber(): string
    {
        $year = date('Y');
        $random = strtoupper(Str::random(5));
        $count = Application::whereYear('created_at', $year)->count() + 1;

        return sprintf('APP/%s/%04d-%s', $year, $count, $random);
    }

    public function generateStudentNumber(int $year): string
    {
        $count = Student::whereYear('created_at', $year)->count() + 1;

        return sprintf('CON/%d/%05d', $year, $count);
    }

    /**
     * Evaluate 1 or 2 sittings O'Level subject credits according to NMCN standards.
     * Core 5 required: English Language, Mathematics, Biology, Chemistry, Physics.
     */
    public function evaluateOLevelCredits(array $sitting1, ?array $sitting2 = null): array
    {
        $gradeValues = [
            'A1' => 6, 'B2' => 5, 'B3' => 4,
            'C4' => 3, 'C5' => 2, 'C6' => 1,
            'D7' => 0, 'E8' => 0, 'F9' => 0,
        ];

        $subjectMap = [];

        // Parse sitting 1
        $s1Subjects = $sitting1['subjects'] ?? $sitting1;
        foreach ($s1Subjects as $key => $item) {
            $name = is_string($key) && ! is_numeric($key) ? trim($key) : trim($item['subject'] ?? '');
            $grade = is_string($item) ? strtoupper(trim($item)) : strtoupper(trim($item['grade'] ?? 'F9'));
            if ($name && isset($gradeValues[$grade])) {
                $subjectMap[$name] = [
                    'grade' => $grade,
                    'value' => $gradeValues[$grade],
                    'sitting' => 1,
                    'credit' => in_array($grade, self::CREDIT_GRADES),
                ];
            }
        }

        // Parse sitting 2 (if present)
        if (! empty($sitting2)) {
            $s2Subjects = $sitting2['subjects'] ?? $sitting2;
            foreach ($s2Subjects as $key => $item) {
                $name = is_string($key) && ! is_numeric($key) ? trim($key) : trim($item['subject'] ?? '');
                $grade = is_string($item) ? strtoupper(trim($item)) : strtoupper(trim($item['grade'] ?? 'F9'));
                if ($name && isset($gradeValues[$grade])) {
                    if (! isset($subjectMap[$name]) || $gradeValues[$grade] > $subjectMap[$name]['value']) {
                        $subjectMap[$name] = [
                            'grade' => $grade,
                            'value' => $gradeValues[$grade],
                            'sitting' => 2,
                            'credit' => in_array($grade, self::CREDIT_GRADES),
                        ];
                    }
                }
            }
        }

        $corePassed = [];
        $coreMissing = [];
        $applicantSupplied = [];
        $totalCredits = 0;

        foreach (self::CORE_SCIENCE_SUBJECTS as $core) {
            $match = null;
            foreach ($subjectMap as $subName => $data) {
                if (strcasecmp($subName, $core) === 0 || str_contains(strtolower($subName), strtolower($core))) {
                    $match = $data;
                    break;
                }
            }

            if ($match && in_array($match['grade'], self::CREDIT_GRADES)) {
                $corePassed[$core] = $match;
                $totalCredits++;
            } else {
                $coreMissing[] = $core;
            }
        }

        // Evaluate applicant-supplied non-core subjects (6th subject etc.)
        foreach ($subjectMap as $subName => $data) {
            $isCore = false;
            foreach (self::CORE_SCIENCE_SUBJECTS as $core) {
                if (strcasecmp($subName, $core) === 0 || str_contains(strtolower($subName), strtolower($core))) {
                    $isCore = true;
                    break;
                }
            }

            if (! $isCore) {
                $applicantSupplied[$subName] = $data;
                if ($data['credit']) {
                    $totalCredits++;
                }
            }
        }

        $isQualified = (count($corePassed) === 5);

        return [
            'credits_count' => $totalCredits,
            'core_credits_count' => count($corePassed),
            'is_qualified' => $isQualified,
            'verified' => $isQualified,
            'core_passed' => $corePassed,
            'core_missing' => $coreMissing,
            'applicant_supplied_subjects' => $applicantSupplied,
            'details' => $subjectMap,
            'schools_attended' => [
                'sitting_1' => $sitting1['school_name'] ?? ($sitting1['secondary_school'] ?? null),
                'sitting_2' => $sitting2['school_name'] ?? ($sitting2['secondary_school'] ?? null),
            ],
            'sittings_count' => ! empty($sitting2) ? 2 : 1,
        ];
    }

    /**
     * Step 1: Submit Application & trigger Step 4: Application Fee invoice generation
     */
    public function submit(Application $application): Application
    {
        $old = ['status' => $application->status];

        $updateData = [
            'status' => 'submitted',
            'submitted_at' => now(),
        ];

        if (! empty($application->o_level_sitting_1)) {
            $eval = $this->evaluateOLevelCredits(
                $application->o_level_sitting_1,
                $application->o_level_sitting_2
            );
            $updateData['o_level_verified'] = $eval['is_qualified'];
            $updateData['o_level_credits_count'] = $eval['credits_count'];
        }

        $application->update($updateData);

        // Auto-generate Step 4 Application Fee Invoice
        $this->paymentService->generateInvoiceForApplication($application, 'application_fee');

        AuditService::log(
            'application.submitted',
            $application,
            $old,
            ['status' => 'submitted', 'application_fee_invoice_generated' => true],
            "Applicant {$application->full_name} submitted application {$application->application_number}.",
            auth()->user() ?? $application->user
        );

        return $application->fresh();
    }

    /**
     * Screening O'Level and JAMB
     */
    public function screen(Application $application, float $score, ?string $remarks, User $screener): void
    {
        $old = [
            'status' => $application->status,
            'screening_score' => $application->screening_score,
            'screening_remarks' => $application->screening_remarks,
        ];

        $newStatus = $score >= 50.0 ? 'shortlisted' : 'under_review';

        $application->update([
            'status' => $newStatus,
            'screening_score' => $score,
            'screening_remarks' => $remarks,
            'screened_by' => $screener->id,
        ]);

        AuditService::log(
            'admissions.screened',
            $application,
            $old,
            [
                'status' => $newStatus,
                'screening_score' => $score,
                'screening_remarks' => $remarks,
            ],
            "Application {$application->application_number} screened by {$screener->name} with score {$score}",
            $screener
        );
    }

    /**
     * Step 5: Invitation for Entrance Exam by Admission Officer
     */
    public function inviteForEntranceExam(
        Application $application,
        \DateTimeInterface|string $examDate,
        string $venue,
        string $seatNumber,
        User $officer
    ): void {
        $old = ['status' => $application->status];

        $application->update([
            'entrance_exam_invited' => true,
            'entrance_exam_date' => $examDate,
            'entrance_exam_venue' => $venue,
            'entrance_exam_seat_number' => $seatNumber,
            'entrance_exam_invited_at' => now(),
            'status' => 'exam_invited',
        ]);

        AuditService::log(
            'admissions.exam_invited',
            $application,
            $old,
            [
                'status' => 'exam_invited',
                'exam_date' => $examDate,
                'venue' => $venue,
                'seat_number' => $seatNumber,
            ],
            "Candidate {$application->application_number} invited for CBT Entrance Exam on ".(is_string($examDate) ? $examDate : $examDate->format('Y-m-d H:i'))." at {$venue} (Seat: {$seatNumber}) by {$officer->name}",
            $officer
        );
    }

    /**
     * Step 6: Computer-based entrance scoring
     */
    public function recordEntranceExamScore(
        Application $application,
        float $score,
        ?string $remarks,
        User $officer
    ): void {
        $old = [
            'entrance_exam_score' => $application->entrance_exam_score,
            'status' => $application->status,
        ];

        $application->update([
            'entrance_exam_score' => $score,
            'entrance_exam_remarks' => $remarks,
            'entrance_exam_scored_at' => now(),
            'entrance_exam_scored_by' => $officer->id,
            'status' => 'exam_scored',
        ]);

        AuditService::log(
            'admissions.exam_scored',
            $application,
            $old,
            [
                'status' => 'exam_scored',
                'entrance_exam_score' => $score,
            ],
            "CBT entrance score {$score}% recorded for {$application->application_number} by {$officer->name}",
            $officer
        );
    }

    /**
     * Step 7: Offer admission (if successful in entrance exam)
     */
    public function offerAdmission(
        Application $application,
        User $officer,
        ?\DateTimeInterface $deadline = null
    ): void {
        $old = ['status' => $application->status];
        $ref = sprintf('ADM/%s/NUR/%s', date('Y'), strtoupper(Str::random(5)));
        $acceptanceDeadline = $deadline ?? now()->addWeeks(2);

        $application->update([
            'status' => 'offered',
            'admission_offered_at' => now(),
            'acceptance_deadline' => $acceptanceDeadline,
            'admission_letter_ref' => $ref,
            'decided_at' => now(),
        ]);

        // Auto-generate Acceptance Fee invoice (Step 8 setup)
        app(PaymentService::class)->generateInvoiceForApplication($application, 'acceptance_fee');

        AuditService::log(
            'admissions.offered',
            $application,
            $old,
            ['status' => 'offered', 'ref' => $ref],
            "Provisional admission offered to {$application->application_number} (Letter Ref: {$ref}) by {$officer->name}",
            $officer
        );
    }

    /**
     * Step 8: Final Student Matriculation
     */
    public function admit(Application $application, User $officer): Student
    {
        return DB::transaction(function () use ($application, $officer) {
            $year = date('Y');
            $studentNumber = $this->generateStudentNumber($year);

            // 1. Get or create user account for the student
            $user = $application->user;
            if (! $user) {
                $user = User::where('email', $application->email)->first();
            }

            if (! $user) {
                $user = User::create([
                    'name' => $application->full_name,
                    'email' => $application->email,
                    'password' => Hash::make($application->access_code ?? 'password123'),
                ]);
            }

            // Assign role
            $user->assignRole('student');

            // Find Level 100
            $level100 = Level::where('numeric_level', 100)->first() ?? Level::firstOrCreate(
                ['numeric_level' => 100],
                ['name' => 'Year 1', 'code' => '100L']
            );

            // 2. Create Student record
            $student = Student::create([
                'user_id' => $user->id,
                'student_number' => $studentNumber,
                'programme_id' => $application->programme_id,
                'department_id' => $application->programme->department_id,
                'entry_session_id' => $application->academic_session_id,
                'current_level_id' => $level100->id,
                'first_name' => $application->first_name,
                'middle_name' => $application->middle_name,
                'last_name' => $application->last_name,
                'gender' => $application->gender,
                'date_of_birth' => $application->date_of_birth,
                'phone' => $application->phone,
                'address' => $application->address,
                'state_of_origin' => $application->state_of_origin,
                'lga' => $application->lga,
                'status' => 'active',
                'admitted_at' => now(),
            ]);

            // 3. Update application status
            $old = ['status' => $application->status];
            $application->update([
                'status' => 'admitted',
                'matriculated_at' => now(),
                'decided_at' => now(),
            ]);

            // 4. Generate initial tuition invoice
            app(PaymentService::class)->generateInvoiceForStudent(
                $student,
                $application->academicSession,
                1
            );

            AuditService::log(
                'admissions.matriculated',
                $student,
                $old,
                ['status' => 'admitted', 'student_number' => $studentNumber],
                "Student {$studentNumber} matriculated from Application {$application->application_number} by {$officer->name}",
                $officer
            );

            return $student;
        });
    }
}
