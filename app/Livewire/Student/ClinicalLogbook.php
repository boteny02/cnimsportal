<?php

namespace App\Livewire\Student;

use App\Models\ClinicalProcedure;
use App\Models\CompetencyCategory;
use App\Models\Student;
use App\Services\Clinical\ClinicalService;
use Livewire\Component;

class ClinicalLogbook extends Component
{
    public ?Student $student = null;

    // Log Procedure Form
    public ?int $procedure_id = null;

    public string $procedure_date = '';

    public string $patient_reference_code = '';

    public string $competency_level = 'supervised';

    public string $student_reflection = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->student = ($user && $user->student) ? $user->student : Student::first();

        $this->procedure_date = date('Y-m-d');
        $firstProc = ClinicalProcedure::first();
        if ($firstProc) {
            $this->procedure_id = $firstProc->id;
        }
    }

    public function submitLog(ClinicalService $service): void
    {
        if (! $this->student) {
            return;
        }

        $this->validate([
            'procedure_id' => 'required|exists:clinical_procedures,id',
            'procedure_date' => 'required|date',
            'competency_level' => 'required|in:observed,assisted,supervised,competent,independent',
            'student_reflection' => 'required|string|min:10',
        ]);

        $activePosting = $this->student->postings()->where('clinical_postings.status', 'active')->first();

        $service->logProcedure($this->student, [
            'clinical_posting_id' => $activePosting?->id,
            'procedure_id' => $this->procedure_id,
            'procedure_date' => $this->procedure_date,
            'patient_reference_code' => $this->patient_reference_code ?: 'PT-CONF-'.rand(100, 999),
            'competency_level' => $this->competency_level,
            'student_reflection' => $this->student_reflection,
            'supervisor_id' => $activePosting?->supervisor_id,
        ]);

        session()->flash('success', 'Clinical procedure logged successfully and queued for preceptor verification.');
        $this->reset(['student_reflection', 'patient_reference_code']);
    }

    public function render()
    {
        $activePosting = $this->student?->postings()->where('clinical_postings.status', 'active')->first();
        $logs = $this->student?->logbooks()->with(['procedure', 'supervisor'])->latest('procedure_date')->get() ?? collect();
        $procedures = ClinicalProcedure::orderBy('title')->get();
        $categories = CompetencyCategory::with(['skills.studentCompetencies' => function ($q) {
            $q->where('student_id', $this->student?->id);
        }])->get();

        return view('livewire.student.clinical-logbook', [
            'activePosting' => $activePosting,
            'logs' => $logs,
            'procedures' => $procedures,
            'categories' => $categories,
        ])->layout('layouts.institutional', ['title' => 'Clinical Hospital Rotation & Digital Logbook']);
    }
}
