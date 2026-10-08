<?php

namespace App\Livewire\Admin\Admissions;

use App\Models\Application;
use App\Models\Programme;
use App\Services\Admissions\ApplicationService;
use Livewire\Component;
use Livewire\WithPagination;

class ApplicationList extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    public ?int $programmeFilter = null;

    // Screening Modal
    public ?Application $selectedApplication = null;

    public float $screeningScore = 75.0;

    public string $screeningRemarks = '';

    public bool $showReviewModal = false;

    // Step 5: Schedule CBT Exam Modal
    public bool $showExamModal = false;

    public string $examDate = '2026-10-25T09:00';

    public string $examVenue = 'College CBT Center - Hall A';

    public string $examSeatNumber = '';

    // Step 6: Enter CBT Score Modal
    public bool $showScoreModal = false;

    public float $cbtScore = 78.5;

    public string $cbtRemarks = 'Candidate demonstrated exceptional knowledge in life sciences and nursing aptitude.';

    // Step 7: Offer Admission Modal
    public bool $showOfferModal = false;

    public string $acceptanceDeadline = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function selectForReview(int $id): void
    {
        $this->selectedApplication = Application::with(['programme', 'academicSession', 'invoices.payments'])->findOrFail($id);
        $this->screeningScore = (float) ($this->selectedApplication->screening_score ?? 75.0);
        $this->screeningRemarks = $this->selectedApplication->screening_remarks ?? '';
        $this->showReviewModal = true;
    }

    public function closeReviewModal(): void
    {
        $this->showReviewModal = false;
        $this->selectedApplication = null;
    }

    public function saveScreening(ApplicationService $service): void
    {
        if (! $this->selectedApplication) {
            return;
        }

        $this->authorize('screen', $this->selectedApplication);

        $service->screen(
            $this->selectedApplication,
            $this->screeningScore,
            $this->screeningRemarks,
            auth()->user()
        );

        session()->flash('success', "Screening score recorded for {$this->selectedApplication->application_number}.");
        $this->closeReviewModal();
    }

    // Step 5: Schedule Exam
    public function openExamModal(int $id): void
    {
        $this->selectedApplication = Application::findOrFail($id);
        $count = Application::where('entrance_exam_invited', true)->count() + 1;
        $this->examSeatNumber = sprintf('CBT-ST-%03d', $count);
        $this->showExamModal = true;
    }

    public function closeExamModal(): void
    {
        $this->showExamModal = false;
        $this->selectedApplication = null;
    }

    public function scheduleExam(ApplicationService $service): void
    {
        if (! $this->selectedApplication) {
            return;
        }

        $this->authorize('screen', $this->selectedApplication);

        $service->inviteForEntranceExam(
            $this->selectedApplication,
            $this->examDate,
            $this->examVenue,
            $this->examSeatNumber,
            auth()->user()
        );

        session()->flash('success', "Entrance exam scheduled for {$this->selectedApplication->application_number}. Seat: {$this->examSeatNumber}");
        $this->closeExamModal();
    }

    // Step 6: CBT Score
    public function openScoreModal(int $id): void
    {
        $this->selectedApplication = Application::findOrFail($id);
        $this->cbtScore = (float) ($this->selectedApplication->entrance_exam_score ?? 78.5);
        $this->cbtRemarks = $this->selectedApplication->entrance_exam_remarks ?? 'Candidate demonstrated good knowledge in life sciences and nursing aptitude.';
        $this->showScoreModal = true;
    }

    public function closeScoreModal(): void
    {
        $this->showScoreModal = false;
        $this->selectedApplication = null;
    }

    public function submitEntranceScore(ApplicationService $service): void
    {
        if (! $this->selectedApplication) {
            return;
        }

        $this->authorize('screen', $this->selectedApplication);

        $service->recordEntranceExamScore(
            $this->selectedApplication,
            $this->cbtScore,
            $this->cbtRemarks,
            auth()->user()
        );

        session()->flash('success', "CBT Entrance Score of {$this->cbtScore}% recorded for {$this->selectedApplication->application_number}.");
        $this->closeScoreModal();
    }

    // Step 7: Offer Admission
    public function openOfferModal(int $id): void
    {
        $this->selectedApplication = Application::findOrFail($id);
        $this->acceptanceDeadline = now()->addWeeks(2)->format('Y-m-d');
        $this->showOfferModal = true;
    }

    public function closeOfferModal(): void
    {
        $this->showOfferModal = false;
        $this->selectedApplication = null;
    }

    public function confirmOfferAdmission(ApplicationService $service): void
    {
        if (! $this->selectedApplication) {
            return;
        }

        $this->authorize('approve', $this->selectedApplication);

        $service->offerAdmission(
            $this->selectedApplication,
            auth()->user(),
            new \DateTime($this->acceptanceDeadline)
        );

        session()->flash('success', "Provisional admission offered to {$this->selectedApplication->full_name} ({$this->selectedApplication->application_number}). Acceptance fee invoice generated.");
        $this->closeOfferModal();
    }

    // Step 8: Final Matriculation
    public function admitApplicant(int $id, ApplicationService $service): void
    {
        $app = Application::with('programme')->findOrFail($id);
        $this->authorize('approve', $app);

        $student = $service->admit($app, auth()->user());

        session()->flash('success', "Applicant {$app->full_name} matriculated successfully! Matriculation Number: {$student->student_number}.");
        if ($this->selectedApplication && $this->selectedApplication->id === $id) {
            $this->closeReviewModal();
        }
    }

    public function render()
    {
        $query = Application::with(['programme', 'academicSession', 'invoices']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('application_number', 'like', "%{$this->search}%")
                    ->orWhere('first_name', 'like', "%{$this->search}%")
                    ->orWhere('last_name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->programmeFilter) {
            $query->where('programme_id', $this->programmeFilter);
        }

        $applications = $query->latest()->paginate(10);

        return view('livewire.admin.admissions.application-list', [
            'applications' => $applications,
            'programmes' => Programme::all(),
        ])->layout('layouts.institutional', ['title' => 'Admissions Screening & Lifecycle Board']);
    }
}
