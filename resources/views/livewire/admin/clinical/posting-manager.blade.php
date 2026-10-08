<div class="py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Clinical Hospital Rotations & Postings</h1>
                <p class="text-xs text-zinc-500 mt-1 dark:text-zinc-400">Manage teaching hospital unit allocations, monitor preceptor assignments, and enforce clinical capacity guidelines.</p>
            </div>
            <div>
                <button wire:click="openCreateModal" class="inline-flex items-center gap-1.5 rounded-lg bg-teal-700 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-teal-600">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Clinical Rotation
                </button>
            </div>
        </div>

        @if(session('error'))
            <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-800 dark:border-rose-900 dark:bg-rose-950/40 dark:text-rose-300">
                {{ session('error') }}
            </div>
        @endif

        <!-- Postings Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            @forelse($postings as $post)
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="rounded bg-teal-100 px-2 py-0.5 text-[10px] font-bold text-teal-800 dark:bg-teal-950 dark:text-teal-300">
                                Level {{ $post->level }}L • {{ $post->programme?->name }}
                            </span>
                            <x-badge type="success">{{ ucfirst($post->status) }}</x-badge>
                        </div>

                        <h3 class="mt-2 text-base font-bold text-zinc-900 dark:text-white">{{ $post->title }}</h3>
                        
                        <div class="mt-3 space-y-1.5 text-xs text-zinc-600 dark:text-zinc-300">
                            <div class="flex items-center gap-1.5">
                                <svg class="size-4 text-teal-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <strong>Facility:</strong> {{ $post->facility?->name }} (Ward: {{ $post->ward?->name ?? 'General Medical' }})
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg class="size-4 text-teal-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"/></svg>
                                <strong>Clinical Preceptor:</strong> {{ $post->supervisor?->name ?? 'Nurse Educator' }}
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg class="size-4 text-teal-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <strong>Rotation Period:</strong> {{ $post->start_date?->format('d M Y') }} – {{ $post->end_date?->format('d M Y') }}
                            </div>
                        </div>

                        <!-- Capacity Bar -->
                        <div class="mt-4">
                            <div class="flex items-center justify-between text-[11px] font-semibold text-zinc-500 mb-1">
                                <span>Students Assigned: {{ $post->students->count() }} of {{ $post->max_capacity }} max</span>
                                <span>{{ round(($post->students->count() / max(1, $post->max_capacity)) * 100) }}% Capacity</span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                                <div class="h-full bg-teal-500 rounded-full" style="width: {{ min(100, ($post->students->count() / max(1, $post->max_capacity)) * 100) }}%"></div>
                            </div>
                        </div>

                        <!-- Allocated Students Preview -->
                        <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                            <span class="text-[11px] font-bold text-zinc-400 uppercase">Allocated Students:</span>
                            <div class="mt-1 flex flex-wrap gap-1.5">
                                @forelse($post->students as $st)
                                    <span class="rounded bg-zinc-100 px-2 py-0.5 text-[11px] font-medium text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">
                                        {{ $st->full_name }} ({{ $st->student_number }})
                                    </span>
                                @empty
                                    <span class="text-xs text-zinc-400 italic">No students allocated yet.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end">
                        <button wire:click="openAssignModal({{ $post->id }})" class="rounded-lg border border-teal-600 bg-teal-50 px-3 py-1.5 text-xs font-semibold text-teal-700 hover:bg-teal-100 dark:bg-teal-950/40 dark:text-teal-300">
                            + Allocate Students to Ward
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-2 rounded-xl border border-zinc-200 bg-white p-8 text-center text-xs text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900">
                    No clinical postings configured yet.
                </div>
            @endforelse
        </div>

        <!-- Create Rotation Modal -->
        @if($showCreateModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-2xs">
                <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center justify-between border-b border-zinc-200 px-6 py-4 dark:border-zinc-800">
                        <h3 class="text-base font-bold text-zinc-900 dark:text-white">Create Hospital Rotation Posting</h3>
                        <button wire:click="closeCreateModal" class="text-zinc-400 hover:text-zinc-600">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form wire:submit="savePosting" class="p-6 space-y-4 text-xs">
                        <div>
                            <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Rotation Batch Title *</label>
                            <input type="text" wire:model="title" placeholder="e.g. 2026 Batch B - Medical Surgical Rotation" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            @error('title') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Hospital Facility *</label>
                                <select wire:model.live="facility_id" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                                    @foreach($facilities as $f)
                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Clinical Ward/Unit</label>
                                <select wire:model="ward_id" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                                    @foreach($wards as $w)
                                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Start Date *</label>
                                <input type="date" wire:model="start_date" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            </div>
                            <div>
                                <label class="block font-semibold text-zinc-700 dark:text-zinc-300">End Date *</label>
                                <input type="date" wire:model="end_date" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Preceptor / Supervisor</label>
                                <select wire:model="supervisor_id" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                                    <option value="">Select Preceptor</option>
                                    @foreach($supervisors as $sup)
                                        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Student Capacity Limit *</label>
                                <input type="number" wire:model="max_capacity" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Clinical Objectives</label>
                            <textarea wire:model="learning_objectives" rows="2" placeholder="Key skills students are expected to perform..." class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                            <button type="button" wire:click="closeCreateModal" class="rounded-lg border border-zinc-300 px-4 py-2 font-semibold text-zinc-700 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                                Cancel
                            </button>
                            <button type="submit" class="rounded-lg bg-teal-700 px-4 py-2 font-semibold text-white hover:bg-teal-600">
                                Save Posting
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Allocate Students Modal -->
        @if($showAssignModal && $selectedPosting)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-2xs">
                <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center justify-between border-b border-zinc-200 px-6 py-4 dark:border-zinc-800">
                        <h3 class="text-base font-bold text-zinc-900 dark:text-white">Allocate Students: {{ $selectedPosting->title }}</h3>
                        <button wire:click="closeAssignModal" class="text-zinc-400 hover:text-zinc-600">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-3 text-xs max-h-96 overflow-y-auto">
                        <p class="text-zinc-500">Select active students to assign to this clinical posting (Max remaining capacity: {{ $selectedPosting->max_capacity - $selectedPosting->students->count() }}):</p>
                        
                        @foreach($unassignedStudents as $st)
                            <label class="flex items-center gap-2 rounded-lg border border-zinc-200 p-2.5 dark:border-zinc-800 cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
                                <input type="checkbox" wire:model="selectedStudentIds" value="{{ $st->id }}" class="size-4 text-teal-600">
                                <div>
                                    <span class="font-bold text-zinc-900 dark:text-white">{{ $st->full_name }}</span>
                                    <span class="text-[11px] text-zinc-500 font-mono">({{ $st->student_number }} • {{ $st->currentLevel?->code }})</span>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-between border-t border-zinc-200 bg-zinc-50 px-6 py-3 dark:border-zinc-800 dark:bg-zinc-800/50">
                        <button type="button" wire:click="closeAssignModal" class="rounded-lg border border-zinc-300 px-4 py-2 font-semibold text-zinc-700 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                            Cancel
                        </button>
                        <button type="button" wire:click="assignStudents" class="rounded-lg bg-teal-700 px-4 py-2 font-semibold text-white hover:bg-teal-600">
                            Confirm Allocation
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
