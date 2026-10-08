<div class="py-8">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Semester Course Registration</h1>
                <p class="text-xs text-zinc-500 mt-1 dark:text-zinc-400">
                    {{ $currentSession?->name }} Academic Session • Semester {{ $semester }} • {{ $student?->programme?->name }} ({{ $student?->currentLevel?->code }})
                </p>
            </div>

            @if($existingRegistration && $existingRegistration->status === 'approved')
                <div class="flex items-center gap-2">
                    <button onclick="window.print()" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-300 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print Official Course Form
                    </button>
                    <x-badge type="success">Approved by HOD</x-badge>
                </div>
            @endif
        </div>

        @if(session('error'))
            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-800 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-300">
                {{ session('error') }}
            </div>
        @endif

        @if(!empty($validation['errors']))
            <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-800 dark:border-amber-900/60 dark:bg-amber-950/40 dark:text-amber-300">
                <div class="font-bold flex items-center gap-1.5 mb-1">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Registration Validation Notice:
                </div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($validation['errors'] as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Credit Load Indicator -->
        <div class="mb-6 rounded-xl border border-zinc-200/80 bg-white p-4 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase text-zinc-400">Selected Credit Load</span>
                <div class="text-xl font-black text-zinc-900 dark:text-white">
                    {{ $validation['total_credits'] }} <span class="text-xs text-zinc-400 font-normal">Credit Units</span>
                </div>
            </div>
            <div class="text-xs text-right text-zinc-500">
                <span>Minimum: 12 Units • Maximum: 26 Units</span>
                <div class="font-semibold {{ $validation['valid'] ? 'text-emerald-600' : 'text-amber-600' }}">
                    {{ $validation['valid'] ? 'Load within acceptable limits' : 'Adjust courses to satisfy limits' }}
                </div>
            </div>
        </div>

        <!-- Available Courses Checklist -->
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 bg-zinc-50 px-5 py-3 dark:border-zinc-800 dark:bg-zinc-800/60 font-bold text-xs text-zinc-700 dark:text-zinc-200">
                Prescribed Curriculum Courses for Level {{ $student?->currentLevel?->code }}
            </div>

            <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @forelse($availableCourses as $course)
                    <label class="p-4 flex items-start gap-3 hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 cursor-pointer transition">
                        <input 
                            type="checkbox" 
                            wire:model.live="selectedCourseIds" 
                            value="{{ $course->id }}" 
                            class="mt-1 size-4 rounded text-teal-600 focus:ring-teal-500"
                        >
                        <div class="flex-1 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-mono font-bold text-sky-700 dark:text-sky-300 text-sm">{{ $course->code }}</span>
                                <span class="font-bold text-zinc-900 dark:text-white">{{ $course->credit_units }} Units</span>
                            </div>
                            <h4 class="font-semibold text-zinc-900 dark:text-white mt-0.5">{{ $course->title }}</h4>
                            <p class="text-zinc-500 dark:text-zinc-400 text-[11px] mt-0.5">{{ $course->description }}</p>
                            
                            @if($course->prerequisites->isNotEmpty())
                                <div class="mt-2 flex items-center gap-1.5 text-[10px]">
                                    <span class="font-semibold text-zinc-400">Prerequisites:</span>
                                    @foreach($course->prerequisites as $pr)
                                        <span class="rounded bg-rose-50 px-1.5 py-0.5 font-mono text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300">
                                            {{ $pr->code }} ({{ $pr->title }})
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </label>
                @empty
                    <div class="p-8 text-center text-xs text-zinc-500">
                        No courses available for this semester.
                    </div>
                @endforelse
            </div>

            <!-- Submit footer -->
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/40 flex items-center justify-between">
                <span class="text-xs text-zinc-500">
                    Status: <x-badge :type="$existingRegistration?->status ?? 'draft'">{{ ucfirst($existingRegistration?->status ?? 'Not Submitted') }}</x-badge>
                </span>

                <button 
                    type="button" 
                    wire:click="submitRegistration" 
                    wire:loading.attr="disabled"
                    class="rounded-xl bg-teal-600 px-5 py-2 text-xs font-bold text-white shadow-xs hover:bg-teal-500 transition"
                >
                    <span wire:loading.remove>{{ $existingRegistration ? 'Update & Re-Submit Registration' : 'Submit Course Registration Form' }}</span>
                    <span wire:loading>Submitting...</span>
                </button>
            </div>
        </div>

    </div>
</div>
