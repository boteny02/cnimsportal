<div class="py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Examination Scoring & Result Processing</h1>
                <p class="text-xs text-zinc-500 mt-1 dark:text-zinc-400">Institutional workflow: Lecturer score entry (CA + Exam) → HOD verification → Exam Officer GPA compilation → Board approval → Portal publication.</p>
            </div>
            
            <!-- Workflow Buttons Bar -->
            <div class="flex flex-wrap items-center gap-2">
                <button wire:click="saveLecturerScores" class="rounded-lg bg-sky-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-sky-600 shadow-2xs cursor-pointer">
                    1. Lecturer Submit
                </button>
                <button wire:click="verifyScores" class="rounded-lg bg-indigo-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-600 shadow-2xs cursor-pointer">
                    2. HOD Verify
                </button>
                <button wire:click="processResults" class="rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-500 shadow-2xs cursor-pointer">
                    3. Process GPA/CGPA
                </button>
                <button wire:click="approveResults" class="rounded-lg bg-purple-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-purple-600 shadow-2xs cursor-pointer">
                    4. Board Approve
                </button>
                <button wire:click="publishResults" wire:confirm="Publish results to student portal?" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-500 shadow-2xs cursor-pointer">
                    5. Publish to Students
                </button>
            </div>
        </div>

        <!-- Session Lifecycle Context Notice -->
        @if($session)
            <div class="rounded-xl border p-4 text-xs flex items-center justify-between shadow-xs
                {{ $session->isActive() ? 'border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-900/40 dark:bg-emerald-950/30 dark:text-emerald-300' : 
                   ($session->isResultProcessing() ? 'border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-900/40 dark:bg-amber-950/30 dark:text-amber-300' : 'border-zinc-200 bg-zinc-50 text-zinc-800 dark:border-zinc-800 dark:bg-zinc-800/40 dark:text-zinc-300') }}">
                <div class="flex items-center gap-2.5">
                    <span class="size-2 rounded-full {{ $session->isActive() ? 'bg-emerald-500 animate-pulse' : ($session->isResultProcessing() ? 'bg-amber-500 animate-pulse' : 'bg-zinc-400') }}"></span>
                    <div>
                        <span class="font-bold uppercase tracking-wider text-[10px]">Academic Session Container:</span>
                        <strong class="ml-1">{{ $session->name }}</strong>
                        <span class="ml-2 font-mono font-semibold">({{ $session->status?->label() }})</span>
                    </div>
                </div>
                <div class="text-[11px]">
                    @if($session->isResultProcessing())
                        <span class="font-bold text-amber-700 dark:text-amber-400">RESULT PROCESSING MODE:</span> Course registration locked. Grading and board approvals in progress.
                    @elseif($session->isClosed() || $session->isArchived())
                        <span class="font-bold text-rose-700 dark:text-rose-400">CLOSED SESSION:</span> Academic transactions finalized. Results are read-only historical records.
                    @else
                        <span>Active session instruction & assessment period.</span>
                    @endif
                </div>
            </div>
        @endif

        <!-- Course & Session Selector -->
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 rounded-xl border border-zinc-200/80 bg-white p-4 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
            <div>
                <label class="block text-[11px] font-bold uppercase text-zinc-400 mb-1">Course Under Assessment</label>
                <select wire:model.live="selectedCourseId" class="block w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->title }} ({{ $c->credit_units }} Units)</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase text-zinc-400 mb-1">Academic Session Container</label>
                <select wire:model.live="selectedSessionId" class="block w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    @foreach($sessions as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->status?->value }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase text-zinc-400 mb-1">Semester / Term</label>
                <select wire:model.live="selectedSemester" class="block w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    <option value="1">First Semester</option>
                    <option value="2">Second Semester</option>
                </select>
            </div>
        </div>

        <!-- Score Entry Table -->
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 bg-zinc-50 px-5 py-3 dark:border-zinc-800 dark:bg-zinc-800/60 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">
                        {{ $course?->code }}: {{ $course?->title }}
                    </span>
                    <span class="ml-2 text-zinc-400 text-xs">({{ $course?->credit_units }} Credit Units)</span>
                </div>
                <div class="text-xs text-zinc-500">
                    Continuous Assessment (Max 30) + Final Examination (Max 70) = Total (100)
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-left text-xs dark:divide-zinc-800">
                    <thead class="bg-zinc-50/50 font-semibold text-zinc-600 dark:bg-zinc-800/40 dark:text-zinc-300">
                        <tr>
                            <th class="px-4 py-3">Matric No.</th>
                            <th class="px-4 py-3">Student Name</th>
                            <th class="px-4 py-3">Attempt</th>
                            <th class="px-4 py-3 w-32">CA Score (30)</th>
                            <th class="px-4 py-3 w-32">Exam Score (70)</th>
                            <th class="px-4 py-3">Total (100)</th>
                            <th class="px-4 py-3">Grade</th>
                            <th class="px-4 py-3">Points</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($rows as $row)
                            @php $st = $row['student']; @endphp
                            <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition">
                                <td class="px-4 py-3 font-mono font-bold text-sky-700 dark:text-sky-300">
                                    {{ $st->student_number }}
                                </td>
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">
                                    {{ $st->full_name }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($row['attempt_type'] === 'CARRYOVER')
                                        <span class="rounded bg-rose-100 px-2 py-0.5 text-[10px] font-bold text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                            Carryover (Att. {{ $row['attempt_number'] }})
                                        </span>
                                    @elseif($row['attempt_type'] === 'REPEAT')
                                        <span class="rounded bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                            Repeat (Att. {{ $row['attempt_number'] }})
                                        </span>
                                    @else
                                        <span class="rounded bg-sky-100 px-2 py-0.5 text-[10px] font-semibold text-sky-800 dark:bg-sky-950 dark:text-sky-300">
                                            1st Attempt
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <input 
                                        type="number" 
                                        step="0.5" 
                                        min="0" 
                                        max="30" 
                                        wire:model.live="scores.{{ $st->id }}.ca" 
                                        class="w-24 rounded-md border border-zinc-300 px-2.5 py-1 text-xs font-semibold focus:border-sky-500 focus:ring-sky-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                                    >
                                </td>
                                <td class="px-4 py-3">
                                    <input 
                                        type="number" 
                                        step="0.5" 
                                        min="0" 
                                        max="70" 
                                        wire:model.live="scores.{{ $st->id }}.exam" 
                                        class="w-24 rounded-md border border-zinc-300 px-2.5 py-1 text-xs font-semibold focus:border-sky-500 focus:ring-sky-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                                    >
                                </td>
                                <td class="px-4 py-3 font-bold text-sm text-zinc-900 dark:text-white">
                                    {{ number_format($row['total'], 1) }}
                                </td>
                                <td class="px-4 py-3 font-mono font-black text-sm {{ $row['points'] >= 3.0 ? 'text-emerald-600' : ($row['points'] >= 2.0 ? 'text-amber-600' : 'text-rose-600') }}">
                                    {{ $row['grade'] }}
                                </td>
                                <td class="px-4 py-3 font-semibold text-zinc-700 dark:text-zinc-300">
                                    {{ number_format($row['points'], 1) }} GP
                                </td>
                                <td class="px-4 py-3">
                                    <x-badge :type="$row['status']">{{ ucwords(str_replace('_', ' ', $row['status'])) }}</x-badge>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($row['result_id'])
                                        <button wire:click="openCorrectionModal({{ $st->id }})" class="rounded border border-zinc-200 px-2 py-0.5 text-[10px] font-semibold text-zinc-600 hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-300 cursor-pointer">
                                            Correction
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-8 text-center text-xs text-zinc-500">
                                    No students enrolled in this course for this session.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/40 flex items-center justify-between">
                <span class="text-xs text-zinc-500">Grading System: 70-100 (A, 5.0) • 60-69 (B, 4.0) • 50-59 (C, 3.0 Pass) • 45-49 (D, 2.0) • 0-44 (F, 0.0)</span>
                <button wire:click="saveLecturerScores" class="rounded-lg bg-sky-700 px-4 py-2 text-xs font-semibold text-white hover:bg-sky-600 cursor-pointer">
                    Save Changes
                </button>
            </div>
        </div>

        <!-- Correction Request Modal -->
        @if($showCorrectionModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/70 backdrop-blur-xs p-4">
                <div class="w-full max-w-md rounded-2xl border border-zinc-200 bg-white p-6 shadow-2xl dark:border-zinc-800 dark:bg-zinc-900 space-y-4">
                    <div class="flex items-center justify-between border-b border-zinc-200 pb-3 dark:border-zinc-800">
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Request Result Correction</h3>
                        <button wire:click="closeCorrectionModal" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">&times;</button>
                    </div>

                    <div class="space-y-3 text-xs">
                        <p class="text-zinc-600 dark:text-zinc-400">
                            Flag this result with a formal correction request for review by the course lecturer or examination board.
                        </p>
                        <div>
                            <label class="block font-bold text-zinc-700 dark:text-zinc-300">Reason / Justification</label>
                            <textarea wire:model="correctionReason" rows="3" placeholder="e.g., Script re-mark shows omitted section marks..." 
                                      class="mt-1 w-full rounded-xl border border-zinc-300 bg-white p-3 text-xs text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"></textarea>
                            @error('correctionReason') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-zinc-200 dark:border-zinc-800">
                        <button wire:click="closeCorrectionModal" class="rounded-xl border border-zinc-300 px-3 py-1.5 text-xs font-semibold text-zinc-700 hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-300">Cancel</button>
                        <button wire:click="submitCorrectionRequest" class="rounded-xl bg-rose-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-rose-500">Submit Request</button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
