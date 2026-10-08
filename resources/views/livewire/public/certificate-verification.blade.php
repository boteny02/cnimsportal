<div class="py-12">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                Institutional Public Registry • Anti-Fraud Security
            </span>
            <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-white">Student & Credential Verification</h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                Verify authentic enrollment and graduation status of nurses and midwives trained by the College.
            </p>
        </div>

        <div class="mt-8 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <form wire:submit="verify" class="flex gap-2">
                <input 
                    type="text" 
                    wire:model="student_number" 
                    placeholder="Enter Student Registration / Matric Number (e.g. CON/2026/00101)"
                    class="block w-full rounded-lg border border-zinc-300 px-4 py-2.5 text-sm shadow-xs focus:border-sky-500 focus:ring-sky-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                >
                <button type="submit" class="rounded-lg bg-emerald-700 px-6 py-2.5 text-sm font-semibold text-white hover:bg-emerald-600 shrink-0 shadow-xs">
                    Verify Student
                </button>
            </form>
            @error('student_number') <p class="mt-2 text-xs text-rose-500">{{ $message }}</p> @enderror
        </div>

        @if($searched)
            <div class="mt-8">
                @if($student)
                    <div class="overflow-hidden rounded-2xl border border-emerald-300/80 bg-white shadow-sm dark:border-emerald-800/80 dark:bg-zinc-900">
                        <div class="border-b border-emerald-100 bg-emerald-50/70 p-5 dark:border-emerald-900/60 dark:bg-emerald-950/40 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold">
                                    ✓
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider">Official Verification Notice</span>
                                    <h3 class="text-lg font-black text-zinc-900 dark:text-white">Authentic Student Record Verified</h3>
                                </div>
                            </div>
                            <x-badge type="success">
                                {{ strtoupper($student->status) }}
                            </x-badge>
                        </div>

                        <div class="p-6 space-y-4 text-sm">
                            <div class="grid grid-cols-2 gap-y-4 gap-x-6">
                                <div>
                                    <span class="text-xs text-zinc-400 font-medium">Student Full Name</span>
                                    <p class="text-base font-bold text-zinc-900 dark:text-white">{{ $student->full_name }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-zinc-400 font-medium">Matriculation / Student Number</span>
                                    <p class="text-base font-mono font-bold text-sky-700 dark:text-sky-300">{{ $student->student_number }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-zinc-400 font-medium">Accredited Programme</span>
                                    <p class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $student->programme?->name }} ({{ $student->programme?->degree_type }})</p>
                                </div>
                                <div>
                                    <span class="text-xs text-zinc-400 font-medium">Department</span>
                                    <p class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $student->department?->name }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-zinc-400 font-medium">Current Academic Level</span>
                                    <p class="text-zinc-700 dark:text-zinc-300">{{ $student->currentLevel?->name }} ({{ $student->currentLevel?->code }})</p>
                                </div>
                                <div>
                                    <span class="text-xs text-zinc-400 font-medium">Admission Academic Session</span>
                                    <p class="text-zinc-700 dark:text-zinc-300">{{ $student->entrySession?->name }} Session</p>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800 text-xs text-zinc-500">
                                This verification confirms that the above individual is formally registered with the College of Nursing Information Management System and recognized under the Nursing and Midwifery Council guidelines.
                            </div>
                        </div>
                    </div>
                @else
                    <div class="rounded-2xl border border-rose-200 bg-rose-50/50 p-8 text-center dark:border-rose-900/60 dark:bg-rose-950/20">
                        <svg class="mx-auto size-10 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <h3 class="mt-2 text-base font-bold text-rose-900 dark:text-rose-200">Record Not Found / Invalid Credentials</h3>
                        <p class="mt-1 text-xs text-rose-700 dark:text-rose-300">
                            The student matriculation number was not found in the official registry. Please confirm the number or contact the College Registrar's office.
                        </p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
