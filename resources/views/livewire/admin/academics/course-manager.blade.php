<div class="py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Curriculum & Course Management</h1>
                <p class="text-xs text-zinc-500 mt-1 dark:text-zinc-400">Define course codes, credit loads, semester distributions, assigned academic lecturers, and course prerequisites.</p>
            </div>
            <div>
                <button wire:click="openCreateModal" class="inline-flex items-center gap-1.5 rounded-lg bg-sky-700 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-sky-600">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add New Course
                </button>
            </div>
        </div>

        <!-- Programme & Level Selector Bar -->
        <div class="mb-6 rounded-xl border border-zinc-200/80 bg-white p-4 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div>
                <label class="block text-[11px] font-bold uppercase text-zinc-400 mb-1">Programme</label>
                <select wire:model.live="selectedProgrammeId" class="block w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    @foreach($programmes as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->degree_type }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase text-zinc-400 mb-1">Academic Level</label>
                <select wire:model.live="selectedLevel" class="block w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    <option value="100">100 Level (Year 1)</option>
                    <option value="200">200 Level (Year 2)</option>
                    <option value="300">300 Level (Year 3)</option>
                    <option value="400">400 Level (Year 4)</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase text-zinc-400 mb-1">Semester</label>
                <select wire:model.live="selectedSemester" class="block w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    <option value="1">First Semester</option>
                    <option value="2">Second Semester</option>
                </select>
            </div>
        </div>

        <!-- Courses Table -->
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 bg-zinc-50 px-5 py-3 dark:border-zinc-800 dark:bg-zinc-800/60 flex items-center justify-between">
                <span class="text-xs font-bold text-zinc-700 dark:text-zinc-200">
                    Courses for {{ $selectedLevel }}L — Semester {{ $selectedSemester }}
                </span>
                <span class="rounded bg-sky-100 px-2 py-0.5 text-[11px] font-semibold text-sky-800 dark:bg-sky-950 dark:text-sky-300">
                    Total Credits: {{ $courses->sum('credit_units') }} Units
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-left text-xs dark:divide-zinc-800">
                    <thead class="bg-zinc-50/50 font-semibold text-zinc-600 dark:bg-zinc-800/40 dark:text-zinc-300">
                        <tr>
                            <th class="px-4 py-3">Code</th>
                            <th class="px-4 py-3">Course Title</th>
                            <th class="px-4 py-3">Units</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Assigned Lecturer</th>
                            <th class="px-4 py-3">Prerequisites</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($courses as $c)
                            <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition">
                                <td class="px-4 py-3 font-mono font-bold text-sky-700 dark:text-sky-300">
                                    {{ $c->code }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-zinc-900 dark:text-white">{{ $c->title }}</div>
                                    <div class="text-[11px] text-zinc-500 line-clamp-1">{{ $c->description }}</div>
                                </td>
                                <td class="px-4 py-3 font-bold text-zinc-800 dark:text-zinc-200">
                                    {{ $c->credit_units }}
                                </td>
                                <td class="px-4 py-3 uppercase">
                                    <span class="rounded px-1.5 py-0.5 text-[10px] font-bold {{ $c->course_type === 'core' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300' }}">
                                        {{ $c->course_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                    {{ $c->lecturer?->name ?? 'Unassigned' }}
                                </td>
                                <td class="px-4 py-3">
                                    @forelse($c->prerequisites as $pr)
                                        <span class="rounded bg-rose-50 px-1.5 py-0.5 text-[10px] font-mono text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-900/50">
                                            {{ $pr->code }}
                                        </span>
                                    @empty
                                        <span class="text-zinc-400">None</span>
                                    @endforelse
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-xs text-zinc-500">
                                    No courses configured for this level and semester. Click "Add New Course" to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Course Modal -->
        @if($showCreateModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-2xs">
                <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center justify-between border-b border-zinc-200 px-6 py-4 dark:border-zinc-800">
                        <h3 class="text-base font-bold text-zinc-900 dark:text-white">Add Curriculum Course</h3>
                        <button wire:click="closeCreateModal" class="text-zinc-400 hover:text-zinc-600">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form wire:submit="saveCourse" class="p-6 space-y-4 text-xs">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Course Code (e.g. NUR 301) *</label>
                                <input type="text" wire:model="code" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 uppercase dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                                @error('code') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Credit Units (1-6) *</label>
                                <input type="number" wire:model="credit_units" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                                @error('credit_units') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Course Title *</label>
                            <input type="text" wire:model="title" placeholder="e.g. Maternal & Child Health Nursing" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            @error('title') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Assigned Lecturer</label>
                                <select wire:model="lecturer_id" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                                    <option value="">Select Lecturer</option>
                                    @foreach($lecturers as $lec)
                                        <option value="{{ $lec->id }}">{{ $lec->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Course Type</label>
                                <select wire:model="course_type" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                                    <option value="core">Core (Compulsory)</option>
                                    <option value="required">Required</option>
                                    <option value="elective">Elective</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Course Prerequisite(s)</label>
                            <select wire:model="selectedPrerequisites" multiple class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 h-24 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                                @foreach($allCourses as $ac)
                                    <option value="{{ $ac->id }}">{{ $ac->code }} - {{ $ac->title }}</option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-zinc-400 mt-1">Hold Cmd/Ctrl to select multiple prerequisites.</p>
                        </div>

                        <div>
                            <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Course Syllabus / Description</label>
                            <textarea wire:model="description" rows="2" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                            <button type="button" wire:click="closeCreateModal" class="rounded-lg border border-zinc-300 px-4 py-2 font-semibold text-zinc-700 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                                Cancel
                            </button>
                            <button type="submit" class="rounded-lg bg-sky-700 px-4 py-2 font-semibold text-white hover:bg-sky-600">
                                Create Course
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</div>
