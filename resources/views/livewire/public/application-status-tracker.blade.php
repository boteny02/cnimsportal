<div class="py-12">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-white">Admission Status Verification</h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                Enter your application reference number (e.g. <code>APP/2026/0001-NURS</code>) or registered email address.
            </p>
        </div>

        <div class="mt-8 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <form wire:submit="checkStatus" class="flex gap-2">
                <input 
                    type="text" 
                    wire:model="search_query" 
                    placeholder="Enter Application Number (e.g. APP/2026/0001-NURS) or Email"
                    class="block w-full rounded-lg border border-zinc-300 px-4 py-2.5 text-sm shadow-xs focus:border-sky-500 focus:ring-sky-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                >
                <button type="submit" class="rounded-lg bg-sky-700 px-6 py-2.5 text-sm font-semibold text-white hover:bg-sky-600 shrink-0 shadow-xs">
                    Track Status
                </button>
            </form>
            @error('search_query') <p class="mt-2 text-xs text-rose-500">{{ $message }}</p> @enderror
        </div>

        @if($searched)
            <div class="mt-8">
                @if($application)
                    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <div class="border-b border-zinc-200 bg-zinc-50/70 p-5 dark:border-zinc-800 dark:bg-zinc-800/50 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Application File</span>
                                <h3 class="text-lg font-bold text-zinc-900 dark:text-white">{{ $application->application_number }}</h3>
                            </div>
                            <div>
                                <x-badge :type="$application->status">
                                    {{ ucwords(str_replace('_', ' ', $application->status)) }}
                                </x-badge>
                            </div>
                        </div>

                        <div class="p-6 space-y-4 text-sm">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-xs text-zinc-400 font-medium">Applicant Name</span>
                                    <p class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $application->full_name }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-zinc-400 font-medium">Programme Applied</span>
                                    <p class="font-semibold text-sky-700 dark:text-sky-300">{{ $application->programme?->name }} ({{ $application->programme?->degree_type }})</p>
                                </div>
                                <div>
                                    <span class="text-xs text-zinc-400 font-medium">Submission Timestamp</span>
                                    <p class="text-zinc-700 dark:text-zinc-300">{{ $application->submitted_at?->format('d M Y, h:i A') ?? 'Not submitted' }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-zinc-400 font-medium">Screening Score</span>
                                    <p class="text-zinc-700 dark:text-zinc-300 font-medium">
                                        {{ $application->screening_score ? $application->screening_score . ' / 100' : 'Awaiting screening results' }}
                                    </p>
                                </div>
                            </div>

                            @if($application->status === 'accepted')
                                <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-5 dark:border-emerald-900/60 dark:bg-emerald-950/40">
                                    <div class="flex items-start gap-3">
                                        <div class="rounded-full bg-emerald-600 p-1 text-white">
                                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-emerald-900 dark:text-emerald-100">Congratulations! Provisional Admission Offered</h4>
                                            <p class="mt-1 text-xs text-emerald-800 dark:text-emerald-300">
                                                You have been offered provisional admission into the {{ $application->programme?->name }} for the {{ $application->academicSession?->name }} Academic Session.
                                            </p>
                                            <div class="mt-3">
                                                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-700 px-3.5 py-1.5 text-xs font-semibold text-white shadow-xs hover:bg-emerald-600">
                                                    Log In to Student Portal to Accept & Pay Fees
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($application->status === 'shortlisted')
                                <div class="mt-4 rounded-xl border border-sky-200 bg-sky-50 p-5 dark:border-sky-900/60 dark:bg-sky-950/40">
                                    <h4 class="font-bold text-sky-900 dark:text-sky-100">Shortlisted for Oral Interview & Verification</h4>
                                    <p class="mt-1 text-xs text-sky-800 dark:text-sky-300">
                                        {{ $application->screening_remarks ?? 'You have successfully passed the initial computer-based screening. Please report to the College Examination Center with original credentials.' }}
                                    </p>
                                </div>
                            @else
                                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-900/60 dark:bg-amber-950/40">
                                    <h4 class="font-bold text-amber-900 dark:text-amber-100">Application Under Review</h4>
                                    <p class="mt-1 text-xs text-amber-800 dark:text-amber-300">
                                        Your documents and O'Level results are currently being scrutinized by the admissions screening committee. Please check back regularly.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="rounded-2xl border border-zinc-200 bg-white p-8 text-center dark:border-zinc-800 dark:bg-zinc-900">
                        <svg class="mx-auto size-10 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h3 class="mt-2 text-sm font-semibold text-zinc-900 dark:text-white">No Application Record Found</h3>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Please verify your application reference or email address.</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
