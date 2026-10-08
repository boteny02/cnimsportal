<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    /**
     * Determine whether the user can view any applications.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('admissions.view');
    }

    /**
     * Determine whether the user can view the application.
     */
    public function view(User $user, Application $application): bool
    {
        // Layer 3: Applicant own record or staff with admissions.view permission
        if ($application->user_id && $application->user_id === $user->id) {
            return true;
        }

        return $user->hasPermissionTo('admissions.view');
    }

    /**
     * Determine whether the user can screen the applicant.
     */
    public function screen(User $user, Application $application): bool
    {
        // Layer 2: admissions.screen permission
        // Layer 4: State must be submitted, screened, or under_review
        return $user->hasPermissionTo('admissions.screen')
            && in_array($application->status, ['submitted', 'under_review', 'screened']);
    }

    /**
     * Determine whether the user can shortlist the applicant.
     */
    public function shortlist(User $user, Application $application): bool
    {
        return $user->hasPermissionTo('admissions.shortlist')
            && in_array($application->status, ['submitted', 'screened', 'under_review']);
    }

    /**
     * Determine whether the user can offer/approve admission.
     */
    public function approve(User $user, Application $application): bool
    {
        return $user->hasPermissionTo('admissions.approve')
            && in_array($application->status, ['submitted', 'screened', 'shortlisted']);
    }

    /**
     * Determine whether the user can reject the applicant.
     */
    public function reject(User $user, Application $application): bool
    {
        return $user->hasPermissionTo('admissions.reject')
            && ! in_array($application->status, ['admitted', 'matriculated']);
    }

    /**
     * Determine whether the user can update the application.
     */
    public function update(User $user, Application $application): bool
    {
        if ($application->user_id && $application->user_id === $user->id) {
            // Applicant can only edit while draft or initially submitted before offer
            return in_array($application->status, ['draft', 'submitted']);
        }

        return $user->hasPermissionTo('admissions.update');
    }
}
