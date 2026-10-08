<div class="py-8">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-800 dark:bg-rose-950/60 dark:text-rose-300">
                    Objective Structured Clinical Examination (OSCE) • Station Assessor Console
                </span>
                <h1 class="mt-2 text-2xl font-black tracking-tight text-zinc-900 dark:text-white">
                    OSCE Clinical Station Scoring Sheet
                </h1>
            </div>
            
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-zinc-400">Total Awarded:</span>
                <span class="rounded-xl bg-zinc-900 px-4 py-2 font-mono text-xl font-black text-white dark:bg-zinc-800">
                    {{ number_format($currentScore, 1) }} / {{ $station?->max_score ?? 20 }}
                </span>
            </div>
        </div>

        <!-- Station & Candidate Selector -->
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 rounded-2xl border border-zinc-200/80 bg-white p-5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
            <div>
                <label class="block text-xs font-bold uppercase text-zinc-400 mb-1">Active Station</label>
                <select wire:model.live="selectedStationId" class="block w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs font-bold text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    @foreach($stations as $st)
                        <option value="{{ $st->id }}">Station {{ $st->station_number }}: {{ $st->title }} ({{ $st->allocated_time_minutes }} mins)</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-zinc-400 mb-1">Candidate Under Assessment</label>
                <select wire:model.live="selectedStudentId" class="block w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs font-bold text-zinc-800 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    @foreach($students as $stud)
                        <option value="{{ $stud->id }}">{{ $stud->student_number }} - {{ $stud->full_name }} ({{ $stud->currentLevel?->code }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Station Scenario Card -->
        @if($station)
            <div class="mb-6 rounded-2xl border border-sky-200 bg-sky-50/60 p-5 dark:border-sky-900/40 dark:bg-sky-950/20">
                <div class="flex items-start gap-3">
                    <div class="rounded-lg bg-sky-600 p-2 text-white shrink-0">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-sky-900 dark:text-sky-200">Clinical Scenario & Candidate Instructions</h4>
                        <p class="mt-1 text-xs text-sky-800 dark:text-sky-300 leading-relaxed">{{ $station->scenario }}</p>
                    </div>
                </div>
            </div>

            <!-- Rubrics Evaluation List -->
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 space-y-6">
                <div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-white">Structured Objective Rubric Criteria</h3>
                    <p class="text-xs text-zinc-500 mt-1 dark:text-zinc-400">Score each step demonstrated by the student nurse.</p>
                </div>

                <div class="space-y-4 divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($station->rubrics as $rub)
                        <div class="pt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex-1">
                                <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $rub->criterion }}</span>
                                <span class="block text-[11px] text-zinc-400">Max Mark: {{ $rub->max_score }}</span>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <input 
                                    type="range" 
                                    min="0" 
                                    max="{{ $rub->max_score }}" 
                                    step="0.5" 
                                    wire:model.live="rubricScores.{{ $rub->id }}" 
                                    class="w-32 accent-rose-600"
                                >
                                <span class="w-16 rounded-lg bg-zinc-100 py-1.5 text-center font-mono text-sm font-bold text-zinc-900 dark:bg-zinc-800 dark:text-white">
                                    {{ $rubricScores[$rub->id] ?? 0 }} / {{ $rub->max_score }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300">Station Examiner Remarks & Feedback</label>
                    <textarea wire:model="examinerComments" rows="3" class="mt-1.5 block w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white" placeholder="Document specific clinical observations, asepsis breaches, or commendable interactions..."></textarea>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <div class="text-xs text-zinc-500">
                        Station Assessment recorded under Examiner: <strong class="text-zinc-800 dark:text-zinc-200">{{ auth()->user()->name }}</strong>
                    </div>
                    <button wire:click="saveScore" class="inline-flex items-center gap-2 rounded-xl bg-rose-600 px-6 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-rose-500">
                        Save Station Candidate Score
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                </div>
            </div>
        @endif

    </div>
</div>
