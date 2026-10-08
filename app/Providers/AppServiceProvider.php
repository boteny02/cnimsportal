<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\ClinicalLogbook;
use App\Models\Course;
use App\Models\FinancialClearance;
use App\Models\Student;
use App\Models\StudentResult;
use App\Policies\ApplicationPolicy;
use App\Policies\ClinicalLogbookPolicy;
use App\Policies\CoursePolicy;
use App\Policies\FinancialClearancePolicy;
use App\Policies\StudentPolicy;
use App\Policies\StudentResultPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->isProduction() || isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || env('VERCEL')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        $this->configureDefaults();

        // Register Model Policies explicitly
        Gate::policy(Application::class, ApplicationPolicy::class);
        Gate::policy(Student::class, StudentPolicy::class);
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(StudentResult::class, StudentResultPolicy::class);
        Gate::policy(ClinicalLogbook::class, ClinicalLogbookPolicy::class);
        Gate::policy(FinancialClearance::class, FinancialClearancePolicy::class);

        // Layer 1/Bypass: Super admin has full institutional bypass
        Gate::before(function ($user, $ability) {
            if ($user && method_exists($user, 'hasRole') && $user->hasRole('super_admin')) {
                return true;
            }

            return null; // Defer to model policies and scoped authorization
        });

        // Layer 2 Fallback: If no model instance was passed, check module.action permission
        Gate::after(function ($user, $ability, $result, $arguments) {
            if ($result !== null) {
                return $result;
            }

            if (empty($arguments) && $user && method_exists($user, 'hasPermissionTo')) {
                return $user->hasPermissionTo($ability);
            }

            return false;
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
