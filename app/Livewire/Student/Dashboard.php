<?php

namespace App\Livewire\Student;

use App\Models\AcademicSession;
use App\Models\FinancialClearance;
use App\Models\Student;
use Livewire\Component;

class Dashboard extends Component
{
    public ?Student $student = null;

    public function mount(): void
    {
        $user = auth()->user();
        if ($user && $user->student) {
            $this->student = $user->student;
        } else {
            // For testing convenience if authenticated as admin or guest
            $this->student = Student::first();
        }
    }

    public function render()
    {
        $session = AcademicSession::current();

        $activePosting = $this->student?->postings()->where('clinical_postings.status', 'active')->first();
        $recentLogbooks = $this->student?->logbooks()->latest('procedure_date')->take(4)->get() ?? collect();
        $latestResults = $this->student?->results()->latest()->take(5)->get() ?? collect();
        $semesterSummary = $this->student?->semesterResults()->latest()->first();

        $clearance = null;
        if ($this->student && $session) {
            $clearance = FinancialClearance::where('student_id', $this->student->id)
                ->where('academic_session_id', $session->id)
                ->where('semester', 1)
                ->where('clearance_type', 'course_registration')
                ->where('is_cleared', true)
                ->first();
        }

        return view('livewire.student.dashboard', [
            'student' => $this->student,
            'session' => $session,
            'activePosting' => $activePosting,
            'recentLogbooks' => $recentLogbooks,
            'latestResults' => $latestResults,
            'semesterSummary' => $semesterSummary,
            'isCleared' => (bool) $clearance,
        ])->layout('layouts.institutional', ['title' => 'Student Portal Dashboard']);
    }
}
