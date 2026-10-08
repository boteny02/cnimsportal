<?php

namespace App\Livewire\Public;

use App\Models\AcademicSession;
use App\Models\Application;
use App\Models\Programme;
use App\Models\User;
use App\Services\Admissions\ApplicationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ApplicationWizard extends Component
{
    public int $step = 1;

    // Step 1: Personal Information
    public string $first_name = '';

    public string $middle_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $phone = '';

    public string $gender = 'female';

    public string $date_of_birth = '2005-06-15';

    public string $state_of_origin = '';

    public string $lga = '';

    public string $address = '';

    // Step 2: Programme Selection
    public ?int $programme_id = null;

    public ?int $academic_session_id = null;

    // Step 3: 2-Sitting O'Level Subject Credits
    public string $secondary_school = 'Queen Amina College, Kaduna';

    public string $secondary_school_sitting_1 = 'Queen Amina College, Kaduna';

    public string $secondary_school_sitting_2 = 'Government Girls Secondary School, Zaria';

    public string $graduation_year = '2024';

    public int $o_level_sittings = 1; // 1 or 2

    // Sitting 1
    public string $sitting_1_exam_body = 'WAEC';

    public string $sitting_1_exam_year = '2024';

    public string $sitting_1_exam_number = '4120984128';

    public string $sitting_1_center = '';

    public array $sitting_1_subjects = [
        ['subject' => 'English Language', 'grade' => 'B2'],
        ['subject' => 'Mathematics', 'grade' => 'B3'],
        ['subject' => 'Biology', 'grade' => 'A1'],
        ['subject' => 'Chemistry', 'grade' => 'B2'],
        ['subject' => 'Physics', 'grade' => 'B3'],
    ];

    public string $sitting_1_custom_subject = 'Civic Education';

    public string $sitting_1_custom_grade = 'B2';

    // Sitting 2 (Optional)
    public string $sitting_2_exam_body = 'NECO';

    public string $sitting_2_exam_year = '2024';

    public string $sitting_2_exam_number = '50982314AB';

    public string $sitting_2_center = '';

    public array $sitting_2_subjects = [
        ['subject' => 'English Language', 'grade' => 'C4'],
        ['subject' => 'Mathematics', 'grade' => 'C4'],
        ['subject' => 'Biology', 'grade' => 'B2'],
        ['subject' => 'Chemistry', 'grade' => 'C5'],
        ['subject' => 'Physics', 'grade' => 'C4'],
    ];

    public string $sitting_2_custom_subject = 'Agricultural Science';

    public string $sitting_2_custom_grade = 'C4';

    // Step 4: JAMB UTME Profile
    public string $jamb_reg_number = '202610482914AB';

    public int $jamb_score = 215;

    public array $jamb_subjects = [
        ['subject' => 'Use of English', 'score' => 62],
        ['subject' => 'Biology', 'score' => 58],
        ['subject' => 'Chemistry', 'score' => 50],
        ['subject' => 'Physics', 'score' => 45],
    ];

    // Step 5: Applicant Portal Credentials Password
    public string $password = 'password123';

    public string $password_confirmation = 'password123';

    // Result & Success Output
    public ?string $submittedApplicationNumber = null;

    public ?Application $createdApplication = null;

    protected function rules(): array
    {
        if ($this->step === 1) {
            return [
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'email' => 'required|email|max:100',
                'phone' => 'required|string|max:20',
                'gender' => 'required|in:male,female,other',
                'date_of_birth' => 'required|date',
                'state_of_origin' => 'required|string|max:50',
                'lga' => 'required|string|max:50',
                'address' => 'required|string|max:255',
            ];
        }

        if ($this->step === 2) {
            return [
                'programme_id' => 'required|exists:programmes,id',
                'academic_session_id' => 'required|exists:academic_sessions,id',
            ];
        }

        if ($this->step === 3) {
            $rules = [
                'secondary_school_sitting_1' => 'required|string|max:150',
                'graduation_year' => 'required|digits:4',
                'o_level_sittings' => 'required|in:1,2',
                'sitting_1_exam_body' => 'required|string',
                'sitting_1_exam_year' => 'required|digits:4',
                'sitting_1_exam_number' => 'required|string|max:30',
                'sitting_1_custom_subject' => 'required|string|max:60',
                'sitting_1_custom_grade' => 'required|in:A1,B2,B3,C4,C5,C6,D7,E8,F9',
            ];

            if ($this->o_level_sittings === 2) {
                $rules['secondary_school_sitting_2'] = 'required|string|max:150';
                $rules['sitting_2_exam_body'] = 'required|string';
                $rules['sitting_2_exam_year'] = 'required|digits:4';
                $rules['sitting_2_exam_number'] = 'required|string|max:30';
                $rules['sitting_2_custom_subject'] = 'required|string|max:60';
                $rules['sitting_2_custom_grade'] = 'required|in:A1,B2,B3,C4,C5,C6,D7,E8,F9';
            }

            return $rules;
        }

        if ($this->step === 4) {
            return [
                'jamb_reg_number' => 'required|string|max:20',
                'jamb_score' => 'required|integer|min:100|max:400',
            ];
        }

        if ($this->step === 5) {
            return [
                'password' => 'required|string|min:6|same:password_confirmation',
            ];
        }

        return [];
    }

    public function nextStep(): void
    {
        $this->validate();
        $this->step++;
    }

    public function prevStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function submitApplication(ApplicationService $service): void
    {
        $this->validate();

        // Ensure 6th custom subject is appended to subjects array
        $s1Subjects = $this->sitting_1_subjects;
        $hasCustom1 = false;
        foreach ($s1Subjects as $sub) {
            if (strcasecmp($sub['subject'] ?? '', $this->sitting_1_custom_subject) === 0) {
                $hasCustom1 = true;
                break;
            }
        }
        if (! $hasCustom1 && ! empty($this->sitting_1_custom_subject)) {
            $s1Subjects[] = [
                'subject' => trim($this->sitting_1_custom_subject),
                'grade' => $this->sitting_1_custom_grade,
            ];
        }

        $sitting1Data = [
            'exam_body' => $this->sitting_1_exam_body,
            'exam_year' => $this->sitting_1_exam_year,
            'exam_number' => $this->sitting_1_exam_number,
            'school_name' => $this->secondary_school_sitting_1,
            'center' => $this->sitting_1_center,
            'subjects' => $s1Subjects,
        ];

        $sitting2Data = null;
        if ($this->o_level_sittings === 2) {
            $s2Subjects = $this->sitting_2_subjects;
            $hasCustom2 = false;
            foreach ($s2Subjects as $sub) {
                if (strcasecmp($sub['subject'] ?? '', $this->sitting_2_custom_subject) === 0) {
                    $hasCustom2 = true;
                    break;
                }
            }
            if (! $hasCustom2 && ! empty($this->sitting_2_custom_subject)) {
                $s2Subjects[] = [
                    'subject' => trim($this->sitting_2_custom_subject),
                    'grade' => $this->sitting_2_custom_grade,
                ];
            }

            $sitting2Data = [
                'exam_body' => $this->sitting_2_exam_body,
                'exam_year' => $this->sitting_2_exam_year,
                'exam_number' => $this->sitting_2_exam_number,
                'school_name' => $this->secondary_school_sitting_2,
                'center' => $this->sitting_2_center,
                'subjects' => $s2Subjects,
            ];
        }

        // 1. Evaluate 2-sitting O'Level credits (5 mandatory core + 6th applicant-supplied subject)
        $eval = $service->evaluateOLevelCredits($sitting1Data, $sitting2Data);

        // 2. Generate Application Number
        $appNumber = $service->generateApplicationNumber();

        // 3. Create or find User for the applicant
        $user = User::where('email', $this->email)->first();
        if (! $user) {
            $user = User::create([
                'name' => trim("{$this->first_name} {$this->middle_name} {$this->last_name}"),
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);
        }
        $user->assignRole('applicant');

        $schoolSummary = $this->secondary_school_sitting_1;
        if ($this->o_level_sittings === 2 && ! empty($this->secondary_school_sitting_2)) {
            $schoolSummary .= " / {$this->secondary_school_sitting_2}";
        }

        // 4. Create Application record
        $app = Application::create([
            'user_id' => $user->id,
            'application_number' => $appNumber,
            'access_code' => $this->password,
            'programme_id' => $this->programme_id,
            'academic_session_id' => $this->academic_session_id,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'state_of_origin' => $this->state_of_origin,
            'lga' => $this->lga,
            'address' => $this->address,
            'secondary_school' => $schoolSummary,
            'secondary_school_sitting_1' => $this->secondary_school_sitting_1,
            'secondary_school_sitting_2' => $this->o_level_sittings === 2 ? $this->secondary_school_sitting_2 : null,
            'graduation_year' => $this->graduation_year,
            'o_level_sittings' => $this->o_level_sittings,
            'o_level_sitting_1' => $sitting1Data,
            'o_level_sitting_2' => $sitting2Data,
            'o_level_verified' => $eval['is_qualified'],
            'o_level_credits_count' => $eval['credits_count'],
            'jamb_reg_number' => $this->jamb_reg_number,
            'jamb_score' => $this->jamb_score,
            'jamb_subjects' => $this->jamb_subjects,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // 5. Submit through ApplicationService (generates Step 4 Application Fee Invoice)
        $service->submit($app);

        // 6. Log applicant in
        Auth::login($user);

        $this->submittedApplicationNumber = $appNumber;
        $this->createdApplication = $app;
        $this->step = 6; // Success confirmation
    }

    public function render()
    {
        return view('livewire.public.application-wizard', [
            'programmes' => Programme::where('is_active', true)->get(),
            'sessions' => AcademicSession::all(),
        ])->layout('layouts.institutional', ['title' => 'Nursing Admissions Application Wizard']);
    }
}
