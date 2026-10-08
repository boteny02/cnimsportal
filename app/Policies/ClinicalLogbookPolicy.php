<?php

namespace App\Policies;

use App\Models\ClinicalLogbook;
use App\Models\User;

class ClinicalLogbookPolicy
{
    /**
     * Determine whether the user can view the clinical logbook entry.
     */
    public function view(User $user, ClinicalLogbook $logbook): bool
    {
        // Student view own logbook
        if ($logbook->student && $logbook->student->user_id === $user->id) {
            return true;
        }

        // Clinical Instructor: Scope check (must be assigned supervisor of posting)
        if ($user->hasRole('clinical_instructor')) {
            $isSupervisorOfPosting = $logbook->clinicalPosting && $logbook->clinicalPosting->supervisor_id === $user->id;
            $isAssignedSupervisor = $logbook->supervisor_id === $user->id;

            return $isSupervisorOfPosting || $isAssignedSupervisor;
        }

        return $user->hasPermissionTo('clinical.logbook.view')
            || $user->hasPermissionTo('clinical.view');
    }

    /**
     * Determine whether the student can update/edit the logbook entry.
     */
    public function update(User $user, ClinicalLogbook $logbook): bool
    {
        if ($logbook->student && $logbook->student->user_id === $user->id) {
            return in_array($logbook->status, ['draft', 'rejected']);
        }

        return false;
    }

    /**
     * Determine whether the user can review/sign-off the logbook entry.
     * Enforces Layer 2 (Permission), Layer 3 (Supervisor of posting), Layer 4 (Status submitted).
     */
    public function review(User $user, ClinicalLogbook $logbook): bool
    {
        // Layer 2
        if (! $user->hasPermissionTo('clinical.logbook.review') && ! $user->hasPermissionTo('clinical.logbook.approve')) {
            return false;
        }

        // Layer 4: Must be submitted
        if ($logbook->status !== 'submitted') {
            return false;
        }

        // Layer 3: Scope check
        if ($user->hasRole('clinical_instructor')) {
            $isSupervisorOfPosting = $logbook->clinicalPosting && $logbook->clinicalPosting->supervisor_id === $user->id;
            $isAssignedSupervisor = $logbook->supervisor_id === $user->id;

            return $isSupervisorOfPosting || $isAssignedSupervisor;
        }

        return $user->hasAnyRole('clinical_coordinator', 'super_admin', 'admin');
    }

    /**
     * Determine whether the user can approve the logbook entry.
     */
    public function approve(User $user, ClinicalLogbook $logbook): bool
    {
        if (! $user->hasPermissionTo('clinical.logbook.approve')) {
            return false;
        }

        if ($logbook->status !== 'submitted') {
            return false;
        }

        if ($user->hasRole('clinical_instructor')) {
            $isSupervisorOfPosting = $logbook->clinicalPosting && $logbook->clinicalPosting->supervisor_id === $user->id;
            $isAssignedSupervisor = $logbook->supervisor_id === $user->id;

            return $isSupervisorOfPosting || $isAssignedSupervisor;
        }

        return $user->hasAnyRole('clinical_coordinator', 'super_admin', 'admin');
    }
}
