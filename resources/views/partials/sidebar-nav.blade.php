@php
$user = auth()->user();
$isStaff = $user && !$user->hasRole('student') && !$user->hasRole('applicant') && (request()->is('admin*') || (!request()->is('student*') && !request()->is('applicant*')));
$isStudent = ($user && $user->hasRole('student')) || request()->is('student*');
$isApplicant = ($user && $user->hasRole('applicant')) || request()->is('applicant*');
$isPublic = request()->is('/') || request()->is('programmes*') || request()->is('apply*') || request()->is('application-status*') || request()->is('verify-certificate*');

// Force workspace mode based on route or user role
if (request()->is('admin*')) {
    $mode = 'admin';
} elseif (request()->is('student*')) {
    $mode = 'student';
} elseif (request()->is('applicant*')) {
    $mode = 'applicant';
} else {
    $mode = $user ? ($user->hasRole('student') ? 'student' : ($user->hasRole('applicant') ? 'applicant' : 'admin')) : 'public';
}
@endphp

<div class="flex h-full flex-col justify-between overflow-y-auto px-4 py-5">
    <div>
        <!-- Institutional Brand Logo & Title -->
        <div class="flex items-center gap-3 px-2 pb-5 border-b border-zinc-200 dark:border-zinc-800">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-sky-600 via-teal-600 to-emerald-500 text-white shadow-md group-hover:scale-105 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <div class="font-extrabold text-sm tracking-tight text-zinc-900 dark:text-white flex items-center gap-1.5">
                        CNIMS PORTAL
                        <span class="rounded bg-sky-100 px-1.5 py-0.2 text-[9px] font-bold text-sky-800 dark:bg-sky-950 dark:text-sky-300">v1.0</span>
                    </div>
                    <p class="text-[11px] font-medium text-zinc-500 dark:text-zinc-400 leading-tight">College of Nursing & Midwifery</p>
                    <span class="inline-flex items-center gap-1 mt-0.5 text-[9px] font-semibold text-emerald-600 dark:text-emerald-400">
                        <span class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        NMCN Accredited
                    </span>
                </div>
            </a>
        </div>

        <!-- User / Persona Identity Badge -->
        <div class="mt-4 px-2">
            @auth
                <div class="rounded-xl border border-zinc-200/80 bg-zinc-50/70 p-3 dark:border-zinc-800/80 dark:bg-zinc-800/40">
                    <div class="flex items-center gap-3">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-sky-600 text-white font-bold text-xs shadow-xs">
                            {{ substr(auth()->user()->name, 0, 2) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-zinc-900 truncate dark:text-white">{{ auth()->user()->name }}</p>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                @php
                                    $roleName = auth()->user()->roles->pluck('display_name')->first() ?? 'Staff Member';
                                    $roleSlug = auth()->user()->roles->pluck('name')->first() ?? 'staff';
                                @endphp
                                <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-[10px] font-semibold 
                                    {{ $roleSlug === 'student' ? 'bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-300' : ($roleSlug === 'applicant' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300' : 'bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300') }}">
                                    {{ $roleName }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2.5 pt-2 border-t border-zinc-200/60 dark:border-zinc-700/60 flex items-center justify-between text-[11px]">
                        <span class="text-zinc-500 dark:text-zinc-400 truncate max-w-[130px]">{{ auth()->user()->email }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="font-semibold text-rose-600 hover:text-rose-700 dark:text-rose-400 text-[10px] cursor-pointer">
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="rounded-xl border border-dashed border-zinc-300 bg-zinc-50/50 p-3 dark:border-zinc-800 dark:bg-zinc-900/40">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-zinc-900 dark:text-white">Applicant / Guest</p>
                            <p class="text-[10px] text-zinc-500 dark:text-zinc-400">Public Portal Access</p>
                        </div>
                        <a href="{{ route('login') }}" class="rounded-lg bg-sky-700 px-2.5 py-1 text-[11px] font-semibold text-white hover:bg-sky-600">
                            Login
                        </a>
                    </div>
                </div>
            @endauth
        </div>

        <!-- Portal Navigation Modes -->
        <nav class="mt-5 space-y-6 px-1">

            @if($mode === 'admin')
                <!-- ================= STAFF / ADMIN WORKSPACE ================= -->
                <div>
                    <p class="px-2 text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Overview</p>
                    <div class="mt-1 space-y-0.5">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-sky-50 text-sky-700 font-bold dark:bg-sky-950/70 dark:text-sky-300 border border-sky-100 dark:border-sky-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Executive Dashboard
                        </a>
                    </div>
                </div>

                <div>
                    <p class="px-2 text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Admissions & Registry</p>
                    <div class="mt-1 space-y-0.5">
                        <a href="{{ route('admin.admissions') }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.admissions*') ? 'bg-sky-50 text-sky-700 font-bold dark:bg-sky-950/70 dark:text-sky-300 border border-sky-100 dark:border-sky-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <div class="flex items-center gap-3">
                                <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Admissions Screening</span>
                            </div>
                            <span class="rounded bg-sky-100 px-1.5 py-0.2 text-[10px] font-bold text-sky-800 dark:bg-sky-900 dark:text-sky-200">Screen</span>
                        </a>

                        <a href="{{ route('admin.students') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.students*') ? 'bg-sky-50 text-sky-700 font-bold dark:bg-sky-950/70 dark:text-sky-300 border border-sky-100 dark:border-sky-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Student Registry Dossiers
                        </a>

                        <a href="{{ route('admin.sessions') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.sessions*') ? 'bg-sky-50 text-sky-700 font-bold dark:bg-sky-950/70 dark:text-sky-300 border border-sky-100 dark:border-sky-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Academic Sessions & Terms
                        </a>

                        <a href="{{ route('admin.academics') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.academics*') ? 'bg-sky-50 text-sky-700 font-bold dark:bg-sky-950/70 dark:text-sky-300 border border-sky-100 dark:border-sky-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Courses & Prerequisites
                        </a>
                    </div>
                </div>

                <div>
                    <p class="px-2 text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Examinations & Grading</p>
                    <div class="mt-1 space-y-0.5">
                        <a href="{{ route('admin.examinations') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.examinations*') ? 'bg-sky-50 text-sky-700 font-bold dark:bg-sky-950/70 dark:text-sky-300 border border-sky-100 dark:border-sky-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Exam Scores & GPA Board
                        </a>
                    </div>
                </div>

                <div>
                    <p class="px-2 text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Hospital Rotations & OSCE</p>
                    <div class="mt-1 space-y-0.5">
                        <a href="{{ route('admin.clinical') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.clinical') ? 'bg-sky-50 text-sky-700 font-bold dark:bg-sky-950/70 dark:text-sky-300 border border-sky-100 dark:border-sky-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Ward Postings & Allocations
                        </a>

                        <a href="{{ route('admin.clinical.logbook') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.clinical.logbook*') ? 'bg-sky-50 text-sky-700 font-bold dark:bg-sky-950/70 dark:text-sky-300 border border-sky-100 dark:border-sky-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Preceptor Logbook Review
                        </a>

                        <a href="{{ route('admin.osce') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.osce*') ? 'bg-sky-50 text-sky-700 font-bold dark:bg-sky-950/70 dark:text-sky-300 border border-sky-100 dark:border-sky-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                            OSCE Station Tablet Scoring
                        </a>
                    </div>
                </div>

                <div>
                    <p class="px-2 text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Finance & Compliance</p>
                    <div class="mt-1 space-y-0.5">
                        <a href="{{ route('admin.finance') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.finance*') ? 'bg-sky-50 text-sky-700 font-bold dark:bg-sky-950/70 dark:text-sky-300 border border-sky-100 dark:border-sky-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Finance & Fee Clearance
                        </a>

                        <a href="{{ route('admin.audit-logs') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.audit-logs*') ? 'bg-sky-50 text-sky-700 font-bold dark:bg-sky-950/70 dark:text-sky-300 border border-sky-100 dark:border-sky-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Institutional Audit Trail
                        </a>
                    </div>
                </div>

                <div class="pt-2 border-t border-zinc-200 dark:border-zinc-800">
                    <p class="px-2 text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">External & Student Portals</p>
                    <div class="mt-1 space-y-0.5">
                        <a href="{{ route('student.dashboard') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-teal-600 hover:bg-teal-50 dark:text-teal-400 dark:hover:bg-teal-950/40 transition">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            </svg>
                            Preview Student Portal
                        </a>
                        <a href="{{ route('applicant.dashboard') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-indigo-600 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-indigo-950/40 transition">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Applicant Dashboard
                        </a>
                        <a href="{{ route('home') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60 transition">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Public Website
                        </a>
                    </div>
                </div>

            @elseif($mode === 'student')
                <!-- ================= STUDENT PORTAL WORKSPACE ================= -->
                <div>
                    <p class="px-2 text-[10px] font-bold uppercase tracking-wider text-teal-600 dark:text-teal-400">Student Center</p>
                    <div class="mt-1 space-y-0.5">
                        <a href="{{ route('student.dashboard') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('student.dashboard') ? 'bg-teal-50 text-teal-700 font-bold dark:bg-teal-950/70 dark:text-teal-300 border border-teal-100 dark:border-teal-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Student Overview
                        </a>
                    </div>
                </div>

                <div>
                    <p class="px-2 text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Academics</p>
                    <div class="mt-1 space-y-0.5">
                        <a href="{{ route('student.courses') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('student.courses*') ? 'bg-teal-50 text-teal-700 font-bold dark:bg-teal-950/70 dark:text-teal-300 border border-teal-100 dark:border-teal-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Course Registration
                        </a>

                        <a href="{{ route('student.results') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('student.results*') ? 'bg-teal-50 text-teal-700 font-bold dark:bg-teal-950/70 dark:text-teal-300 border border-teal-100 dark:border-teal-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Result Slip & GPA
                        </a>
                    </div>
                </div>

                <div>
                    <p class="px-2 text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Clinical Training</p>
                    <div class="mt-1 space-y-0.5">
                        <a href="{{ route('student.clinical') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('student.clinical*') ? 'bg-teal-50 text-teal-700 font-bold dark:bg-teal-950/70 dark:text-teal-300 border border-teal-100 dark:border-teal-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Digital Logbook & Competencies
                        </a>
                    </div>
                </div>

                <div>
                    <p class="px-2 text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Bursary</p>
                    <div class="mt-1 space-y-0.5">
                        <a href="{{ route('student.fees') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('student.fees*') ? 'bg-teal-50 text-teal-700 font-bold dark:bg-teal-950/70 dark:text-teal-300 border border-teal-100 dark:border-teal-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Fees & Financial Clearance
                        </a>
                    </div>
                </div>

                <div class="pt-2 border-t border-zinc-200 dark:border-zinc-800">
                    <p class="px-2 text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Institutional</p>
                    <div class="mt-1 space-y-0.5">
                        <a href="{{ route('home') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60 transition">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            College Home
                        </a>
                        <a href="{{ route('public.programmes') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60 transition">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            </svg>
                            Programmes Catalog
                        </a>
                    </div>
                </div>

            @elseif($mode === 'applicant')
                <!-- ================= APPLICANT PORTAL WORKSPACE ================= -->
                <div>
                    <p class="px-2 text-[10px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Applicant Center</p>
                    <div class="mt-1 space-y-0.5">
                        <a href="{{ route('applicant.dashboard') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('applicant.dashboard') ? 'bg-indigo-50 text-indigo-700 font-bold dark:bg-indigo-950/70 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Admissions Dashboard
                        </a>
                    </div>
                </div>

                <div>
                    <p class="px-2 text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Admissions Actions</p>
                    <div class="mt-1 space-y-0.5">
                        <a href="{{ route('public.apply') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('public.apply') ? 'bg-indigo-50 text-indigo-700 font-bold dark:bg-indigo-950/70 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Application Wizard
                        </a>

                        <a href="{{ route('public.application-status') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('public.application-status') ? 'bg-indigo-50 text-indigo-700 font-bold dark:bg-indigo-950/70 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Track Status & Code
                        </a>
                    </div>
                </div>

                <div class="pt-2 border-t border-zinc-200 dark:border-zinc-800">
                    <p class="px-2 text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">General Information</p>
                    <div class="mt-1 space-y-0.5">
                        <a href="{{ route('home') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60 transition">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            College Home
                        </a>
                        <a href="{{ route('public.programmes') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60 transition">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            </svg>
                            Programmes & Requirements
                        </a>
                    </div>
                </div>

            @else
                <!-- ================= PUBLIC / ADMISSIONS WORKSPACE ================= -->
                <div>
                    <p class="px-2 text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Public Portal</p>
                    <div class="mt-1 space-y-0.5">
                        <a href="{{ route('home') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('home') ? 'bg-sky-50 text-sky-700 font-bold dark:bg-sky-950/70 dark:text-sky-300 border border-sky-100 dark:border-sky-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            College Welcome
                        </a>

                        <a href="{{ route('public.programmes') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('public.programmes') ? 'bg-sky-50 text-sky-700 font-bold dark:bg-sky-950/70 dark:text-sky-300 border border-sky-100 dark:border-sky-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Academic Programmes
                        </a>

                        <a href="{{ route('public.apply') }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('public.apply') ? 'bg-sky-50 text-sky-700 font-bold dark:bg-sky-950/70 dark:text-sky-300 border border-sky-100 dark:border-sky-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <div class="flex items-center gap-3">
                                <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                <span>Admissions & Apply</span>
                            </div>
                            <span class="rounded bg-sky-600 px-1.5 py-0.2 text-[9px] font-bold text-white shadow-2xs">2025/2026</span>
                        </a>

                        <a href="{{ route('public.application-status') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('public.application-status') ? 'bg-sky-50 text-sky-700 font-bold dark:bg-sky-950/70 dark:text-sky-300 border border-sky-100 dark:border-sky-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Track Application Status
                        </a>

                        <a href="{{ route('public.verify-certificate') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('public.verify-certificate') ? 'bg-sky-50 text-sky-700 font-bold dark:bg-sky-950/70 dark:text-sky-300 border border-sky-100 dark:border-sky-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Registry & Credential Verification
                        </a>
                    </div>
                </div>

                <div class="pt-2 border-t border-zinc-200 dark:border-zinc-800">
                    <p class="px-2 text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Applicant & Secure Portals</p>
                    <div class="mt-1 space-y-0.5">
                        <a href="{{ route('applicant.login') }}" 
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('applicant*') ? 'bg-indigo-50 text-indigo-700 font-bold dark:bg-indigo-950/70 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-900/50' : 'text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800/60' }}">
                            <div class="flex items-center gap-3">
                                <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                <span>Applicant Portal Login</span>
                            </div>
                            <span class="rounded bg-indigo-600 px-1.5 py-0.2 text-[9px] font-bold text-white shadow-2xs">Portal</span>
                        </a>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-sky-700 hover:bg-sky-50 dark:text-sky-300 dark:hover:bg-sky-950/40 transition">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Staff Administrative Portal
                        </a>
                        <a href="{{ route('student.dashboard') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold text-teal-700 hover:bg-teal-50 dark:text-teal-300 dark:hover:bg-teal-950/40 transition">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            </svg>
                            Student Center Portal
                        </a>
                    </div>
                </div>
            @endif

        </nav>
    </div>

    <!-- Sidebar Bottom Footer -->
    <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800">
        <div class="rounded-xl bg-zinc-50 p-2.5 dark:bg-zinc-800/40 border border-zinc-200/50 dark:border-zinc-800">
            <div class="flex items-center justify-between text-[11px] text-zinc-500 dark:text-zinc-400">
                <span class="font-bold">Portal Mode:</span>
                <span class="font-semibold uppercase tracking-wider text-[10px] rounded px-1.5 py-0.2
                    {{ $mode === 'admin' ? 'bg-sky-100 text-sky-800 dark:bg-sky-900 dark:text-sky-200' : ($mode === 'student' ? 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200' : ($mode === 'applicant' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200' : 'bg-zinc-200 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-200')) }}">
                    {{ ucfirst($mode) }}
                </span>
            </div>
            <p class="mt-1 text-[10px] text-zinc-400 dark:text-zinc-500 leading-tight">
                Nursing & Midwifery Council of Nigeria Regulatory Standards
            </p>
        </div>
    </div>
</div>
