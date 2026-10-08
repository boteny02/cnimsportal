<div class="py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <!-- Welcome banner -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 sm:text-3xl dark:text-white">
                    Institutional Operations Dashboard
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-zinc-500 dark:text-zinc-400">
                    Real-time metrics across Admissions, Academics, Hospital Rotations, Examinations, and Finance.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.admissions') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-sky-700 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-sky-600 transition">
                    Review Applications ({{ $stats['pending_applications'] }})
                </a>
                <a href="{{ route('admin.clinical.logbook') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-teal-700 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-teal-600 transition">
                    Pending Logbooks ({{ $stats['pending_logbooks'] }})
                </a>
            </div>
        </div>

        <!-- Metric Cards Grid -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-card title="Enrolled Students" :value="$stats['total_students']" sub="Accredited & Active" color="blue">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </x-stat-card>

            <x-stat-card title="Total Applications" :value="$stats['total_applicants']" :sub="$stats['pending_applications'] . ' Pending Screening'" color="indigo">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </x-stat-card>

            <x-stat-card title="Hospital Rotations" :value="$stats['active_postings']" :sub="$stats['pending_logbooks'] . ' Procedures Awaiting Sign-off'" color="emerald">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </x-stat-card>

            <x-stat-card title="Fee Revenue (YTD)" :value="'NGN ' . number_format($stats['total_revenue'], 0)" sub="Bursary & Collections" color="amber">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </x-stat-card>
        </div>

        <!-- Quick Access Module Cards -->
        <div class="mt-8">
            <h2 class="text-base font-bold text-zinc-900 dark:text-white">Institutional Workflow Hubs</h2>
            <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                
                <a href="{{ route('admin.admissions') }}" class="group relative overflow-hidden rounded-xl border border-zinc-200/80 bg-white p-5 hover:border-sky-500 hover:shadow-md transition dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-sky-100 p-2 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-zinc-900 group-hover:text-sky-600 dark:text-white">Admissions & Screening</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Review O'Levels, screen applicants, admit & matriculate students.</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.students') }}" class="group relative overflow-hidden rounded-xl border border-zinc-200/80 bg-white p-5 hover:border-sky-500 hover:shadow-md transition dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-emerald-100 p-2 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-zinc-900 group-hover:text-sky-600 dark:text-white">Student Directory & Profiles</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Comprehensive student dossiers, academic, clinical & fee histories.</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.examinations') }}" class="group relative overflow-hidden rounded-xl border border-zinc-200/80 bg-white p-5 hover:border-sky-500 hover:shadow-md transition dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-purple-100 p-2 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-zinc-900 group-hover:text-sky-600 dark:text-white">Examinations & GPA/CGPA</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Score entry sheet, HOD verification, board approval & publishing.</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.clinical') }}" class="group relative overflow-hidden rounded-xl border border-zinc-200/80 bg-white p-5 hover:border-sky-500 hover:shadow-md transition dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-teal-100 p-2 text-teal-700 dark:bg-teal-950/60 dark:text-teal-300">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-zinc-900 group-hover:text-sky-600 dark:text-white">Hospital Rotations & Wards</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Teaching hospital ward placements, capacity limits, preceptors.</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.osce') }}" class="group relative overflow-hidden rounded-xl border border-zinc-200/80 bg-white p-5 hover:border-sky-500 hover:shadow-md transition dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-rose-100 p-2 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-zinc-900 group-hover:text-sky-600 dark:text-white">OSCE Clinical Assessment</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Tablet-friendly OSCE stations, rubric checks & candidate scoring.</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.finance') }}" class="group relative overflow-hidden rounded-xl border border-zinc-200/80 bg-white p-5 hover:border-sky-500 hover:shadow-md transition dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-amber-100 p-2 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-zinc-900 group-hover:text-sky-600 dark:text-white">Finance & Clearance</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Tuition structures, invoice receipts, course registration clearance.</p>
                        </div>
                    </div>
                </a>

            </div>
        </div>

        <!-- Recent Audit Trail & Applications Two-Column -->
        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
            
            <!-- Recent Applications -->
            <div class="rounded-xl border border-zinc-200/90 bg-white p-5 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Recent Admission Applications</h3>
                    <a href="{{ route('admin.admissions') }}" class="text-xs font-semibold text-sky-600 hover:text-sky-500">View All</a>
                </div>
                <div class="mt-3 divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    @forelse($recentApplications as $app)
                        <div class="py-2.5 flex items-center justify-between text-xs">
                            <div>
                                <p class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $app->full_name }}</p>
                                <p class="text-zinc-500 dark:text-zinc-400 font-mono text-[11px]">{{ $app->application_number }} • {{ $app->programme?->name }}</p>
                            </div>
                            <div>
                                <x-badge :type="$app->status">{{ ucwords(str_replace('_', ' ', $app->status)) }}</x-badge>
                            </div>
                        </div>
                    @empty
                        <p class="py-4 text-center text-xs text-zinc-500">No applications recorded yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Audit Logs -->
            <div class="rounded-xl border border-zinc-200/90 bg-white p-5 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Institutional Audit Log</h3>
                    <a href="{{ route('admin.audit-logs') }}" class="text-xs font-semibold text-sky-600 hover:text-sky-500">View Audit Trail</a>
                </div>
                <div class="mt-3 divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    @forelse($recentLogs as $log)
                        <div class="py-2.5 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-mono font-semibold text-sky-700 dark:text-sky-300">{{ $log->action }}</span>
                                <span class="text-zinc-400 text-[11px]">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="mt-0.5 text-zinc-600 dark:text-zinc-300">{{ $log->description ?? 'Action performed by ' . ($log->user?->name ?? 'System') }}</p>
                        </div>
                    @empty
                        <p class="py-4 text-center text-xs text-zinc-500">No institutional events logged yet.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</div>
