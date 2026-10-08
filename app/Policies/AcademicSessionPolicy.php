<?php

namespace App\Policies;

use App\Models\AcademicSession;
use App\Models\User;

class AcademicSessionPolicy
{
    /**
     * Determine whether the user can view sessions list.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('sessions.view') || $user->hasAnyRole('super_admin', 'admin', 'registrar', 'academic_officer', 'hod');
    }

    /**
     * Determine whether the user can view the specific session.
     */
    public function view(User $user, AcademicSession $session): bool
    {
        return $user->hasPermissionTo('sessions.view') || $user->hasAnyRole('super_admin', 'admin', 'registrar', 'academic_officer', 'hod');
    }

    /**
     * Determine whether the user can create a new session.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('sessions.create') || $user->hasAnyRole('super_admin', 'admin', 'registrar', 'academic_officer');
    }

    /**
     * Determine whether the user can update the session.
     */
    public function update(User $user, AcademicSession $session): bool
    {
        return $user->hasPermissionTo('sessions.update') || $user->hasAnyRole('super_admin', 'admin', 'registrar', 'academic_officer');
    }

    /**
     * Determine whether the user can activate the session.
     */
    public function activate(User $user, AcademicSession $session): bool
    {
        return $user->hasPermissionTo('sessions.activate') || $user->hasAnyRole('super_admin', 'admin', 'registrar', 'academic_officer');
    }

    /**
     * Determine whether the user can transition session to result processing.
     */
    public function beginResultProcessing(User $user, AcademicSession $session): bool
    {
        return $user->hasPermissionTo('sessions.begin_result_processing') || $user->hasAnyRole('super_admin', 'admin', 'registrar', 'academic_officer');
    }

    /**
     * Determine whether the user can close the session.
     */
    public function close(User $user, AcademicSession $session): bool
    {
        return $user->hasPermissionTo('sessions.close') || $user->hasAnyRole('super_admin', 'admin', 'registrar', 'academic_officer');
    }

    /**
     * Determine whether the user can archive the session.
     */
    public function archive(User $user, AcademicSession $session): bool
    {
        return $user->hasPermissionTo('sessions.archive') || $user->hasAnyRole('super_admin', 'admin', 'registrar');
    }

    /**
     * Determine whether the user has administrative override rights for sessions.
     */
    public function override(User $user): bool
    {
        return $user->hasPermissionTo('sessions.override') || $user->hasAnyRole('super_admin', 'admin', 'registrar');
    }
}
