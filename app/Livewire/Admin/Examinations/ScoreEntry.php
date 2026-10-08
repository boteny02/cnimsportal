<?php

namespace App\Livewire\Admin\Examinations;

use App\Enums\AttemptType;
use App\Models\AcademicSession;
use App\Models\Course;
use App\Models\Student;
use App\Models\StudentResult;
use App\Services\Examinations\ResultProcessingService;
use Livewire\Component;

class ScoreEntry extends Component
{
    public ?int $selectedCourseId = null;

    public ?int $selectedSessionId = null;

    public int $selectedSemester = 1;

    // Scores keyed by student_id
    public array $scores = [];

    // Correction request modal
    public bool $showCorrectionModal = false;

    public ?int $correctingStudentId = null;

    public string $correctionReason = '';

    public function mount(): void
    {
        $session = AcademicSession::current();
        if ($session) {
            $this->selectedSessionId = $session->id;
        }

        $defaultCourse = Course::first();
        if ($defaultCourse) {
            $this->selectedCourseId = $defaultCourse->id;
            $this->loadScores();
        }
    }

    public function updatedSelectedCourseId(): void
    {
        $this->loadScores();
    }

    public function updatedSelectedSessionId(): void
    {
        $this->loadScores();
    }

    public function updatedSelectedSemester(): void
    {
        $this->loadScores();
    }

    public function loadScores(): void
    {
        $this->scores = [];
        if (! $this->selectedCourseId || ! $this->selectedSessionId) {
            return;
        }

        $course = Course::find($this->selectedCourseId);
        if (! $course) {
            return;
        }

        $students = Student::where('programme_id', $course->programme_id)
            ->where('status', 'active')
            ->get();

        foreach ($students as $st) {
            $existing = StudentResult::where('student_id', $st->id)
                ->where('course_id', $course->id)
                ->where('academic_session_id', $this->selectedSessionId)
                ->where('semester', $this->selectedSemester)
                ->first();

            $ca = $existing ? (float) $existing->ca_score : 25.0;
            $exam = $existing ? (float) $existing->exam_score : 45.0;

            // Detect past attempts in previous sessions
            $pastAttemptsCount = StudentResult::where('student_id', $st->id)
                ->where('course_id', $course->id)
                ->where('academic_session_id', '!=', $this->selectedSessionId)
                ->count();

            $attemptNumber = $existing ? $existing->attempt_number : ($pastAttemptsCount + 1);
            $attemptType = $existing
                ? ($existing->attempt_type?->value ?? ($pastAttemptsCount > 0 ? AttemptType::CARRYOVER->value : AttemptType::FIRST_ATTEMPT->value))
                : ($pastAttemptsCount > 0 ? AttemptType::CARRYOVER->value : AttemptType::FIRST_ATTEMPT->value);

            $this->scores[$st->id] = [
                'ca' => $ca,
                'exam' => $exam,
                'status' => $existing?->status?->value ?? ($existing?->status ?? 'draft'),
                'attempt_number' => $attemptNumber,
                'attempt_type' => $attemptType,
                'result_id' => $existing?->id,
            ];
        }
    }

