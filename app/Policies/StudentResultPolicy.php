<?php

namespace App\Policies;

use App\Enums\ResultStatus;
use App\Models\StudentResult;
use App\Models\User;

class StudentResultPolicy
{
    /**
     * Determine whether the user can view results.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('results.view')
            || $user->hasPermissionTo('student.results.view');
    }

    /**
     * Determine whether the user can view this specific result.
     * Enforces Layer 3 (Student ownership) and Layer 4 (Only published for students).
     */
    public function view(User $user, StudentResult $result): bool
    {
        // Student can ONLY view their own published results
        if ($result->student && $result->student->user_id === $user->id) {
            return $this->getStatusValue($result) === ResultStatus::PUBLISHED->value;
        }

        return $user->hasPermissionTo('results.view');
    }

    /**
     * Determine whether the user can enter/update the result score.
     * Enforces Layer 2 (Permission), Layer 3 (Assigned Lecturer), and Layer 4 (Draft state only).
     */
    public function update(User $user, StudentResult $result): bool
    {
        // Layer 2: results.enter or results.update
        if (! $user->hasPermissionTo('results.enter') && ! $user->hasPermissionTo('results.update')) {
            return false;
        }

        $status = $this->getStatusValue($result);

        // Layer 4: Workflow State - Must be DRAFT or CORRECTION_REQUESTED
        if ($status !== ResultStatus::DRAFT->value && $status !== ResultStatus::CORRECTION_REQUESTED->value) {
            return false;
        }

        // Layer 3: Scope check
        if ($user->hasRole('lecturer')) {
            return $result->course && $result->course->lecturer_id === $user->id;
        }

        if ($user->hasRole('hod') && $user->department_id) {
            return $result->course && $result->course->department_id === $user->department_id;
        }

        return $user->hasAnyRole('super_admin', 'admin', 'exam_officer');
    }

    /**
     * Determine whether the user can submit the result to HOD.
     */
    public function submit(User $user, StudentResult $result): bool
    {
        if (! $user->hasPermissionTo('results.submit')) {
            return false;
        }

        $status = $this->getStatusValue($result);
        if ($status !== ResultStatus::DRAFT->value && $status !== ResultStatus::CORRECTION_REQUESTED->value) {
            return false;
        }

        if ($user->hasRole('lecturer')) {
            return $result->course && $result->course->lecturer_id === $user->id;
        }

        return $user->hasAnyRole('super_admin', 'admin', 'hod');
    }

    /**
     * Determine whether the user can verify the result.
     * Enforces HOD department scope and submitted_by_lecturer status.
     */
    public function verify(User $user, StudentResult $result): bool
    {
        if (! $user->hasPermissionTo('results.verify')) {
            return false;
        }

        // Layer 4: State must be submitted_by_lecturer
        if ($this->getStatusValue($result) !== ResultStatus::SUBMITTED->value) {
            return false;
        }

        // Layer 3: HOD must match department of the course
        if ($user->hasRole('hod') && $user->department_id) {
            return $result->course && $result->course->department_id === $user->department_id;
        }

        return $user->hasAnyRole('super_admin', 'admin', 'exam_officer');
    }

    /**
     * Determine whether the user can approve the result.
     */
    public function approve(User $user, StudentResult $result): bool
    {
        if (! $user->hasPermissionTo('results.approve')) {
            return false;
        }

        // Layer 4: Must be verified before board approval
        if (! in_array($this->getStatusValue($result), [ResultStatus::VERIFIED->value, ResultStatus::PROCESSED->value], true)) {
            return false;
        }

        return $user->hasAnyRole('super_admin', 'admin', 'registrar', 'dean');
    }

    /**
     * Determine whether the user can publish the result.
     */
    public function publish(User $user, StudentResult $result): bool
    {
        if (! $user->hasPermissionTo('results.publish')) {
            return false;
        }

        // Layer 4: Must be approved before publication
        if ($this->getStatusValue($result) !== ResultStatus::APPROVED->value) {
            return false;
        }

        return $user->hasAnyRole('super_admin', 'admin', 'exam_officer');
    }

    /**
     * Extract string status whether model returns an enum or string.
     */
    protected function getStatusValue(StudentResult $result): string
    {
        if ($result->status instanceof ResultStatus) {
            return $result->status->value;
        }

        return (string) $result->status;
    }
}
