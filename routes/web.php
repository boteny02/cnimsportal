<?php

use App\Livewire\Admin\Academics\CourseManager;
use App\Livewire\Admin\Academics\SessionManager;
use App\Livewire\Admin\Admissions\ApplicationList;
use App\Livewire\Admin\AuditLogs\AuditLogViewer;
use App\Livewire\Admin\Clinical\LogbookReview;
use App\Livewire\Admin\Clinical\PostingManager;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Examinations\ScoreEntry;
use App\Livewire\Admin\Finance\FinanceManager;
use App\Livewire\Admin\Osce\StationScoring;
use App\Livewire\Admin\Students\StudentDirectory;
use App\Livewire\Applicant\ApplicantLogin;
use App\Livewire\Applicant\Dashboard as ApplicantDashboard;
use App\Livewire\Public\ApplicationStatusTracker;
use App\Livewire\Public\ApplicationWizard;
use App\Livewire\Public\CertificateVerification;
use App\Livewire\Student\ClinicalLogbook;
use App\Livewire\Student\CourseRegistration;
use App\Livewire\Student\Dashboard as StudentDashboard;
use App\Livewire\Student\FeesAndReceipts;
use App\Livewire\Student\ResultSlip;
use App\Models\Programme;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Portal Routes
|--------------------------------------------------------------------------
*/
Route::view('/', 'welcome')->name('home');
Route::get('/programmes', function () {
    return view('public.programmes', [
        'programmes' => Programme::where('is_active', true)->with('department')->get(),
    ]);
})->name('public.programmes');
Route::get('/apply', ApplicationWizard::class)->name('public.apply');
Route::get('/application-status', ApplicationStatusTracker::class)->name('public.application-status');
Route::get('/verify-certificate', CertificateVerification::class)->name('public.verify-certificate');

/*
|--------------------------------------------------------------------------
| Demo Persona Switcher (For Evaluation & Testing)
|--------------------------------------------------------------------------
*/
Route::post('/demo/switch-user', function (Request $request) {
    $request->validate(['email' => 'required|email']);
    $user = User::where('email', $request->email)->first();

    if ($user) {
        Auth::login($user);
        $roleName = $user->roles->pluck('display_name')->first() ?? 'User';
        $targetRoute = 'admin.dashboard';
        if ($user->hasRole('student')) {
            $targetRoute = 'student.dashboard';
        } elseif ($user->hasRole('applicant')) {
            $targetRoute = 'applicant.dashboard';
        }

        return redirect()->route($targetRoute)->with('success', "Active persona switched to {$user->name} ({$roleName}).");
    }

    return back()->with('error', 'Requested persona not found.');
})->name('demo.switch-user');

Route::get('/demo/switch/{email}', function (string $email) {
    $user = User::where('email', $email)->first();

    if ($user) {
        Auth::login($user);
        $roleName = $user->roles->pluck('display_name')->first() ?? 'User';
        $targetRoute = 'admin.dashboard';
        if ($user->hasRole('student')) {
            $targetRoute = 'student.dashboard';
        } elseif ($user->hasRole('applicant')) {
            $targetRoute = 'applicant.dashboard';
        }

        return redirect()->route($targetRoute)->with('success', "Active persona switched to {$user->name} ({$roleName}).");
    }

    return redirect()->route('home')->with('error', 'Requested persona not found.');
})->name('demo.switch-user.get');

/*
|--------------------------------------------------------------------------
| Authenticated Dashboard Redirector
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    if (Auth::check()) {
        if (Auth::user()->hasRole('student')) {
            return redirect()->route('student.dashboard');
        }
        if (Auth::user()->hasRole('applicant')) {
            return redirect()->route('applicant.dashboard');
        }
    }

    return redirect()->route('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Staff / Administrative Portal Routes (RBAC Protected)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboard::class)->name('dashboard');
    Route::get('/admissions', ApplicationList::class)->name('admissions');
    Route::get('/students', StudentDirectory::class)->name('students');
    Route::get('/sessions', SessionManager::class)->name('sessions');
    Route::get('/academics', CourseManager::class)->name('academics');
    Route::get('/examinations', ScoreEntry::class)->name('examinations');
    Route::get('/clinical', PostingManager::class)->name('clinical');
    Route::get('/clinical/logbook', LogbookReview::class)->name('clinical.logbook');
    Route::get('/osce', StationScoring::class)->name('osce');
    Route::get('/finance', FinanceManager::class)->name('finance');
    Route::get('/audit-logs', AuditLogViewer::class)->name('audit-logs');
});

/*
|--------------------------------------------------------------------------
| Student Portal Routes (Authenticated Students)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {
    Route::get('/', StudentDashboard::class)->name('dashboard');
    Route::get('/courses', CourseRegistration::class)->name('courses');
    Route::get('/results', ResultSlip::class)->name('results');
    Route::get('/clinical', ClinicalLogbook::class)->name('clinical');
    Route::get('/fees', FeesAndReceipts::class)->name('fees');
});

/*
|--------------------------------------------------------------------------
| Prospective Applicant Portal Routes
|--------------------------------------------------------------------------
*/
Route::get('/applicant/login', ApplicantLogin::class)->name('applicant.login');
Route::get('/applicant', function () {
    if (! Auth::check()) {
        return redirect()->route('applicant.login');
    }

    return redirect()->route('applicant.dashboard');
});
Route::middleware(['auth'])->prefix('applicant')->name('applicant.')->group(function () {
    Route::get('/dashboard', ApplicantDashboard::class)->name('dashboard');
});

require __DIR__.'/settings.php';
