<div class="py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Preceptor Clinical Logbook Review</h1>
                <p class="text-xs text-zinc-500 mt-1 dark:text-zinc-400">Review clinical nursing procedures logged by student nurses during ward rotations and provide professional feedback.</p>
            </div>
            
            <div>
                <select wire:model.live="statusFilter" class="rounded-lg border border-zinc-300 px-3 py-2 text-xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    <option value="submitted">Submitted (Awaiting Preceptor Sign-off)</option>
                    <option value="approved">Approved & Signed</option>
                    <option value="rejected">Rejected / Needs Revision</option>
                    <option value="all">All Submissions</option>
                </select>
            </div>
        </div>

        <!-- Logbook Submissions Table -->
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-left text-xs dark:divide-zinc-800">
                    <thead class="bg-zinc-50 font-semibold text-zinc-600 dark:bg-zinc-800/60 dark:text-zinc-300">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Student Nurse</th>
                            <th class="px-4 py-3">Procedure Performed</th>
                            <th class="px-4 py-3">Rotation / Ward</th>
                            <th class="px-4 py-3">Self-Rating</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Review</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($logs as $log)
                            <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition">
                                <td class="px-4 py-3 font-mono text-zinc-600 dark:text-zinc-400">
                                    {{ $log->procedure_date?->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-zinc-900 dark:text-white">{{ $log->student?->full_name }}</div>
                                    <div class="text-[11px] font-mono text-zinc-500">{{ $log->student?->student_number }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-zinc-800 dark:text-zinc-200">{{ $log->procedure?->title }}</div>
                                    <div class="text-[11px] text-zinc-500 line-clamp-1">Ref: "{{ $log->student_reflection }}"</div>
                                </td>
                                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                    {{ $log->posting?->facility?->name }} • {{ $log->posting?->ward?->name ?? 'Ward' }}
                                </td>
                                <td class="px-4 py-3 uppercase">
                                    <span class="rounded bg-sky-50 px-1.5 py-0.5 text-[10px] font-bold text-sky-700 dark:bg-sky-950/60 dark:text-sky-300">
                                        {{ $log->competency_level }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <x-badge :type="$log->status">{{ ucfirst($log->status) }}</x-badge>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button wire:click="openReview({{ $log->id }})" class="rounded-md bg-teal-700 px-3 py-1 text-xs font-semibold text-white hover:bg-teal-600 shadow-2xs">
                                        Sign Off
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-xs text-zinc-500">
                                    No procedure logs waiting for sign-off in this category.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $logs->links() }}
            </div>
        </div>

        <!-- Preceptor Sign-off Modal -->
        @if($showReviewModal && $selectedLog)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-2xs">
                <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center justify-between border-b border-zinc-200 px-6 py-4 dark:border-zinc-800">
                        <div>
                            <h3 class="text-base font-bold text-zinc-900 dark:text-white">Sign Off Clinical Procedure</h3>
                            <p class="text-xs text-zinc-500 font-mono">{{ $selectedLog->student?->student_number }} • {{ $selectedLog->student?->full_name }}</p>
                        </div>
                        <button wire:click="closeReview" class="text-zinc-400 hover:text-zinc-600">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 text-xs">
                        <div class="rounded-xl bg-zinc-50 p-4 dark:bg-zinc-800/50 space-y-2">
                            <div><strong class="text-zinc-900 dark:text-white">Procedure:</strong> {{ $selectedLog->procedure?->title }}</div>
                            <div><strong class="text-zinc-900 dark:text-white">Date & Patient Code:</strong> {{ $selectedLog->procedure_date?->format('d M Y') }} • {{ $selectedLog->patient_reference_code ?? 'Confidential' }}</div>
                            <div><strong class="text-zinc-900 dark:text-white">Student Reflection:</strong> <span class="italic text-zinc-600 dark:text-zinc-300">"{{ $selectedLog->student_reflection }}"</span></div>
                            <div><strong class="text-zinc-900 dark:text-white">Candidate Self-Assessed Level:</strong> <span class="uppercase font-bold text-teal-600">{{ $selectedLog->competency_level }}</span></div>
                        </div>

                        <div>
                            <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Preceptor Remarks & Clinical Recommendations *</label>
                            <textarea wire:model="supervisorRemarks" rows="3" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-between border-t border-zinc-200 bg-zinc-50 px-6 py-3 dark:border-zinc-800 dark:bg-zinc-800/50">
                        <button type="button" wire:click="reject" class="rounded-lg border border-rose-300 bg-rose-50 px-3.5 py-1.5 font-semibold text-rose-700 hover:bg-rose-100 dark:border-rose-900/40 dark:bg-rose-950/40 dark:text-rose-300">
                            Reject / Request Revision
                        </button>
                        <button type="button" wire:click="approve" class="rounded-lg bg-emerald-600 px-4 py-1.5 font-semibold text-white hover:bg-emerald-500 shadow-2xs">
                            Sign Off & Approve Procedure
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
