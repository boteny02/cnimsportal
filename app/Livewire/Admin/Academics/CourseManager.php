<?php

namespace App\Livewire\Admin\Academics;

use App\Models\AcademicSession;
use App\Models\Course;
use App\Models\Department;
use App\Models\Programme;
use App\Models\User;
use Livewire\Component;

class CourseManager extends Component
{
    public ?int $selectedProgrammeId = null;

    public int $selectedLevel = 100;

    public int $selectedSemester = 1;

    // Create Course Modal
    public bool $showCreateModal = false;

    public string $code = '';

    public string $title = '';

    public int $credit_units = 3;

    public int $level = 100;

    public int $semester = 1;

    public string $course_type = 'core';

    public ?int $department_id = null;

    public ?int $programme_id = null;

    public ?int $lecturer_id = null;

    public string $description = '';

    public array $selectedPrerequisites = [];

    public function mount(): void
    {
        $defaultProg = Programme::first();
        if ($defaultProg) {
            $this->selectedProgrammeId = $defaultProg->id;
            $this->programme_id = $defaultProg->id;
            $this->department_id = $defaultProg->department_id;
        }
    }

    public function openCreateModal(): void
    {
        $this->reset(['code', 'title', 'credit_units', 'description', 'selectedPrerequisites']);
        $this->level = $this->selectedLevel;
        $this->semester = $this->selectedSemester;
        $this->programme_id = $this->selectedProgrammeId;
        $dept = Department::first();
        $this->department_id = $dept ? $dept->id : 1;
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    public function saveCourse(): void
    {
        $this->validate([
            'code' => 'required|string|unique:courses,code',
            'title' => 'required|string|max:150',
            'credit_units' => 'required|integer|min:1|max:6',
            'level' => 'required|integer',
            'semester' => 'required|in:1,2',
            'department_id' => 'required|exists:departments,id',
        ]);

        $course = Course::create([
            'department_id' => $this->department_id,
            'programme_id' => $this->programme_id,
            'code' => strtoupper(trim($this->code)),
            'title' => trim($this->title),
            'credit_units' => $this->credit_units,
            'level' => $this->level,
            'semester' => $this->semester,
            'course_type' => $this->course_type,
            'lecturer_id' => $this->lecturer_id,
            'description' => $this->description,
        ]);

        if (! empty($this->selectedPrerequisites)) {
            $course->prerequisites()->sync($this->selectedPrerequisites);
        }

        session()->flash('success', "Course {$course->code} created successfully!");
        $this->closeCreateModal();
    }

    public function render()
    {
        $coursesQuery = Course::with(['department', 'programme', 'lecturer', 'prerequisites']);

        if ($this->selectedProgrammeId) {
            $coursesQuery->where(function ($q) {
                $q->where('programme_id', $this->selectedProgrammeId)
                    ->orWhereNull('programme_id');
            });
        }

        $courses = $coursesQuery->where('level', $this->selectedLevel)
            ->where('semester', $this->selectedSemester)
            ->orderBy('code')
            ->get();

        return view('livewire.admin.academics.course-manager', [
            'courses' => $courses,
            'programmes' => Programme::all(),
            'departments' => Department::all(),
            'allCourses' => Course::orderBy('code')->get(),
            'lecturers' => User::all(),
            'currentSession' => AcademicSession::current(),
        ])->layout('layouts.institutional', ['title' => 'Academic Curriculum & Course Management']);
    }
}
