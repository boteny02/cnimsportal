<div class="py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        @if(!$student)
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-6 text-center text-amber-800 dark:border-amber-900/40 dark:bg-amber-950/20">
                <p class="font-bold">No active student profile linked to this account.</p>
                <p class="mt-1 text-xs">Please switch to the Student Persona using the top persona bar to experience the student interface.</p>
            </div>
        @else
            <!-- Student Header Profile Banner -->
            <div class="mb-8 rounded-2xl border border-teal-200 bg-gradient-to-r from-teal-900 via-teal-800 to-sky-900 p-6 text-white shadow-md dark:border-teal-900">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="flex size-16 items-center justify-center rounded-2xl bg-white/10 text-2xl font-black text-teal-200 backdrop-blur-xs border border-white/20">
                            {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-xl font-black sm:text-2xl">{{ $student->full_name }}</h1>
                                <span class="rounded bg-teal-500/30 px-2 py-0.5 text-xs font-semibold text-teal-200 border border-teal-400/30">
                                    {{ $student->currentLevel?->code }}
                                </span>
                            </div>
                            <p class="mt-0.5 text-xs text-teal-200 font-mono tracking-wide">
                                Matric No: <strong>{{ $student->student_number }}</strong> • {{ $student->programme?->name }} ({{ $student->programme?->degree_type }})
                            </p>
                            <p class="text-[11px] text-teal-300/80 mt-1">
                                Department of Nursing Sciences • {{ $session?->name }} Academic Session
                            </p>
                        </div>
                    </div>

                    <div class="flex sm:flex-col items-end gap-2 text-right">
                        <div>
                            <span class="text-[10px] text-teal-300 font-bold uppercase tracking-wider">Financial Clearance</span>
                            <div>
                                @if($isCleared)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/20 px-2.5 py-0.5 text-xs font-bold text-emerald-300 border border-emerald-500/30">
                                        ✓ Fee Cleared
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-500/20 px-2.5 py-0.5 text-xs font-bold text-amber-300 border border-amber-500/30">
                                        Pending Payment
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <span class="text-[10px] text-teal-300 font-bold uppercase tracking-wider">Cumulative Standing</span>
                            <div class="font-mono text-sm font-bold text-white">
                                CGPA: {{ $semesterSummary?->cgpa ?? '5.00' }} / 5.0
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Stats Row -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <x-stat-card title="Registered Courses" :value="$student->courseRegistrations()->where('academic_session_id', $session?->id)->first()?->total_credits ?? '10 Units'" sub="First Semester" color="blue">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </x-stat-card>

                <x-stat-card title="Active Ward Rotation" :value="$activePosting?->ward?->name ?? 'Medical Ward'" :sub="$activePosting?->facility?->name ?? 'Teaching Hospital'" color="emerald">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </x-stat-card>

                <x-stat-card title="Procedures Logged" :value="$student->logbooks()->count()" :sub="$student->logbooks()->where('status', 'approved')->count() . ' Approved by Preceptor'" color="indigo">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </x-stat-card>

                <x-stat-card title="Academic Standing" :value="$semesterSummary?->academic_standing ?? 'Good Standing'" sub="Pass Threshold: 50%" color="emerald">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </x-stat-card>
            </div>

            <!-- Student Action Hub -->
            <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('student.courses') }}" class="group rounded-xl border border-zinc-200 bg-white p-4 shadow-2xs hover:border-teal-500 hover:shadow-md transition dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-teal-50 p-2 text-teal-600 dark:bg-teal-950 dark:text-teal-300">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-zinc-900 group-hover:text-teal-600 dark:text-white">Course Registration</h3>
                            <p class="text-[11px] text-zinc-500">Register courses for semester</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('student.results') }}" class="group rounded-xl border border-zinc-200 bg-white p-4 shadow-2xs hover:border-teal-500 hover:shadow-md transition dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-purple-50 p-2 text-purple-600 dark:bg-purple-950 dark:text-purple-300">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-zinc-900 group-hover:text-purple-600 dark:text-white">Official Result Slip</h3>
                            <p class="text-[11px] text-zinc-500">View statement of grades</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('student.clinical') }}" class="group rounded-xl border border-zinc-200 bg-white p-4 shadow-2xs hover:border-teal-500 hover:shadow-md transition dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-emerald-50 p-2 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-300">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-zinc-900 group-hover:text-emerald-600 dark:text-white">Clinical Logbook</h3>
                            <p class="text-[11px] text-zinc-500">Log procedural experiences</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('student.fees') }}" class="group rounded-xl border border-zinc-200 bg-white p-4 shadow-2xs hover:border-teal-500 hover:shadow-md transition dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-amber-50 p-2 text-amber-600 dark:bg-amber-950 dark:text-amber-300">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-zinc-900 group-hover:text-amber-600 dark:text-white">Tuition & Receipts</h3>
                            <p class="text-[11px] text-zinc-500">Pay fees & download receipts</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Two-Column Recent Logbook & Recent Results -->
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                
                <!-- Recent Logbook Entries -->
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Recent Clinical Procedures Logged</h3>
                        <a href="{{ route('student.clinical') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-500">View Full Logbook</a>
                    </div>
                    <div class="mt-3 divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse($recentLogbooks as $log)
                            <div class="py-2.5 flex items-center justify-between text-xs">
                                <div>
                                    <p class="font-semibold text-zinc-900 dark:text-white">{{ $log->procedure?->title }}</p>
                                    <p class="text-[11px] text-zinc-500">{{ $log->procedure_date?->format('d M Y') }} • Patient: {{ $log->patient_reference_code }}</p>
                                </div>
                                <div class="text-right">
                                    <x-badge :type="$log->status">{{ ucfirst($log->status) }}</x-badge>
                                </div>
                            </div>
                        @empty
                            <p class="py-4 text-center text-xs text-zinc-400">No procedures logged yet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Results -->
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Published Academic Results</h3>
                        <a href="{{ route('student.results') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-500">Full Result Slip</a>
                    </div>
                    <div class="mt-3 divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse($latestResults as $res)
                            <div class="py-2.5 flex items-center justify-between text-xs">
                                <div>
                                    <p class="font-semibold text-zinc-900 dark:text-white">{{ $res->course?->code }} - {{ $res->course?->title }}</p>
                                    <p class="text-[11px] text-zinc-500">{{ $res->course?->credit_units }} Credit Units</p>
                                </div>
                                <div class="text-right">
                                    <span class="font-mono font-bold text-sm text-sky-600 dark:text-sky-400">{{ $res->grade }} ({{ $res->grade_point }})</span>
                                    <span class="block text-[10px] text-zinc-400">{{ $res->total_score }}/100</span>
                                </div>
                            </div>
                        @empty
                            <p class="py-4 text-center text-xs text-zinc-400">No results published yet.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        @endif

    </div>
</div>
