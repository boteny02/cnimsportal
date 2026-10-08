<?php

namespace App\Policies;

use App\Models\FinancialClearance;
use App\Models\User;

class FinancialClearancePolicy
{
    /**
     * Determine whether the user can view the financial clearance.
     */
    public function view(User $user, FinancialClearance $clearance): bool
    {
        if ($clearance->student && $clearance->student->user_id === $user->id) {
            return true;
        }

        return $user->hasPermissionTo('clearance.view')
            || $user->hasPermissionTo('finance.view');
    }

    /**
     * Determine whether the user can grant standard financial clearance.
     */
    public function approve(User $user, FinancialClearance $clearance): bool
    {
        return $user->hasPermissionTo('clearance.approve');
    }

    /**
     * Determine whether the user can reject clearance.
     */
    public function reject(User $user, FinancialClearance $clearance): bool
    {
        return $user->hasPermissionTo('clearance.reject');
    }

    /**
     * Determine whether the user can override financial clearance despite balance.
     * Enforces strict role restriction (Bursar, Registrar, Super Admin) with required audit log.
     */
    public function override(User $user, FinancialClearance $clearance): bool
    {
        if (! $user->hasPermissionTo('clearance.override')) {
            return false;
        }

        return $user->hasAnyRole('bursar', 'registrar', 'super_admin');
    }
}
