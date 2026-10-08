<?php

namespace App\Livewire\Admin\Students;

use App\Models\Level;
use App\Models\Programme;
use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;

class StudentDirectory extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $programmeFilter = null;

    public ?int $levelFilter = null;

    public string $statusFilter = 'all';

    // Slideover / Dossier
    public ?Student $selectedStudent = null;

    public string $activeTab = 'personal'; // personal, academics, clinical, finance

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function viewProfile(int $id): void
    {
        $this->selectedStudent = Student::with([
            'programme',
            'department',
            'currentLevel',
            'entrySession',
            'courseRegistrations.items.course',
            'results.course',
            'postings.facility',
            'postings.ward',
            'logbooks.procedure',
            'competencies.skill',
            'invoices.payments',
            'financialClearances',
        ])->findOrFail($id);

        $this->activeTab = 'personal';
    }

    public function closeProfile(): void
    {
        $this->selectedStudent = null;
    }

    public function render()
    {
        $query = Student::with(['programme', 'department', 'currentLevel']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('student_number', 'like', "%{$this->search}%")
                    ->orWhere('first_name', 'like', "%{$this->search}%")
                    ->orWhere('last_name', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%");
            });
        }

        if ($this->programmeFilter) {
            $query->where('programme_id', $this->programmeFilter);
        }

        if ($this->levelFilter) {
            $query->where('current_level_id', $this->levelFilter);
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $students = $query->orderBy('student_number')->paginate(10);

        return view('livewire.admin.students.student-directory', [
            'students' => $students,
            'programmes' => Programme::all(),
            'levels' => Level::all(),
        ])->layout('layouts.institutional', ['title' => 'Student Directory & Institutional Dossiers']);
    }
}
