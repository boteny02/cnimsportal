<?php

namespace App\Services\Clinical;

use App\Models\ClinicalLogbook;
use App\Models\ClinicalPosting;
use App\Models\CompetencySkill;
use App\Models\OsceScore;
use App\Models\OsceStation;
use App\Models\Student;
use App\Models\StudentCompetency;
use App\Models\User;
use App\Services\Audit\AuditService;
use Exception;

class ClinicalService
{
    public function assignStudentsToPosting(ClinicalPosting $posting, array $studentIds): void
    {
        $currentCount = $posting->students()->count();
        $newCount = count($studentIds);

        if ($currentCount + $newCount > $posting->max_capacity) {
            throw new Exception("Maximum clinical placement capacity ({$posting->max_capacity}) exceeded.");
        }

        $posting->students()->syncWithoutDetaching($studentIds);

        AuditService::log(
            'clinical.students_assigned',
            $posting,
            null,
            ['count' => $newCount],
            "Assigned {$newCount} student(s) to clinical rotation: {$posting->title}"
        );
    }

    public function logProcedure(Student $student, array $data): ClinicalLogbook
    {
        $entry = ClinicalLogbook::create([
            'student_id' => $student->id,
            'clinical_posting_id' => $data['clinical_posting_id'] ?? null,
            'procedure_id' => $data['procedure_id'],
            'procedure_date' => $data['procedure_date'],
            'patient_reference_code' => $data['patient_reference_code'] ?? null,
            'competency_level' => $data['competency_level'] ?? 'supervised',
            'student_reflection' => $data['student_reflection'] ?? null,
            'supervisor_id' => $data['supervisor_id'] ?? null,
            'status' => 'submitted',
        ]);

        AuditService::log(
            'clinical.procedure_logged',
            $entry,
            null,
            ['procedure_id' => $data['procedure_id'], 'level' => $entry->competency_level],
            "Clinical procedure logged by {$student->student_number}",
            $student->user
        );

        return $entry;
    }

    public function verifyLogbookEntry(ClinicalLogbook $logbook, bool $approved, ?string $remarks, User $supervisor): void
    {
        // Layer 3 (Scope): Clinical Instructor must be supervisor of the posting or assigned supervisor
        if ($supervisor && ! $supervisor->hasRole('super_admin') && $supervisor->hasRole('clinical_instructor')) {
            $isSupervisorOfPosting = $logbook->clinicalPosting && $logbook->clinicalPosting->supervisor_id === $supervisor->id;
            $isAssignedSupervisor = $logbook->supervisor_id === $supervisor->id;
            if (! $isSupervisorOfPosting && ! $isAssignedSupervisor) {
                abort(403, 'Access Denied: Instructor is not the designated supervisor for this clinical posting.');
            }
        }

        $old = ['status' => $logbook->status];
        $newStatus = $approved ? 'approved' : 'rejected';

        $logbook->update([
            'status' => $newStatus,
            'supervisor_remarks' => $remarks,
            'supervisor_id' => $supervisor->id,
            'verified_at' => now(),
        ]);

        AuditService::log(
            'clinical.logbook_verified',
            $logbook,
            $old,
            ['status' => $newStatus, 'remarks' => $remarks],
            "Logbook entry #{$logbook->id} for {$logbook->student->student_number} marked {$newStatus} by {$supervisor->name}",
            $supervisor
        );
    }

    public function updateStudentCompetency(Student $student, int $skillId, string $level, ?string $remarks, User $assessor): StudentCompetency
    {
        $skill = CompetencySkill::findOrFail($skillId);

        $competency = StudentCompetency::updateOrCreate(
            [
                'student_id' => $student->id,
                'skill_id' => $skill->id,
            ],
            [
                'current_level' => $level,
                'assessed_by' => $assessor->id,
                'assessed_at' => now(),
                'remarks' => $remarks,
            ]
        );

        AuditService::log(
            'clinical.competency_assessed',
            $competency,
            null,
            ['skill' => $skill->name, 'level' => $level],
            "Competency '{$skill->name}' for {$student->student_number} updated to {$level} by {$assessor->name}",
            $assessor
        );

        return $competency;
    }

    public function scoreOsceCandidate(
        OsceStation $station,
        Student $student,
        float $score,
        array $rubricBreakdown,
        ?string $comments,
        User $assessor
    ): OsceScore {
        $scoreEntry = OsceScore::updateOrCreate(
            [
                'osce_station_id' => $station->id,
                'student_id' => $student->id,
            ],
            [
                'assessor_id' => $assessor->id,
                'score_awarded' => $score,
                'max_possible' => $station->max_score,
                'rubric_breakdown' => $rubricBreakdown,
                'examiner_comments' => $comments,
            ]
        );

        AuditService::log(
            'osce.score_entered',
            $scoreEntry,
            null,
            ['station' => $station->title, 'score' => $score],
            "OSCE score ({$score}/{$station->max_score}) recorded for {$student->student_number} at {$station->title}",
            $assessor
        );

        return $scoreEntry;
    }
}