    public function saveLecturerScores(ResultProcessingService $service): void
    {
        if (! $this->selectedCourseId || ! $this->selectedSessionId) {
            return;
        }

        $course = Course::findOrFail($this->selectedCourseId);
        $this->authorize('enterScores', $course);

        try {
            $service->saveLecturerScores(
                $this->selectedCourseId,
                $this->selectedSessionId,
                $this->selectedSemester,
                $this->scores,
                auth()->user()
            );

            session()->flash('success', 'Assessment scores recorded and submitted by lecturer.');
            $this->loadScores();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function verifyScores(ResultProcessingService $service): void
    {
        if (! $this->selectedCourseId || ! $this->selectedSessionId) {
            return;
        }

        $course = Course::findOrFail($this->selectedCourseId);
        if (auth()->user()->hasRole('hod')) {
            abort_unless(auth()->user()->department_id === $course->department_id, 403, 'Unauthorized to verify results outside your department.');
        }
        $this->authorize('results.verify');

        try {
            $service->verifyScores(
                $this->selectedCourseId,
                $this->selectedSessionId,
                $this->selectedSemester,
                auth()->user()
            );

            session()->flash('success', 'Course assessment verified by Head of Department.');
            $this->loadScores();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function processResults(ResultProcessingService $service): void
    {
        if (! $this->selectedSessionId) {
            return;
        }

        $this->authorize('results.verify');

        try {
            $service->processResults(
                $this->selectedSessionId,
                $this->selectedSemester,
                auth()->user()
            );

            session()->flash('success', 'Semester examinations processed! GPA and cumulative CGPA recalculated for all students.');
            $this->loadScores();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function approveResults(ResultProcessingService $service): void
    {
        if (! $this->selectedSessionId) {
            return;
        }

        $this->authorize('results.approve');

        try {
            $service->approveResults(
                $this->selectedSessionId,
                $this->selectedSemester,
                auth()->user()
            );

            session()->flash('success', 'Results formally approved by Academic Board.');
            $this->loadScores();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function publishResults(ResultProcessingService $service): void
    {
        if (! $this->selectedSessionId) {
            return;
        }

        $this->authorize('results.publish');

        try {
            $service->publishResults(
                $this->selectedSessionId,
                $this->selectedSemester,
                auth()->user()
            );

            session()->flash('success', 'Results officially published to the Student Portal!');
            $this->loadScores();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function openCorrectionModal(int $studentId): void
    {
        $this->correctingStudentId = $studentId;
        $this->correctionReason = '';
        $this->showCorrectionModal = true;
    }

    public function closeCorrectionModal(): void
    {
        $this->showCorrectionModal = false;
        $this->correctingStudentId = null;
        $this->correctionReason = '';
    }

    public function submitCorrectionRequest(ResultProcessingService $service): void
    {
        if (! $this->correctingStudentId) {
            return;
        }

        $resId = $this->scores[$this->correctingStudentId]['result_id'] ?? null;
        if (! $resId) {
            session()->flash('error', 'No submitted result found for this student.');

            return;
        }

        $this->validate(['correctionReason' => 'required|string|min:5']);

        try {
            $service->requestCorrection($resId, $this->correctionReason, auth()->user());
            session()->flash('success', 'Result correction request submitted successfully.');
            $this->closeCorrectionModal();
            $this->loadScores();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render(ResultProcessingService $service)
    {
        $course = Course::with('programme')->find($this->selectedCourseId);
        $session = AcademicSession::find($this->selectedSessionId);
        $students = Student::where('status', 'active')
            ->where('programme_id', $course?->programme_id)
            ->get();

        $rows = [];
        foreach ($students as $st) {
            $ca = (float) ($this->scores[$st->id]['ca'] ?? 0);
            $exam = (float) ($this->scores[$st->id]['exam'] ?? 0);
            $total = $ca + $exam;
            $calc = $service->calculateGradeAndPoints($total);

            $rows[] = [
                'student' => $st,
                'ca' => $ca,
                'exam' => $exam,
                'total' => $total,
                'grade' => $calc['grade'],
                'points' => $calc['grade_point'],
                'status' => $this->scores[$st->id]['status'] ?? 'draft',
                'attempt_number' => $this->scores[$st->id]['attempt_number'] ?? 1,
                'attempt_type' => $this->scores[$st->id]['attempt_type'] ?? 'FIRST_ATTEMPT',
                'result_id' => $this->scores[$st->id]['result_id'] ?? null,
            ];
        }

        return view('livewire.admin.examinations.score-entry', [
            'course' => $course,
            'courses' => Course::all(),
            'session' => $session,
            'sessions' => AcademicSession::all(),
            'rows' => $rows,
        ])->layout('layouts.institutional', ['title' => 'Examinations & Grading Management']);
    }
}
