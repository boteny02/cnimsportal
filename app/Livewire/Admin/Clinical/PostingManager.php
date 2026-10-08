<?php

namespace App\Livewire\Admin\Clinical;

use App\Models\AcademicSession;
use App\Models\ClinicalFacility;
use App\Models\ClinicalPosting;
use App\Models\ClinicalWard;
use App\Models\Programme;
use App\Models\Student;
use App\Models\User;
use App\Services\Clinical\ClinicalService;
use Livewire\Component;

class PostingManager extends Component
{
    public ?int $selectedPostingId = null;

    // Create Posting Form
    public bool $showCreateModal = false;

    public string $title = '';

    public ?int $programme_id = null;

    public ?int $academic_session_id = null;

    public int $level = 200;

    public ?int $facility_id = null;

    public ?int $ward_id = null;

    public ?int $supervisor_id = null;

    public string $start_date = '';

    public string $end_date = '';

    public int $max_capacity = 15;

    public string $learning_objectives = '';

    // Assign Students
    public bool $showAssignModal = false;

    public array $selectedStudentIds = [];

    public function mount(): void
    {
        $prog = Programme::first();
        if ($prog) {
            $this->programme_id = $prog->id;
        }

        $session = AcademicSession::current();
        if ($session) {
            $this->academic_session_id = $session->id;
        }

        $fac = ClinicalFacility::first();
        if ($fac) {
            $this->facility_id = $fac->id;
            $ward = ClinicalWard::where('clinical_facility_id', $fac->id)->first();
            $this->ward_id = $ward?->id;
        }

        $this->start_date = date('Y-m-d');
        $this->end_date = date('Y-m-d', strtotime('+30 days'));
    }

    public function openCreateModal(): void
    {
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    public function savePosting(): void
    {
        $this->validate([
            'title' => 'required|string|max:150',
            'facility_id' => 'required|exists:clinical_facilities,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'max_capacity' => 'required|integer|min:1|max:50',
        ]);

        $posting = ClinicalPosting::create([
            'title' => $this->title,
            'programme_id' => $this->programme_id,
            'academic_session_id' => $this->academic_session_id,
            'level' => $this->level,
            'facility_id' => $this->facility_id,
            'ward_id' => $this->ward_id,
            'supervisor_id' => $this->supervisor_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'max_capacity' => $this->max_capacity,
            'learning_objectives' => $this->learning_objectives,
            'status' => 'active',
        ]);

        session()->flash('success', "Clinical posting '{$posting->title}' created successfully!");
        $this->closeCreateModal();
    }

    public function openAssignModal(int $postingId): void
    {
        $this->selectedPostingId = $postingId;
        $this->selectedStudentIds = [];
        $this->showAssignModal = true;
    }

    public function closeAssignModal(): void
    {
        $this->showAssignModal = false;
        $this->selectedPostingId = null;
    }

    public function assignStudents(ClinicalService $service): void
    {
        if (! $this->selectedPostingId || empty($this->selectedStudentIds)) {
            return;
        }

        $posting = ClinicalPosting::findOrFail($this->selectedPostingId);

        try {
            $service->assignStudentsToPosting($posting, $this->selectedStudentIds);
            session()->flash('success', 'Students assigned to rotation successfully.');
            $this->closeAssignModal();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $postings = ClinicalPosting::with(['facility', 'ward', 'supervisor', 'students', 'programme'])
            ->latest()
            ->get();

        $selectedPosting = $this->selectedPostingId ? ClinicalPosting::find($this->selectedPostingId) : null;
        $unassignedStudents = Student::where('status', 'active')->get();

        return view('livewire.admin.clinical.posting-manager', [
            'postings' => $postings,
            'facilities' => ClinicalFacility::all(),
            'wards' => ClinicalWard::where('clinical_facility_id', $this->facility_id)->get(),
            'supervisors' => User::all(),
            'programmes' => Programme::all(),
            'unassignedStudents' => $unassignedStudents,
            'selectedPosting' => $selectedPosting,
        ])->layout('layouts.institutional', ['title' => 'Clinical Hospital Rotations & Postings']);
    }
}
