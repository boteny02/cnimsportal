<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Admissions & Screening Lifecycle Board</h1>
            <p class="text-xs text-zinc-500 mt-1 dark:text-zinc-400">Manage candidate O'Level credits, JAMB scores, CBT exam scheduling, entrance results, admission offers, and acceptance payments.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('public.apply') }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-300 bg-white px-3 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Open Application Wizard
            </a>
        </div>
    </div>

    <!-- Filter bar -->
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-4 rounded-xl border border-zinc-200/80 bg-white p-4 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
        <div class="sm:col-span-2">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search candidate name, application number, or email..." 
                class="block w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs shadow-2xs focus:border-sky-500 focus:ring-sky-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
            >
        </div>
        <div>
            <select wire:model.live="statusFilter" class="block w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs shadow-2xs focus:border-sky-500 focus:ring-sky-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                <option value="all">All Stages</option>
                <option value="submitted">Submitted (Fee Pending)</option>
                <option value="fee_paid">App Fee Paid</option>
                <option value="exam_invited">CBT Exam Scheduled</option>
                <option value="exam_scored">CBT Scored</option>
                <option value="offered">Admission Offered</option>
                <option value="acceptance_paid">Acceptance Paid</option>
                <option value="admitted">Matriculated / Admitted</option>
            </select>
        </div>
        <div>
            <select wire:model.live="programmeFilter" class="block w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs shadow-2xs focus:border-sky-500 focus:ring-sky-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                <option value="">All Programmes</option>
                @foreach($programmes as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-left text-xs dark:divide-zinc-800">
                <thead class="bg-zinc-50 font-semibold text-zinc-600 dark:bg-zinc-800/60 dark:text-zinc-300">
                    <tr>
                        <th class="px-4 py-3">Application Ref</th>
                        <th class="px-4 py-3">Applicant Name</th>
                        <th class="px-4 py-3">Programme</th>
                        <th class="px-4 py-3">O'Level & JAMB</th>
                        <th class="px-4 py-3">Fee Status</th>
                        <th class="px-4 py-3">Entrance Exam / Score</th>
                        <th class="px-4 py-3">Lifecycle Stage</th>
                        <th class="px-4 py-3 text-right">Workflow Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($applications as $app)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                            <!-- Ref -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-mono font-bold text-sky-700 dark:text-sky-300">{{ $app->application_number }}</span>
                                <div class="text-[10px] text-zinc-400">{{ $app->created_at->format('d M Y') }}</div>
                            </td>

                            <!-- Candidate -->
                            <td class="px-4 py-3">
                                <div class="font-bold text-zinc-900 dark:text-white">{{ $app->full_name }}</div>
                                <div class="text-[11px] text-zinc-500">{{ $app->email }} • {{ $app->phone }}</div>
                            </td>

                            <!-- Programme -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $app->programme?->name ?? 'Basic Nursing' }}</span>
                                <div class="text-[10px] text-zinc-400">{{ $app->academicSession?->name }}</div>
                            </td>

                            <!-- O'Level & JAMB -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5">
                                    <span class="rounded bg-emerald-100 px-1.5 py-0.2 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                        {{ $app->o_level_credits_count ?? 5 }}/5 Credits
                                    </span>
                                    <span class="text-[10px] text-zinc-500">({{ $app->o_level_sittings ?? 1 }} Sitting)</span>
                                </div>
                                <div class="text-[11px] font-semibold text-teal-700 dark:text-teal-400 mt-0.5">
                                    JAMB: {{ $app->jamb_score ?? 215 }}/400
                                </div>
                            </td>

                            <!-- Fee Status -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div>
                                    @if($app->application_fee_paid)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
                                            <span class="size-1.5 rounded-full bg-emerald-500"></span> App Fee: Paid
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-600 dark:text-rose-400">
                                            <span class="size-1.5 rounded-full bg-rose-500"></span> App Fee: Unpaid
                                        </span>
                                    @endif
                                </div>
                                @if(in_array($app->status, ['offered', 'acceptance_paid', 'admitted']))
                                    <div class="mt-0.5">
                                        @if($app->acceptance_fee_paid)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-teal-600 dark:text-teal-400">
                                                ✓ Acceptance: Paid
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] font-medium text-amber-600 dark:text-amber-400">
                                                Acceptance: Pending
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <!-- Entrance Exam / Score -->
                            <td class="px-4 py-3">
                                @if($app->entrance_exam_score !== null)
                                    <div class="font-bold text-indigo-700 dark:text-indigo-400">
                                        CBT: {{ number_format($app->entrance_exam_score, 1) }}%
                                    </div>
                                    <div class="text-[10px] text-zinc-400">Seat: {{ $app->entrance_exam_seat_number ?? 'CBT-01' }}</div>
                                @elseif($app->entrance_exam_invited)
                                    <span class="rounded bg-sky-100 px-1.5 py-0.5 text-[10px] font-bold text-sky-800 dark:bg-sky-950 dark:text-sky-300">
                                        Scheduled ({{ $app->entrance_exam_seat_number ?? 'Seat TBA' }})
                                    </span>
                                @else
                                    <span class="text-zinc-400 text-[11px]">Not Scheduled</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="rounded-md px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider
                                    {{ $app->status === 'admitted' ? 'bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-300' : '' }}
                                    {{ $app->status === 'offered' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : '' }}
                                    {{ $app->status === 'acceptance_paid' ? 'bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-300' : '' }}
                                    {{ $app->status === 'exam_scored' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300' : '' }}
                                    {{ $app->status === 'exam_invited' ? 'bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300' : '' }}
                                    {{ $app->status === 'fee_paid' ? 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300' : '' }}
                                    {{ $app->status === 'submitted' ? 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300' : '' }}">
                                    {{ str_replace('_', ' ', $app->status) }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    
                                    <!-- Schedule CBT Exam (Step 5) -->
                                    @if(in_array($app->status, ['fee_paid', 'submitted']) && !$app->entrance_exam_invited)
                                        <button type="button" wire:click="openExamModal({{ $app->id }})" title="Schedule CBT Entrance Exam" class="rounded-lg bg-sky-700 px-2 py-1 text-[11px] font-bold text-white hover:bg-sky-600 cursor-pointer">
                                            📅 Schedule Exam
                                        </button>
                                    @endif

                                    <!-- Enter CBT Score (Step 6) -->
                                    @if($app->entrance_exam_invited && $app->entrance_exam_score === null)
                                        <button type="button" wire:click="openScoreModal({{ $app->id }})" title="Enter CBT Entrance Score" class="rounded-lg bg-indigo-700 px-2 py-1 text-[11px] font-bold text-white hover:bg-indigo-600 cursor-pointer">
                                            📝 Input CBT Score
                                        </button>
                                    @endif

                                    <!-- Offer Admission (Step 7) -->
                                    @if(in_array($app->status, ['exam_scored', 'shortlisted']) && !in_array($app->status, ['offered', 'acceptance_paid', 'admitted']))
                                        <button type="button" wire:click="openOfferModal({{ $app->id }})" title="Offer Provisional Admission" class="rounded-lg bg-emerald-700 px-2 py-1 text-[11px] font-bold text-white hover:bg-emerald-600 cursor-pointer">
                                            🎉 Offer Admission
                                        </button>
                                    @endif

                                    <!-- Final Matriculate (Step 8) -->
                                    @if($app->status === 'acceptance_paid')
                                        <button type="button" wire:click="admitApplicant({{ $app->id }})" title="Finalize Student Matriculation" class="rounded-lg bg-teal-700 px-2 py-1 text-[11px] font-bold text-white hover:bg-teal-600 cursor-pointer">
                                            🎓 Matriculate
                                        </button>
                                    @endif

                                    <!-- View Details -->
                                    <button type="button" wire:click="selectForReview({{ $app->id }})" class="rounded-lg border border-zinc-300 bg-white px-2 py-1 text-[11px] font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 cursor-pointer">
                                        Review
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-xs text-zinc-500">
                                No applications found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
            {{ $applications->links() }}
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL: SCHEDULE CBT ENTRANCE EXAM (Step 5)                     -->
    <!-- ============================================================== -->
    @if($showExamModal && $selectedApplication)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/80 p-4 backdrop-blur-xs">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                <h3 class="font-bold text-base text-zinc-900 dark:text-white">Schedule CBT Entrance Examination</h3>
                <p class="text-xs text-zinc-500 mt-1">Issue examination invitation and allocate venue & seat number for <strong>{{ $selectedApplication->full_name }}</strong>.</p>

                <div class="mt-4 space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Exam Date & Time *</label>
                        <input type="datetime-local" wire:model.defer="examDate" class="w-full rounded-lg border border-zinc-300 bg-zinc-50 px-3 py-2 text-xs text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    </div>

                    <div>
                        <label class="block font-semibold text-zinc-700 dark:text-zinc-300 mb-1">CBT Examination Center / Venue *</label>
                        <input type="text" wire:model.defer="examVenue" class="w-full rounded-lg border border-zinc-300 bg-zinc-50 px-3 py-2 text-xs text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    </div>

                    <div>
                        <label class="block font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Allocated Seat Number *</label>
                        <input type="text" wire:model.defer="examSeatNumber" class="w-full rounded-lg border border-zinc-300 bg-zinc-50 px-3 py-2 text-xs font-mono font-bold text-sky-700 dark:border-zinc-700 dark:bg-zinc-800 dark:text-sky-300">
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <button type="button" wire:click="closeExamModal" class="rounded-xl border border-zinc-300 bg-white px-4 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" wire:click="scheduleExam" class="rounded-xl bg-sky-700 px-4 py-2 text-xs font-bold text-white hover:bg-sky-600 shadow-sm cursor-pointer">
                        Confirm & Issue Exam Slip
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL: INPUT CBT ENTRANCE SCORE (Step 6)                       -->
    <!-- ============================================================== -->
    @if($showScoreModal && $selectedApplication)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/80 p-4 backdrop-blur-xs">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                <h3 class="font-bold text-base text-zinc-900 dark:text-white">Record CBT Entrance Examination Score</h3>
                <p class="text-xs text-zinc-500 mt-1">Input the computer-based test result for <strong>{{ $selectedApplication->full_name }}</strong> ({{ $selectedApplication->application_number }}).</p>

                <div class="mt-4 space-y-3 text-xs">
                    <div>
                        <label class="block font-semibold text-zinc-700 dark:text-zinc-300 mb-1">CBT Score (Percentage 0 - 100%) *</label>
                        <input type="number" step="0.5" wire:model.defer="cbtScore" class="w-full rounded-lg border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm font-bold text-indigo-700 dark:border-zinc-700 dark:bg-zinc-800 dark:text-indigo-300">
                    </div>

                    <div>
                        <label class="block font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Screening & Aptitude Remarks</label>
                        <textarea wire:model.defer="cbtRemarks" rows="3" class="w-full rounded-lg border border-zinc-300 bg-zinc-50 px-3 py-2 text-xs text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <button type="button" wire:click="closeScoreModal" class="rounded-xl border border-zinc-300 bg-white px-4 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" wire:click="submitEntranceScore" class="rounded-xl bg-indigo-700 px-4 py-2 text-xs font-bold text-white hover:bg-indigo-600 shadow-sm cursor-pointer">
                        Save CBT Result
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL: OFFER PROVISIONAL ADMISSION (Step 7)                    -->
    <!-- ============================================================== -->
    @if($showOfferModal && $selectedApplication)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/80 p-4 backdrop-blur-xs">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                <h3 class="font-bold text-base text-zinc-900 dark:text-white">Offer Provisional Admission</h3>
                <p class="text-xs text-zinc-500 mt-1">Generate official provisional admission offer and acceptance fee invoice for <strong>{{ $selectedApplication->full_name }}</strong>.</p>

                <div class="mt-4 space-y-3 text-xs">
                    <div class="rounded-xl bg-emerald-50 p-3 border border-emerald-200 dark:bg-emerald-950/40 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200">
                        <p class="font-bold">Programme: {{ $selectedApplication->programme?->name }}</p>
                        <p class="text-[11px] mt-0.5">Entrance Score: {{ $selectedApplication->entrance_exam_score }}% • JAMB: {{ $selectedApplication->jamb_score ?? 215 }}</p>
                    </div>

                    <div>
                        <label class="block font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Acceptance Fee Payment Deadline *</label>
                        <input type="date" wire:model.defer="acceptanceDeadline" class="w-full rounded-lg border border-zinc-300 bg-zinc-50 px-3 py-2 text-xs text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                        <span class="text-[10px] text-zinc-400 mt-0.5 block">Default: 14 days from offer date.</span>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <button type="button" wire:click="closeOfferModal" class="rounded-xl border border-zinc-300 bg-white px-4 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" wire:click="confirmOfferAdmission" class="rounded-xl bg-emerald-700 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-600 shadow-sm cursor-pointer">
                        Issue Provisional Admission Offer
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- ============================================================== -->
    <!-- MODAL: REVIEW DOSSIER DETAILS                                  -->
    <!-- ============================================================== -->
    @if($showReviewModal && $selectedApplication)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/80 p-4 backdrop-blur-xs">
            <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                    <div>
                        <h3 class="font-bold text-base text-zinc-900 dark:text-white">{{ $selectedApplication->full_name }}</h3>
                        <p class="text-xs text-zinc-500 font-mono">{{ $selectedApplication->application_number }} • {{ $selectedApplication->programme?->name }}</p>
                    </div>
                    <span class="rounded bg-sky-100 px-2 py-0.5 text-xs font-bold text-sky-800 uppercase dark:bg-sky-950 dark:text-sky-300">
                        {{ str_replace('_', ' ', $selectedApplication->status) }}
                    </span>
                </div>

                <div class="mt-4 space-y-4 text-xs">
                    <!-- 2-Sitting O'Level Review -->
                    <div class="rounded-xl bg-zinc-50 p-4 border border-zinc-200 dark:bg-zinc-800/40 dark:border-zinc-700">
                        <div class="font-bold text-zinc-900 dark:text-white mb-2 uppercase text-[11px]">2-Sitting O'Level & Science Credits Verification:</div>
                        <div class="flex items-center gap-3 text-xs mb-3">
                            <span>Sittings: <strong>{{ $selectedApplication->o_level_sittings ?? 1 }}</strong></span>
                            <span>Science Credits: <strong class="text-emerald-600 dark:text-emerald-400">{{ $selectedApplication->o_level_credits_count ?? 5 }}/5 Passed</strong></span>
                            <span>School: <strong>{{ $selectedApplication->secondary_school ?? 'Queen Amina College' }}</strong></span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-center text-xs">
                            <div class="p-2 bg-white rounded border border-zinc-200 dark:bg-zinc-800">English: <strong>B2</strong></div>
                            <div class="p-2 bg-white rounded border border-zinc-200 dark:bg-zinc-800">Maths: <strong>B3</strong></div>
                            <div class="p-2 bg-white rounded border border-zinc-200 dark:bg-zinc-800">Biology: <strong>A1</strong></div>
                            <div class="p-2 bg-white rounded border border-zinc-200 dark:bg-zinc-800">Chemistry: <strong>B2</strong></div>
                            <div class="p-2 bg-white rounded border border-zinc-200 dark:bg-zinc-800">Physics: <strong>B3</strong></div>
                        </div>
                    </div>

                    <!-- Screening Score Form -->
                    <div>
                        <label class="block font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Composite Screening Score (0 - 100):</label>
                        <input type="number" step="0.5" wire:model.defer="screeningScore" class="w-full rounded-lg border border-zinc-300 bg-zinc-50 px-3 py-2 text-xs font-bold dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    </div>

                    <div>
                        <label class="block font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Official Screening Remarks:</label>
                        <textarea wire:model.defer="screeningRemarks" rows="2" class="w-full rounded-lg border border-zinc-300 bg-zinc-50 px-3 py-2 text-xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <button type="button" wire:click="closeReviewModal" class="rounded-xl border border-zinc-300 bg-white px-4 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 cursor-pointer">
                        Close
                    </button>
                    <button type="button" wire:click="saveScreening" class="rounded-xl bg-sky-700 px-4 py-2 text-xs font-bold text-white hover:bg-sky-600 cursor-pointer">
                        Save Screening
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
