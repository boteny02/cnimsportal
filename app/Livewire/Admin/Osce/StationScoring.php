<?php

namespace App\Livewire\Admin\Osce;

use App\Models\OsceExamination;
use App\Models\OsceScore;
use App\Models\OsceStation;
use App\Models\Student;
use App\Services\Clinical\ClinicalService;
use Livewire\Component;

class StationScoring extends Component
{
    public ?int $selectedExamId = null;

    public ?int $selectedStationId = null;

    public ?int $selectedStudentId = null;

    // Rubric marks keyed by rubric id
    public array $rubricScores = [];

    public string $examinerComments = 'Good bedside manners, verified 2 patient identifiers, adhered to aseptic hand hygiene protocols.';

    public function mount(): void
    {
        $exam = OsceExamination::first();
        if ($exam) {
            $this->selectedExamId = $exam->id;
            $station = OsceStation::where('osce_examination_id', $exam->id)->first();
            if ($station) {
                $this->selectedStationId = $station->id;
            }
        }

        $student = Student::first();
        if ($student) {
            $this->selectedStudentId = $student->id;
        }

        $this->loadRubrics();
    }

    public function updatedSelectedStationId(): void
    {
        $this->loadRubrics();
    }

    public function updatedSelectedStudentId(): void
    {
        $this->loadRubrics();
    }

    public function loadRubrics(): void
    {
        $this->rubricScores = [];
        if (! $this->selectedStationId) {
            return;
        }

        $station = OsceStation::with('rubrics')->find($this->selectedStationId);
        if (! $station) {
            return;
        }

        // Check if student has previous score
        if ($this->selectedStudentId) {
            $prev = OsceScore::where('osce_station_id', $station->id)
                ->where('student_id', $this->selectedStudentId)
                ->first();

            if ($prev && is_array($prev->rubric_breakdown)) {
                $this->rubricScores = $prev->rubric_breakdown;
                $this->examinerComments = $prev->examiner_comments ?? '';

                return;
            }
        }

        foreach ($station->rubrics as $rub) {
            $this->rubricScores[$rub->id] = (float) $rub->max_score;
        }
    }

    public function saveScore(ClinicalService $service): void
    {
        $this->validate([
            'selectedStationId' => 'required|exists:osce_stations,id',
            'selectedStudentId' => 'required|exists:students,id',
        ]);

        $station = OsceStation::with('rubrics')->findOrFail($this->selectedStationId);
        $student = Student::findOrFail($this->selectedStudentId);

        $total = 0.0;
        foreach ($station->rubrics as $rub) {
            $val = min($rub->max_score, max(0.0, (float) ($this->rubricScores[$rub->id] ?? 0)));
            $this->rubricScores[$rub->id] = $val;
            $total += $val;
        }

        $service->scoreOsceCandidate(
            $station,
            $student,
            $total,
            $this->rubricScores,
            $this->examinerComments,
            auth()->user()
        );

        session()->flash('success', "OSCE score ({$total}/{$station->max_score}) saved for {$student->student_number}.");
    }

    public function render()
    {
        $station = OsceStation::with(['rubrics', 'examination'])->find($this->selectedStationId);
        $student = Student::find($this->selectedStudentId);
        $stations = OsceStation::where('osce_examination_id', $this->selectedExamId)->get();

        $currentScore = 0.0;
        if ($station) {
            foreach ($station->rubrics as $rub) {
                $currentScore += (float) ($this->rubricScores[$rub->id] ?? 0);
            }
        }

        return view('livewire.admin.osce.station-scoring', [
            'station' => $station,
            'stations' => $stations,
            'students' => Student::where('status', 'active')->get(),
            'currentScore' => $currentScore,
            'examinations' => OsceExamination::all(),
        ])->layout('layouts.institutional', ['title' => 'OSCE Tablet Assessor Scoring']);
    }
}
