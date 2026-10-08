<?php

namespace App\Livewire\Admin;

use App\Models\Application;
use App\Models\AuditLog;
use App\Models\ClinicalLogbook;
use App\Models\ClinicalPosting;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentResult;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'total_students' => Student::where('status', 'active')->count(),
            'total_applicants' => Application::count(),
            'pending_applications' => Application::whereIn('status', ['submitted', 'under_review'])->count(),
            'active_postings' => ClinicalPosting::where('status', 'active')->count(),
            'pending_logbooks' => ClinicalLogbook::where('status', 'submitted')->count(),
            'unprocessed_results' => StudentResult::whereIn('status', ['submitted_by_lecturer', 'verified_by_hod'])->count(),
            'total_revenue' => Payment::where('status', 'successful')->sum('amount'),
        ];

        $recentLogs = AuditLog::with('user')->latest()->take(6)->get();
        $recentApplications = Application::with('programme')->latest()->take(5)->get();

        return view('livewire.admin.dashboard', [
            'stats' => $stats,
            'recentLogs' => $recentLogs,
            'recentApplications' => $recentApplications,
        ])->layout('layouts.institutional', ['title' => 'Staff Administrative Dashboard']);
    }
}
