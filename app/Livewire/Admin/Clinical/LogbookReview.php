<?php

namespace App\Livewire\Admin\Clinical;

use App\Models\ClinicalLogbook;
use App\Services\Clinical\ClinicalService;
use Livewire\Component;
use Livewire\WithPagination;

class LogbookReview extends Component
{
    use WithPagination;

    public string $statusFilter = 'submitted'; // submitted, approved, rejected, all

    public ?int $selectedLogId = null;

    public string $supervisorRemarks = 'Demonstrated strict aseptic technique and good patient communication.';

    public bool $showReviewModal = false;

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openReview(int $id): void
    {
        $this->selectedLogId = $id;
        $this->showReviewModal = true;
    }

    public function closeReview(): void
    {
        $this->showReviewModal = false;
        $this->selectedLogId = null;
    }

    public function approve(ClinicalService $service): void
    {
        if (! $this->selectedLogId) {
            return;
        }

        $log = ClinicalLogbook::findOrFail($this->selectedLogId);
        $this->authorize('approve', $log);

        $service->verifyLogbookEntry($log, true, $this->supervisorRemarks, auth()->user());

        session()->flash('success', "Procedure '{$log->procedure?->title}' approved for {$log->student->student_number}.");
        $this->closeReview();
    }

    public function reject(ClinicalService $service): void
    {
        if (! $this->selectedLogId) {
            return;
        }

        $log = ClinicalLogbook::findOrFail($this->selectedLogId);
        $this->authorize('review', $log);

        $service->verifyLogbookEntry($log, false, $this->supervisorRemarks, auth()->user());

        session()->flash('success', "Procedure '{$log->procedure?->title}' returned/rejected for revision.");
        $this->closeReview();
    }

    public function render()
    {
        $query = ClinicalLogbook::with(['student', 'procedure', 'posting.facility', 'supervisor']);

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $logs = $query->latest('procedure_date')->paginate(10);
        $selectedLog = $this->selectedLogId ? ClinicalLogbook::find($this->selectedLogId) : null;

        return view('livewire.admin.clinical.logbook-review', [
            'logs' => $logs,
            'selectedLog' => $selectedLog,
        ])->layout('layouts.institutional', ['title' => 'Digital Clinical Logbook Preceptor Review']);
    }
}
