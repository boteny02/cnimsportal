<?php

namespace App\Livewire\Student;

use App\Models\AcademicSession;
use App\Models\Course;
use App\Models\Student;
use App\Models\StudentCourseRegistration;
use App\Services\Academics\CourseRegistrationService;
use Livewire\Component;

class CourseRegistration extends Component
{
    public ?Student $student = null;

    public ?AcademicSession $currentSession = null;

    public int $semester = 1;

    public array $selectedCourseIds = [];

    public ?StudentCourseRegistration $existingRegistration = null;

    public function mount(CourseRegistrationService $service): void
    {
        $user = auth()->user();
        $this->student = ($user && $user->student) ? $user->student : Student::first();
        $this->currentSession = AcademicSession::current();

        $this->loadRegistration();
    }

    public function loadRegistration(): void
    {
        if (! $this->student || ! $this->currentSession) {
            return;
        }

        $this->existingRegistration = StudentCourseRegistration::with('items.course')
            ->where('student_id', $this->student->id)
            ->where('academic_session_id', $this->currentSession->id)
            ->where('semester', $this->semester)
            ->first();

        if ($this->existingRegistration) {
            $this->selectedCourseIds = $this->existingRegistration->items->pluck('course_id')->toArray();
        } else {
            // Default select core courses
            $available = Course::where('level', $this->student->currentLevel?->numeric_level ?? 100)
                ->where('semester', $this->semester)
                ->pluck('id')
                ->toArray();
            $this->selectedCourseIds = $available;
        }
    }

    public function submitRegistration(CourseRegistrationService $service): void
    {
        if (! $this->student || ! $this->currentSession) {
            return;
        }

        try {
            $reg = $service->registerCourses(
                $this->student,
                $this->selectedCourseIds,
                $this->currentSession,
                $this->semester
            );

            session()->flash('success', "Course registration successfully submitted ({$reg->total_credits} credit units)!");
            $this->loadRegistration();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render(CourseRegistrationService $service)
    {
        $availableCourses = collect();
        $validation = ['valid' => true, 'errors' => [], 'total_credits' => 0];

        if ($this->student && $this->currentSession) {
            $availableCourses = $service->getAvailableCourses($this->student, $this->currentSession, $this->semester);
            $validation = $service->validateRegistration($this->student, $this->selectedCourseIds, $this->currentSession, $this->semester);
        }

        return view('livewire.student.course-registration', [
            'availableCourses' => $availableCourses,
            'validation' => $validation,
        ])->layout('layouts.institutional', ['title' => 'Semester Course Registration']);
    }
}
