<div class="py-8">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Official Examination Statement of Results</h1>
                <p class="text-xs text-zinc-500 mt-1 dark:text-zinc-400">Institutional academic transcript slip approved by the Academic Examination Board.</p>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-300 bg-white px-3.5 py-1.5 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print Result Slip
                </button>
            </div>
        </div>

        <!-- Session Selector -->
        <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 gap-3 rounded-xl border border-zinc-200/80 bg-white p-4 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
            <div>
                <label class="block text-[11px] font-bold uppercase text-zinc-400 mb-1">Academic Session</label>
                <select wire:model.live="selectedSessionId" class="block w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    @foreach($sessions as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} Session</option>
                    @endforeach
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

        <!-- Printable Official Slip Container -->
        <div class="overflow-hidden rounded-2xl border border-zinc-300/80 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900 print:border-none print:shadow-none">
            
            <!-- Slip Header -->
            <div class="border-b border-zinc-200 bg-zinc-50 p-6 text-center dark:border-zinc-800 dark:bg-zinc-800/40">
                <div class="size-12 rounded-xl bg-sky-700 text-white font-black text-xl flex items-center justify-center mx-auto mb-2">
                    C
                </div>
                <h2 class="text-lg font-black tracking-tight text-zinc-900 dark:text-white uppercase">College of Nursing & Midwifery Sciences</h2>
                <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">Office of the Registrar & Controller of Examinations</p>
                <span class="mt-2 inline-block rounded bg-sky-100 px-2.5 py-0.5 text-xs font-bold text-sky-800 font-mono dark:bg-sky-950 dark:text-sky-300">
                    STATEMENT OF EXAMINATION RESULTS
                </span>
            </div>

            <!-- Student Metadata Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-6 border-b border-zinc-200 dark:border-zinc-800 text-xs">
                <div>
                    <span class="text-zinc-400 font-medium block">Candidate Name:</span>
                    <strong class="text-zinc-900 dark:text-white text-sm">{{ $student?->full_name }}</strong>
                </div>
                <div>
                    <span class="text-zinc-400 font-medium block">Matriculation No:</span>
                    <strong class="font-mono text-sky-700 dark:text-sky-300 text-sm">{{ $student?->student_number }}</strong>
                </div>
                <div>
                    <span class="text-zinc-400 font-medium block">Programme:</span>
                    <strong class="text-zinc-800 dark:text-zinc-200">{{ $student?->programme?->name }}</strong>
                </div>
                <div>
                    <span class="text-zinc-400 font-medium block">Academic Session:</span>
                    <strong class="text-zinc-800 dark:text-zinc-200">{{ $currentSession?->name }} (Sem {{ $selectedSemester }})</strong>
                </div>
            </div>

            <!-- Results Table -->
            <div class="p-6">
                <table class="min-w-full divide-y divide-zinc-200 text-left text-xs dark:divide-zinc-800">
                    <thead class="font-semibold text-zinc-500 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="py-2.5">Course Code & Title</th>
                            <th class="py-2.5">Attempt</th>
                            <th class="py-2.5">Units</th>
                            <th class="py-2.5">CA (30)</th>
                            <th class="py-2.5">Exam (70)</th>
                            <th class="py-2.5">Total (100)</th>
                            <th class="py-2.5">Grade</th>
                            <th class="py-2.5">Grade Pt</th>
                            <th class="py-2.5">Credit Pts</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse($results as $res)
                            <tr>
                                <td class="py-3 font-medium text-zinc-900 dark:text-white">
                                    <span class="font-mono font-bold text-sky-700 dark:text-sky-300 mr-1">{{ $res->course?->code }}</span>
                                    {{ $res->course?->title }}
                                </td>
                                <td class="py-3">
                                    @if($res->attempt_type?->value === 'CARRYOVER')
                                        <span class="rounded bg-rose-100 px-2 py-0.5 text-[10px] font-bold text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                            Carryover (Att. {{ $res->attempt_number }})
                                        </span>
                                    @elseif($res->attempt_type?->value === 'REPEAT')
                                        <span class="rounded bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                            Repeat (Att. {{ $res->attempt_number }})
                                        </span>
                                    @else
                                        <span class="rounded bg-sky-100 px-2 py-0.5 text-[10px] font-semibold text-sky-800 dark:bg-sky-950 dark:text-sky-300">
                                            1st Attempt
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 font-semibold">{{ $res->course?->credit_units }}</td>
                                <td class="py-3 text-zinc-600 dark:text-zinc-300">{{ $res->ca_score }}</td>
                                <td class="py-3 text-zinc-600 dark:text-zinc-300">{{ $res->exam_score }}</td>
                                <td class="py-3 font-bold text-zinc-900 dark:text-white">{{ $res->total_score }}</td>
                                <td class="py-3 font-mono font-black text-sm text-sky-600 dark:text-sky-400">{{ $res->grade }}</td>
                                <td class="py-3 font-mono">{{ number_format($res->grade_point, 1) }}</td>
                                <td class="py-3 font-mono font-semibold">{{ number_format($res->credit_points, 1) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-xs text-zinc-400 italic">
                                    No examination results recorded for this semester session.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Summary Bar -->
                <div class="mt-6 rounded-xl border border-zinc-200 bg-zinc-50/70 p-4 dark:border-zinc-800 dark:bg-zinc-800/40 grid grid-cols-2 sm:grid-cols-5 gap-3 text-xs text-center">
                    <div>
                        <span class="text-zinc-400 font-medium">Credits Registered</span>
                        <div class="text-base font-bold text-zinc-900 dark:text-white">{{ $summary?->credits_registered ?? $results->sum('course.credit_units') }} Units</div>
                    </div>
                    <div>
                        <span class="text-zinc-400 font-medium">Credits Earned</span>
                        <div class="text-base font-bold text-emerald-600">{{ $summary?->credits_earned ?? $results->where('grade_point', '>=', 2.0)->sum('course.credit_units') }} Units</div>
                    </div>
                    <div>
                        <span class="text-zinc-400 font-medium">Semester GPA</span>
                        <div class="text-lg font-black font-mono text-sky-700 dark:text-sky-300">{{ number_format($summary?->gpa ?? 5.00, 2) }}</div>
                    </div>
                    <div>
                        <span class="text-zinc-400 font-medium">Cumulative CGPA</span>
                        <div class="text-lg font-black font-mono text-teal-700 dark:text-teal-300">{{ number_format($summary?->cgpa ?? 5.00, 2) }}</div>
                    </div>
                    <div>
                        <span class="text-zinc-400 font-medium">Academic Standing</span>
                        <div class="text-xs font-black uppercase text-indigo-700 dark:text-indigo-300 mt-1">{{ $summary?->academic_standing ?? 'Good Standing' }}</div>
                    </div>
                </div>

                <!-- Signatures & Accreditation Footer -->
                <div class="mt-10 pt-6 border-t border-zinc-200 dark:border-zinc-800 grid grid-cols-2 gap-8 text-center text-xs">
                    <div>
                        <div class="border-b border-zinc-400 w-48 mx-auto pb-6"></div>
                        <span class="mt-1 block font-semibold text-zinc-700 dark:text-zinc-300">Dr. Emmanuel Adeyemi</span>
                        <span class="text-[11px] text-zinc-400">Head of Department (Nursing)</span>
                    </div>
                    <div>
                        <div class="border-b border-zinc-400 w-48 mx-auto pb-6"></div>
                        <span class="mt-1 block font-semibold text-zinc-700 dark:text-zinc-300">Prof. Fatima Bello</span>
                        <span class="text-[11px] text-zinc-400">Academic Board Chairman / Provost</span>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
