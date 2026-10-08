<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 space-y-8">
    
    <!-- Header Section with Quick Stats & Actions -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-zinc-200 pb-6 dark:border-zinc-800">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-sky-600 text-white shadow-md">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </span>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Academic Sessions & Term Containers</h1>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Foundational time-bound containers governing course offerings, registrations, examinations, and results.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @can('create', App\Models\AcademicSession::class)
                <button wire:click="openCreateModal" 
                        class="inline-flex items-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-sky-500 transition cursor-pointer">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Academic Session
                </button>
            @endcan
        </div>
    </div>

    <!-- Active Container Status Banner -->
    @if($currentSession)
        <div class="rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-teal-50 p-6 dark:border-emerald-900/50 dark:from-emerald-950/40 dark:to-teal-950/30 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <span class="mt-1 flex size-3 rounded-full bg-emerald-500 animate-pulse"></span>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-400">Active Operational Session</span>
                            <span class="rounded-md bg-emerald-100 px-2 py-0.5 text-xs font-extrabold text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300">
                                {{ $currentSession->status->label() }}
                            </span>
                        </div>
                        <h2 class="text-2xl font-black text-zinc-900 dark:text-white mt-1">{{ $currentSession->name }} Academic Session</h2>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-0.5">
                            Valid from {{ $currentSession->start_date?->format('M d, Y') }} through {{ $currentSession->end_date?->format('M d, Y') }}
                            @if($currentSession->allowsRegistration())
                                &bull; <span class="text-emerald-700 dark:text-emerald-300 font-semibold">Course Registration is OPEN</span>
                            @else
                                &bull; <span class="text-amber-700 dark:text-amber-300 font-semibold">Course Registration is LOCKED</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @if($currentSession->isActive())
                        <button wire:click="beginResultProcessing({{ $currentSession->id }})"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-amber-300 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-800 hover:bg-amber-100 dark:border-amber-800 dark:bg-amber-950/50 dark:text-amber-300 transition cursor-pointer">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Lock & Begin Result Processing
                        </button>
                    @endif

                    <button wire:click="manageSession({{ $currentSession->id }})"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-300 bg-white px-3 py-2 text-xs font-bold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 transition cursor-pointer">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        Manage Semesters & Offerings
                    </button>

                    <button wire:click="openCloseModal({{ $currentSession->id }})"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-rose-300 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-800 hover:bg-rose-100 dark:border-rose-800 dark:bg-rose-950/50 dark:text-rose-300 transition cursor-pointer">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Close Session Checklist
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- All Sessions Master Table -->
    <div class="rounded-2xl border border-zinc-200 bg-white overflow-hidden dark:border-zinc-800 dark:bg-zinc-900 shadow-xs">
        <div class="border-b border-zinc-200 px-6 py-4 dark:border-zinc-800 flex items-center justify-between">
            <h3 class="text-sm font-bold text-zinc-900 dark:text-white uppercase tracking-wider">All Academic Session Records</h3>
            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $sessions->count() }} registered sessions</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-zinc-50 text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:bg-zinc-800/50 dark:text-zinc-400">
                    <tr>
                        <th class="px-6 py-3.5">Session / Code</th>
                        <th class="px-6 py-3.5">Duration</th>
                        <th class="px-6 py-3.5">Semesters / Terms</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Current Active</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($sessions as $s)
                        <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-zinc-900 dark:text-white text-sm">{{ $s->name }}</div>
                                <div class="text-[11px] font-mono text-zinc-500 dark:text-zinc-400">{{ $s->code ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-300">
                                <div>{{ $s->start_date?->format('d/m/Y') }} &rarr; {{ $s->end_date?->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-zinc-400">Created {{ $s->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @foreach($s->semesters as $sem)
                                        <span class="rounded bg-zinc-100 px-2 py-0.5 text-[10px] font-semibold text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                            {{ $sem->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColor = match($s->status?->value ?? 'draft') {
                                        'active' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                        'result_processing' => 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                        'closed' => 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border-rose-200 dark:border-rose-800',
                                        'archived' => 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300 border-purple-200 dark:border-purple-800',
                                        default => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border-zinc-200 dark:border-zinc-700',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-lg border px-2.5 py-1 text-[11px] font-bold {{ $statusColor }}">
                                    {{ $s->status?->label() ?? 'DRAFT' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($s->is_current)
                                    <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-bold text-xs">
                                        <span class="size-2 rounded-full bg-emerald-500"></span>
                                        Current
                                    </span>
                                @else
                                    <span class="text-zinc-400 text-xs">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <button wire:click="manageSession({{ $s->id }})" 
                                            class="rounded-lg border border-zinc-200 px-2.5 py-1 text-[11px] font-semibold text-zinc-700 hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800 transition cursor-pointer">
                                        Terms & Offerings
                                    </button>

                                    @if($s->status?->value === 'draft')
                                        <button wire:click="activateSession({{ $s->id }})"
                                                wire:confirm="Activate {{ $s->name }} as the official operational session? Any previously active session will be closed."
                                                class="rounded-lg bg-emerald-600 px-2.5 py-1 text-[11px] font-bold text-white hover:bg-emerald-500 transition cursor-pointer">
                                            Activate
                                        </button>
                                    @elseif($s->status?->value === 'active')
                                        <button wire:click="beginResultProcessing({{ $s->id }})"
                                                class="rounded-lg bg-amber-600 px-2.5 py-1 text-[11px] font-bold text-white hover:bg-amber-500 transition cursor-pointer">
                                            Result Processing
                                        </button>
                                        <button wire:click="openCloseModal({{ $s->id }})"
                                                class="rounded-lg bg-rose-600 px-2.5 py-1 text-[11px] font-bold text-white hover:bg-rose-500 transition cursor-pointer">
                                            Close
                                        </button>
                                    @elseif($s->status?->value === 'result_processing')
                                        <button wire:click="openCloseModal({{ $s->id }})"
                                                class="rounded-lg bg-rose-600 px-2.5 py-1 text-[11px] font-bold text-white hover:bg-rose-500 transition cursor-pointer">
                                            Close
                                        </button>
                                    @elseif($s->status?->value === 'closed')
                                        <button wire:click="archiveSession({{ $s->id }})"
                                                wire:confirm="Archive session {{ $s->name }} into read-only historical storage?"
                                                class="rounded-lg bg-purple-600 px-2.5 py-1 text-[11px] font-bold text-white hover:bg-purple-500 transition cursor-pointer">
                                            Archive
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-zinc-500">No academic sessions registered.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Session Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/70 backdrop-blur-xs p-4">
            <div class="w-full max-w-lg rounded-2xl border border-zinc-200 bg-white p-6 shadow-2xl dark:border-zinc-800 dark:bg-zinc-900 space-y-5">
                <div class="flex items-center justify-between border-b border-zinc-200 pb-3 dark:border-zinc-800">
                    <h3 class="text-base font-bold text-zinc-900 dark:text-white">Create Academic Session</h3>
                    <button wire:click="closeCreateModal" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">&times;</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-zinc-700 dark:text-zinc-300">Session Name (e.g. 2026/2027)</label>
                        <input type="text" wire:model="name" placeholder="2026/2027" 
                               class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-xs text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white" />
                        @error('name') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-zinc-700 dark:text-zinc-300">Session Code (e.g. 2026-2027)</label>
                        <input type="text" wire:model="code" placeholder="2026-2027" 
                               class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-xs text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white" />
                        @error('code') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-zinc-700 dark:text-zinc-300">Start Date</label>
                            <input type="date" wire:model="start_date" 
                                   class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-xs text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white" />
                            @error('start_date') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-bold text-zinc-700 dark:text-zinc-300">End Date</label>
                            <input type="date" wire:model="end_date" 
                                   class="mt-1 w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-xs text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white" />
                            @error('end_date') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="rounded-xl bg-sky-50 p-3 text-[11px] text-sky-800 dark:bg-sky-950/40 dark:text-sky-300">
                        Initial configuration creates session in <strong>DRAFT</strong> status with First & Second Semesters automatically generated.
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-zinc-200 dark:border-zinc-800">
                    <button wire:click="closeCreateModal" class="rounded-xl border border-zinc-300 px-4 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800">
                        Cancel
                    </button>
                    <button wire:click="createSession" class="rounded-xl bg-sky-600 px-4 py-2 text-xs font-bold text-white hover:bg-sky-500">
                        Create Session
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Manage Terms & Offerings Modal -->
    @if($showManageModal && $selectedSession)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/70 backdrop-blur-xs p-4">
            <div class="w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-2xl border border-zinc-200 bg-white p-6 shadow-2xl dark:border-zinc-800 dark:bg-zinc-900 space-y-6">
                <div class="flex items-center justify-between border-b border-zinc-200 pb-3 dark:border-zinc-800">
                    <div>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-white">{{ $selectedSession->name }} &mdash; Academic Terms & Offerings</h3>
                        <p class="text-xs text-zinc-500">Manage term dates, status, and course offerings allocated to this session container.</p>
                    </div>
                    <button wire:click="closeManageModal" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">&times;</button>
                </div>

                <!-- Terms / Semesters -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-400 mb-2">Semesters / Academic Terms</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($selectedSession->semesters as $sem)
                            <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/40 space-y-2 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-zinc-900 dark:text-white">{{ $sem->name }}</span>
                                    <span class="rounded bg-sky-100 px-2 py-0.5 text-[10px] font-bold text-sky-800 dark:bg-sky-950 dark:text-sky-300">
                                        {{ $sem->code ?? "Sem {$sem->semester}" }}
                                    </span>
                                </div>
                                <div class="text-zinc-500 text-[11px]">
                                    Sequence: {{ $sem->sequence }} &bull; {{ $sem->start_date?->format('d/m/Y') }} to {{ $sem->end_date?->format('d/m/Y') }}
                                </div>
                                <div class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400">
                                    {{ $sem->is_current ? 'Currently Active Term' : 'Term Inactive' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Course Offerings -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-400">Course Offerings In Session ({{ $selectedSession->offerings->count() }})</h4>
                        <button wire:click="provisionOfferings" class="rounded-lg bg-teal-600 px-2.5 py-1 text-xs font-bold text-white hover:bg-teal-500 transition">
                            + Auto-Provision Active Courses
                        </button>
                    </div>

                    <div class="max-h-60 overflow-y-auto rounded-xl border border-zinc-200 dark:border-zinc-800">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-zinc-50 text-[10px] font-bold uppercase text-zinc-500 dark:bg-zinc-800/50 dark:text-zinc-400">
                                <tr>
                                    <th class="px-3 py-2">Course</th>
                                    <th class="px-3 py-2">Level</th>
                                    <th class="px-3 py-2">Semester</th>
                                    <th class="px-3 py-2">Department</th>
                                    <th class="px-3 py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 text-[11px]">
                                @forelse($selectedSession->offerings as $off)
                                    <tr>
                                        <td class="px-3 py-2 font-bold">{{ $off->course?->code }} &mdash; {{ $off->course?->title }}</td>
                                        <td class="px-3 py-2">{{ $off->level }}L</td>
                                        <td class="px-3 py-2">Semester {{ $off->academicSemester?->semester }}</td>
                                        <td class="px-3 py-2">{{ $off->department?->name }}</td>
                                        <td class="px-3 py-2"><span class="rounded bg-emerald-100 px-1.5 py-0.5 text-[10px] font-semibold text-emerald-800">{{ $off->status }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-4 text-center text-zinc-500">No offerings provisioned. Click Auto-Provision to generate offerings for active curriculum.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-zinc-200 dark:border-zinc-800">
                    <button wire:click="closeManageModal" class="rounded-xl bg-zinc-800 px-4 py-2 text-xs font-bold text-white hover:bg-zinc-700">
                        Done
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Session Close Pre-Flight Checklist Modal -->
    @if($showCloseModal && $closingSession)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/70 backdrop-blur-xs p-4">
            <div class="w-full max-w-xl rounded-2xl border border-zinc-200 bg-white p-6 shadow-2xl dark:border-zinc-800 dark:bg-zinc-900 space-y-5">
                <div class="flex items-center justify-between border-b border-zinc-200 pb-3 dark:border-zinc-800">
                    <div>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-white">Pre-Flight Closure Checklist: {{ $closingSession->name }}</h3>
                        <p class="text-xs text-zinc-500">Validating examination workflows, approvals, and GPA processing before formal closure.</p>
                    </div>
                    <button wire:click="closeCloseModal" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">&times;</button>
                </div>

                <div class="space-y-3 text-xs">
                    @php
                        $checks = $preflightChecks['checks'] ?? [];
                        $canClose = $preflightChecks['can_close'] ?? false;
                        $errors = $preflightChecks['errors'] ?? [];
                    @endphp

                    <div class="space-y-2 divide-y divide-zinc-100 dark:divide-zinc-800">
                        <!-- Check 1: Corrections -->
                        <div class="flex items-center justify-between py-2">
                            <span class="text-zinc-700 dark:text-zinc-300">Pending Result Correction Requests</span>
                            @if(($checks['pending_corrections'] ?? 0) === 0)
                                <span class="inline-flex items-center gap-1 font-bold text-emerald-600">&check; 0 Pending</span>
                            @else
                                <span class="inline-flex items-center gap-1 font-bold text-rose-600">&cross; {{ $checks['pending_corrections'] }} Pending</span>
                            @endif
                        </div>

                        <!-- Check 2: Draft Results -->
                        <div class="flex items-center justify-between py-2">
                            <span class="text-zinc-700 dark:text-zinc-300">Unsubmitted Lecturer Scores (Draft)</span>
                            @if(($checks['draft_results'] ?? 0) === 0)
                                <span class="inline-flex items-center gap-1 font-bold text-emerald-600">&check; All Submitted</span>
                            @else
                                <span class="inline-flex items-center gap-1 font-bold text-rose-600">&cross; {{ $checks['draft_results'] }} Drafts</span>
                            @endif
                        </div>

                        <!-- Check 3: HOD Verification -->
                        <div class="flex items-center justify-between py-2">
                            <span class="text-zinc-700 dark:text-zinc-300">Awaiting Departmental HOD Verification</span>
                            @if(($checks['unverified_results'] ?? 0) === 0)
                                <span class="inline-flex items-center gap-1 font-bold text-emerald-600">&check; All Verified</span>
                            @else
                                <span class="inline-flex items-center gap-1 font-bold text-amber-600">&cross; {{ $checks['unverified_results'] }} Unverified</span>
                            @endif
                        </div>

                        <!-- Check 4: Board Approval -->
                        <div class="flex items-center justify-between py-2">
                            <span class="text-zinc-700 dark:text-zinc-300">Awaiting Academic Board Approval</span>
                            @if(($checks['unapproved_results'] ?? 0) === 0)
                                <span class="inline-flex items-center gap-1 font-bold text-emerald-600">&check; All Approved</span>
                            @else
                                <span class="inline-flex items-center gap-1 font-bold text-amber-600">&cross; {{ $checks['unapproved_results'] }} Unapproved</span>
                            @endif
                        </div>

                        <!-- Check 5: Student Publication -->
                        <div class="flex items-center justify-between py-2">
                            <span class="text-zinc-700 dark:text-zinc-300">Publication to Student Portal</span>
                            @if(($checks['unpublished_results'] ?? 0) === 0)
                                <span class="inline-flex items-center gap-1 font-bold text-emerald-600">&check; Published</span>
                            @else
                                <span class="inline-flex items-center gap-1 font-bold text-rose-600">&cross; {{ $checks['unpublished_results'] }} Unpublished</span>
                            @endif
                        </div>
                    </div>

                    @if(!$canClose)
                        <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-[11px] text-rose-800 dark:border-rose-900/50 dark:bg-rose-950/40 dark:text-rose-300">
                            <strong>Closure Blocked:</strong>
                            <ul class="list-disc pl-4 mt-1 space-y-0.5">
                                @foreach($errors as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>

                        @can('override', $closingSession)
                            <div class="flex items-center gap-2 pt-2">
                                <input type="checkbox" id="forceCloseCheck" wire:model="forceClose" class="rounded border-zinc-300 text-rose-600" />
                                <label for="forceCloseCheck" class="text-xs text-zinc-700 dark:text-zinc-300 font-semibold">
                                    Administrative Override: Force closure despite outstanding items
                                </label>
                            </div>
                        @endcan
                    @else
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-[11px] text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300">
                            <strong>All Pre-flight Checks Passed:</strong> Ready to close session {{ $closingSession->name }}. Closure will automatically finalize CGPA calculations and student progression standing.
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-zinc-200 dark:border-zinc-800">
                    <button wire:click="closeCloseModal" class="rounded-xl border border-zinc-300 px-4 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-300">
                        Cancel
                    </button>
                    @if($canClose || $forceClose)
                        <button wire:click="executeCloseSession" class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white hover:bg-rose-500">
                            Confirm & Close Academic Session
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

</div>
