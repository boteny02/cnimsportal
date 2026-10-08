<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    /**
     * Determine whether the user can view any student directories.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('students.view');
    }

    /**
     * Determine whether the user can view the student record.
     */
    public function view(User $user, Student $student): bool
    {
        // Layer 3 (Scope): Own record
        if ($student->user_id && $student->user_id === $user->id) {
            return true;
        }

        // Layer 2: Permission check
        if (! $user->hasPermissionTo('students.view')) {
            return false;
        }

        // Layer 3 (Scope): HOD can only view students in their department
        if ($user->hasRole('hod') && $user->department_id) {
            return $student->programme && $student->programme->department_id === $user->department_id;
        }

        return true;
    }

    /**
     * Determine whether the user can update the student profile.
     */
    public function update(User $user, Student $student): bool
    {
        if ($student->user_id && $student->user_id === $user->id) {
            return $user->hasPermissionTo('student.profile.update') || $user->hasPermissionTo('students.update_own');
        }

        return $user->hasPermissionTo('students.update');
    }

    /**
     * Determine whether the user can view student academic details / transcripts.
     */
    public function viewAcademic(User $user, Student $student): bool
    {
        if ($student->user_id && $student->user_id === $user->id) {
            return true;
        }

        if (! $user->hasPermissionTo('students.view_academic') && ! $user->hasPermissionTo('students.view')) {
            return false;
        }

        if ($user->hasRole('hod') && $user->department_id) {
            return $student->programme && $student->programme->department_id === $user->department_id;
        }

        return true;
    }

    /**
     * Determine whether the user can view student clinical details.
     */
    public function viewClinical(User $user, Student $student): bool
    {
        if ($student->user_id && $student->user_id === $user->id) {
            return true;
        }

        if ($user->hasRole('clinical_instructor')) {
            // Must be supervising at least one posting this student is assigned to
            return $student->clinicalPostings()
                ->where('supervisor_id', $user->id)
                ->exists();
        }

        return $user->hasPermissionTo('students.view_clinical')
            || $user->hasPermissionTo('clinical.view');
    }

    /**
     * Determine whether the user can view student financial records.
     */
    public function viewFinancial(User $user, Student $student): bool
    {
        if ($student->user_id && $student->user_id === $user->id) {
            return true;
        }

        return $user->hasPermissionTo('students.view_financial')
            || $user->hasPermissionTo('finance.view')
            || $user->hasPermissionTo('finance.dashboard');
    }
}
