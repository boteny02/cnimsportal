<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Determine whether the user can view any courses.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('courses.view') || $user->hasPermissionTo('student.registration.view');
    }

    /**
     * Determine whether the user can view the course.
     */
    public function view(User $user, Course $course): bool
    {
        return $user->hasPermissionTo('courses.view') || $user->hasPermissionTo('student.registration.view');
    }

    /**
     * Determine whether the user can manage/edit the course.
     */
    public function manage(User $user, Course $course): bool
    {
        if ($user->hasRole('hod') && $user->department_id) {
            return $course->department_id === $user->department_id;
        }

        return $user->hasPermissionTo('courses.manage') || $user->hasPermissionTo('courses.update');
    }

    /**
     * Determine whether the user can enter scores for this course.
     * Enforces Layer 2 (Permission), Layer 3 (Lecturer Assignment Scope), and Layer 4 (Course Active).
     */
    public function enterScores(User $user, Course $course): bool
    {
        // Layer 2: results.enter permission
        if (! $user->hasPermissionTo('results.enter')) {
            return false;
        }

        // Layer 4: Course must be active
        if (! $course->is_active) {
            return false;
        }

        // Layer 3: Scope check
        // If user is Lecturer, must be assigned to this course
        if ($user->hasRole('lecturer')) {
            return $course->lecturer_id === $user->id;
        }

        // HOD can enter/supervise scores for courses in their department
        if ($user->hasRole('hod') && $user->department_id) {
            return $course->department_id === $user->department_id;
        }

        // Admin, super_admin, or exam_officer
        return $user->hasAnyRole('super_admin', 'admin', 'exam_officer');
    }
}
