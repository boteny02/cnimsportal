<div class="py-8">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Digital Clinical Logbook & Rotations</h1>
                <p class="text-xs text-zinc-500 mt-1 dark:text-zinc-400">Record procedural clinical experiences, document reflective practice, and track preceptor competencies.</p>
            </div>
        </div>

        <!-- Active Rotation Card -->
        @if($activePosting)
            <div class="mb-8 rounded-2xl border border-teal-200 bg-teal-50/70 p-5 dark:border-teal-900/60 dark:bg-teal-950/30">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="rounded-xl bg-teal-600 p-2.5 text-white shrink-0">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-teal-800 dark:text-teal-300">Active Hospital Placement</span>
                            <h3 class="text-base font-bold text-zinc-900 dark:text-white">{{ $activePosting->title }}</h3>
                            <p class="text-xs text-zinc-600 dark:text-zinc-300 mt-0.5">
                                {{ $activePosting->facility?->name }} • <strong>Ward:</strong> {{ $activePosting->ward?->name }} • <strong>Preceptor:</strong> {{ $activePosting->supervisor?->name }}
                            </p>
                        </div>
                    </div>
                    <div class="text-xs text-zinc-500 sm:text-right">
                        <span>Period: {{ $activePosting->start_date?->format('d M') }} – {{ $activePosting->end_date?->format('d M Y') }}</span>
                        <div class="mt-1 font-bold text-teal-700 dark:text-teal-400">Attendance: {{ $activePosting->pivot->attendance_rate ?? 100 }}%</div>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            
            <!-- Log Procedure Form -->
            <div class="lg:col-span-1 rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 h-fit">
                <h3 class="text-sm font-bold text-zinc-900 dark:text-white mb-1">Log Clinical Procedure</h3>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mb-4">Submit a bedside or clinical nursing task performed.</p>

                <form wire:submit="submitLog" class="space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Procedure Performed *</label>
                        <select wire:model="procedure_id" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            @foreach($procedures as $proc)
                                <option value="{{ $proc->id }}">{{ $proc->title }} ({{ $proc->category }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Date *</label>
                            <input type="date" wire:model="procedure_date" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-1.5 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Patient Code</label>
                            <input type="text" wire:model="patient_reference_code" placeholder="e.g. PT-304" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-1.5 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Self-Assessed Competency Level *</label>
                        <select wire:model="competency_level" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            <option value="observed">Observed (Watch preceptor)</option>
                            <option value="assisted">Assisted (Aided procedure)</option>
                            <option value="supervised">Supervised (Under direct eyes)</option>
                            <option value="competent">Competent (Independent technique)</option>
                            <option value="independent">Independent (Mastered skill)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Clinical Reflection *</label>
                        <textarea wire:model="student_reflection" rows="3" placeholder="Describe aseptic boundaries, patient comfort, and key learnings..." class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"></textarea>
                        @error('student_reflection') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="w-full rounded-lg bg-teal-600 py-2.5 font-bold text-white shadow-xs hover:bg-teal-500 transition">
                        Submit to Preceptor
                    </button>
                </form>
            </div>

            <!-- Logbook History -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- History Table -->
                <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="border-b border-zinc-200 bg-zinc-50 px-5 py-3 dark:border-zinc-800 dark:bg-zinc-800/60 font-bold text-xs text-zinc-800 dark:text-white">
                        Procedural Experience Log Entries ({{ $logs->count() }})
                    </div>

                    <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($logs as $log)
                            <div class="p-4 text-xs space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-zinc-900 dark:text-white text-sm">{{ $log->procedure?->title }}</span>
                                    <x-badge :type="$log->status">{{ ucfirst($log->status) }}</x-badge>
                                </div>
                                <div class="text-zinc-500 text-[11px]">
                                    {{ $log->procedure_date?->format('d M Y') }} • Patient Code: <span class="font-mono text-zinc-700 dark:text-zinc-300">{{ $log->patient_reference_code }}</span> • Level: <strong class="text-teal-600 uppercase">{{ $log->competency_level }}</strong>
                                </div>
                                <p class="text-zinc-700 dark:text-zinc-300 italic bg-zinc-50 dark:bg-zinc-800/40 p-2.5 rounded-lg border border-zinc-100 dark:border-zinc-800">
                                    "{{ $log->student_reflection }}"
                                </p>
                                @if($log->supervisor_remarks)
                                    <div class="mt-2 text-[11px] text-teal-800 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/40 p-2 rounded-lg border border-teal-200 dark:border-teal-900/50">
                                        <strong>Preceptor Feedback ({{ $log->supervisor?->name }}):</strong> {{ $log->supervisor_remarks }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="p-8 text-center text-xs text-zinc-400">
                                No procedures logged yet. Use the form on the left to submit your first clinical entry.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Competency Engine Progress Cards -->
                <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-white mb-3">Nursing Competency Progression</h3>
                    <div class="space-y-4">
                        @foreach($categories as $cat)
                            <div class="rounded-xl border border-zinc-100 bg-zinc-50/70 p-3.5 dark:border-zinc-800 dark:bg-zinc-800/40">
                                <h4 class="text-xs font-bold text-teal-800 dark:text-teal-300 uppercase tracking-wide">{{ $cat->name }}</h4>
                                <div class="mt-2 space-y-2">
                                    @foreach($cat->skills as $sk)
                                        @php $currentComp = $sk->studentCompetencies->first(); @endphp
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-zinc-700 dark:text-zinc-300 font-medium">{{ $sk->name }}</span>
                                            <span class="rounded bg-white px-2 py-0.5 text-[10px] font-bold uppercase shadow-2xs border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700 {{ $currentComp && in_array($currentComp->current_level, ['competent', 'independent']) ? 'text-emerald-600' : 'text-amber-600' }}">
                                                {{ $currentComp?->current_level ?? 'Not Started' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
