<?php

namespace App\Livewire\Student;

use App\Models\AcademicSession;
use App\Models\Student;
use App\Models\StudentResult;
use App\Models\StudentSemesterResult;
use Livewire\Component;

class ResultSlip extends Component
{
    public ?Student $student = null;

    public ?int $selectedSessionId = null;

    public int $selectedSemester = 1;

    public function mount(): void
    {
        $user = auth()->user();
        $this->student = ($user && $user->student) ? $user->student : Student::first();

        $session = AcademicSession::current();
        if ($session) {
            $this->selectedSessionId = $session->id;
        }
    }

    public function render()
    {
        $results = collect();
        $summary = null;

        if ($this->student && $this->selectedSessionId) {
            $results = StudentResult::where('student_id', $this->student->id)
                ->where('academic_session_id', $this->selectedSessionId)
                ->where('semester', $this->selectedSemester)
                ->with('course')
                ->get();

            $summary = StudentSemesterResult::where('student_id', $this->student->id)
                ->where('academic_session_id', $this->selectedSessionId)
                ->where('semester', $this->selectedSemester)
                ->first();
        }

        return view('livewire.student.result-slip', [
            'results' => $results,
            'summary' => $summary,
            'sessions' => AcademicSession::all(),
            'currentSession' => AcademicSession::find($this->selectedSessionId),
        ])->layout('layouts.institutional', ['title' => 'Official Semester Statement of Results']);
    }
}
