<?php

namespace App\Livewire\Admin\Academics;

use App\Models\AcademicSession;
use App\Services\Academics\AcademicSessionTransitionService;
use Exception;
use Livewire\Component;

class SessionManager extends Component
{
    // Create Modal
    public bool $showCreateModal = false;

    public string $name = '';

    public string $code = '';

    public string $start_date = '';

    public string $end_date = '';

    // Selected Session for Details / Manage Semesters & Offerings
    public ?int $selectedSessionId = null;

    public bool $showManageModal = false;

    // Closing Pre-flight Modal
    public ?int $closingSessionId = null;

    public bool $showCloseModal = false;

    public array $preflightChecks = [];

    public bool $forceClose = false;

    public function openCreateModal(): void
    {
        $this->authorize('create', AcademicSession::class);
        $this->reset(['name', 'code', 'start_date', 'end_date']);
        $nextYear = (int) date('Y');
        $this->name = $nextYear.'/'.($nextYear + 1);
        $this->code = $nextYear.'-'.($nextYear + 1);
        $this->start_date = date('Y-09-01');
        $this->end_date = date('Y-08-31', strtotime('+1 year'));
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    public function createSession(AcademicSessionTransitionService $service): void
    {
        $this->authorize('create', AcademicSession::class);

        $this->validate([
            'name' => 'required|string|unique:academic_sessions,name',
            'code' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        try {
            $session = $service->create([
                'name' => $this->name,
                'code' => $this->code,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
            ], auth()->user());

            session()->flash('success', "Academic Session {$session->name} created in DRAFT state with semesters.");
            $this->closeCreateModal();
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function activateSession(int $sessionId, AcademicSessionTransitionService $service): void
    {
        $session = AcademicSession::findOrFail($sessionId);
        $this->authorize('activate', $session);

        try {
            $service->activate($session, auth()->user());
            session()->flash('success', "Academic Session {$session->name} activated successfully as current operational session.");
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function beginResultProcessing(int $sessionId, AcademicSessionTransitionService $service): void
    {
        $session = AcademicSession::findOrFail($sessionId);
        $this->authorize('beginResultProcessing', $session);

        try {
            $service->beginResultProcessing($session, auth()->user());
            session()->flash('success', "Academic Session {$session->name} transitioned to RESULT_PROCESSING. Course registration is now locked.");
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function openCloseModal(int $sessionId, AcademicSessionTransitionService $service): void
    {
        $session = AcademicSession::findOrFail($sessionId);
        $this->authorize('close', $session);

        $this->closingSessionId = $sessionId;
        $this->preflightChecks = $service->validateClosing($session);
        $this->forceClose = false;
        $this->showCloseModal = true;
    }

    public function closeCloseModal(): void
    {
        $this->showCloseModal = false;
        $this->closingSessionId = null;
        $this->preflightChecks = [];
    }

    public function executeCloseSession(AcademicSessionTransitionService $service): void
    {
        if (! $this->closingSessionId) {
            return;
        }

        $session = AcademicSession::findOrFail($this->closingSessionId);
        $this->authorize('close', $session);

        try {
            $service->close($session, auth()->user(), $this->forceClose);
            session()->flash('success', "Academic Session {$session->name} has been closed and student progression finalized.");
            $this->closeCloseModal();
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function archiveSession(int $sessionId, AcademicSessionTransitionService $service): void
    {
        $session = AcademicSession::findOrFail($sessionId);
        $this->authorize('archive', $session);

        try {
            $service->archive($session, auth()->user());
            session()->flash('success', "Academic Session {$session->name} archived as read-only historical container.");
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function manageSession(int $sessionId): void
    {
        $this->selectedSessionId = $sessionId;
        $this->showManageModal = true;
    }

    public function closeManageModal(): void
    {
        $this->showManageModal = false;
        $this->selectedSessionId = null;
    }

    public function provisionOfferings(AcademicSessionTransitionService $service): void
    {
        if (! $this->selectedSessionId) {
            return;
        }
        $session = AcademicSession::findOrFail($this->selectedSessionId);

        $count = $service->provisionCourseOfferings($session);
        session()->flash('success', "{$count} course offerings provisioned for session {$session->name}.");
    }

    public function render()
    {
        $sessions = AcademicSession::with(['semesters', 'creator', 'activator'])
            ->orderBy('is_current', 'desc')
            ->orderBy('start_date', 'desc')
            ->get();

        $currentSession = AcademicSession::current();
        $selectedSession = $this->selectedSessionId ? AcademicSession::with(['semesters', 'offerings.course'])->find($this->selectedSessionId) : null;
        $closingSession = $this->closingSessionId ? AcademicSession::find($this->closingSessionId) : null;

        return view('livewire.admin.academics.session-manager', [
            'sessions' => $sessions,
            'currentSession' => $currentSession,
            'selectedSession' => $selectedSession,
            'closingSession' => $closingSession,
        ])->layout('layouts.institutional', ['title' => 'Academic Sessions & Term Lifecycle Container']);
    }
}
