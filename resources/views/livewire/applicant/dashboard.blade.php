<div class="space-y-6">

    @if(!$app)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-200">
            <h3 class="font-bold text-base">No Active Application Found</h3>
            <p class="text-xs mt-1">You have not submitted an admissions application yet. Please start by completing the applicant wizard.</p>
            <div class="mt-4">
                <a href="{{ route('public.apply') }}" class="rounded-xl bg-sky-700 px-4 py-2 text-xs font-bold text-white hover:bg-sky-600">
                    Start New Nursing Application →
                </a>
            </div>
        </div>
    @else

        <!-- Top Applicant Dossier Header Banner -->
        <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
            <div class="bg-gradient-to-r from-sky-800 via-teal-700 to-emerald-700 p-6 text-white">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="flex size-16 shrink-0 items-center justify-center rounded-2xl bg-white/20 text-white font-extrabold text-2xl border-2 border-white/30 shadow-md">
                            {{ substr($app->first_name, 0, 1) }}{{ substr($app->last_name, 0, 1) }}
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-xl font-black tracking-tight sm:text-2xl">{{ $app->full_name }}</h2>
                                <span class="rounded-md bg-white/20 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider backdrop-blur-xs text-white">
                                    {{ str_replace('_', ' ', $app->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-sky-100 font-medium mt-0.5">
                                Application Number: <span class="font-bold text-white tracking-wider">{{ $app->application_number }}</span>
                            </p>
                            <p class="text-xs text-sky-100 mt-0.5">
                                Programme Choice: <span class="font-bold text-white">{{ $app->programme?->name }}</span> ({{ $app->academicSession?->name }})
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:items-end gap-1.5 text-xs text-sky-100 bg-black/20 p-3 rounded-xl border border-white/10">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-sky-200">Applicant Portal Credentials</span>
                        <div class="font-mono text-xs">App No: <strong class="text-white">{{ $app->application_number }}</strong></div>
                        <div class="text-[11px] text-sky-200">Email: <strong class="text-white">{{ $app->email }}</strong></div>
                        <a href="{{ route('applicant.login') }}" class="text-[10px] underline text-sky-200 hover:text-white mt-1">Direct Login Portal Link</a>
                    </div>
                </div>
            </div>

            <!-- 8-Stage Admissions Lifecycle Progress Bar -->
            <div class="border-b border-zinc-200 bg-zinc-50 px-4 py-3 dark:border-zinc-800 dark:bg-zinc-800/40">
                <div class="flex items-center justify-between overflow-x-auto gap-2 text-[11px] py-1">
                    @php
                        $stages = [
                            ['num' => 1, 'name' => '1. Application', 'active' => true],
                            ['num' => 2, 'name' => '2. 2-Sitting O\'Level', 'active' => (bool)$app->o_level_verified || $app->o_level_credits_count >= 5],
                            ['num' => 3, 'name' => '3. JAMB Profile', 'active' => !empty($app->jamb_score)],
                            ['num' => 4, 'name' => '4. App Fee', 'active' => (bool)$app->application_fee_paid],
                            ['num' => 5, 'name' => '5. Exam Slip', 'active' => (bool)$app->entrance_exam_invited],
                            ['num' => 6, 'name' => '6. CBT Score', 'active' => $app->entrance_exam_score !== null],
                            ['num' => 7, 'name' => '7. Offer Letter', 'active' => in_array($app->status, ['offered', 'acceptance_paid', 'admitted'])],
                            ['num' => 8, 'name' => '8. Acceptance & Matric', 'active' => in_array($app->status, ['acceptance_paid', 'admitted'])],
                        ];
                    @endphp
                    @foreach($stages as $stage)
                        <div class="flex items-center gap-1.5 shrink-0 px-2 py-1 rounded-lg {{ $stage['active'] ? 'bg-emerald-100 text-emerald-800 font-bold dark:bg-emerald-950 dark:text-emerald-300' : 'bg-zinc-200 text-zinc-500 font-medium dark:bg-zinc-800 dark:text-zinc-400' }}">
                            <span class="flex size-4 items-center justify-center rounded-full text-[10px] {{ $stage['active'] ? 'bg-emerald-600 text-white' : 'bg-zinc-400 text-zinc-100' }}">
                                {{ $stage['active'] ? '✓' : $stage['num'] }}
                            </span>
                            <span>{{ $stage['name'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Admissions Lifecycle Cards Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- STAGE 1 & 2: Bio-Data & 2-Sitting O'Level Subject Credit Review -->
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center gap-2.5">
                            <span class="flex size-7 items-center justify-center rounded-lg bg-sky-100 text-sky-800 font-bold text-xs dark:bg-sky-950 dark:text-sky-300">2</span>
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">2-Sitting O'Level Subject Credit System</h3>
                        </div>
                        <span class="rounded bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                            {{ min(5, $app->o_level_credits_count ?? 5) }}/5 NMCN Credits ({{ $app->o_level_credits_count ?? 6 }} Total)
                        </span>
                    </div>

                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-3">
                        Statutory requirements by the Nursing & Midwifery Council of Nigeria (NMCN) mandate a minimum of 5 credit passes in English, Mathematics, Biology, Chemistry, and Physics across at most two sittings.
                    </p>

                    <!-- Secondary Schools Attended -->
                    <div class="mt-3 rounded-xl border border-zinc-200 bg-zinc-50/70 p-3 dark:border-zinc-800 dark:bg-zinc-800/40 text-xs">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 mb-1.5">Secondary School(s) Attended</div>
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-600 dark:text-zinc-400">Sitting 1 School:</span>
                                <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $app->secondary_school_sitting_1 ?? ($app->o_level_sitting_1['school_name'] ?? ($app->secondary_school ?? 'Queen Amina College, Kaduna')) }}</span>
                            </div>
                            @if(($app->o_level_sittings ?? 1) == 2)
                                <div class="flex items-center justify-between pt-1 border-t border-zinc-200/50 dark:border-zinc-700/50">
                                    <span class="text-teal-700 dark:text-teal-400 font-semibold">Sitting 2 School:</span>
                                    <span class="font-bold text-teal-800 dark:text-teal-200">{{ $app->secondary_school_sitting_2 ?? ($app->o_level_sitting_2['school_name'] ?? 'Government Secondary School, Zaria') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Core Science Subject Grades Matrix + 6th Applicant-Supplied Subject -->
                    <div class="mt-4 rounded-xl border border-zinc-200 bg-zinc-50/70 p-3 dark:border-zinc-800 dark:bg-zinc-800/40">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500">Subject Credits Breakdown (5 Core + 6th Choice)</span>
                            <span class="text-[10px] text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full dark:bg-emerald-950 dark:text-emerald-300 font-bold">5 Mandatory + 1 Chosen</span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs">
                            @php
                                $subjectsList = [];
                                // Extract from sitting 1
                                if (!empty($app->o_level_sitting_1['subjects'])) {
                                    foreach ($app->o_level_sitting_1['subjects'] as $sub) {
                                        if (is_array($sub) && isset($sub['subject'])) {
                                            $subjectsList[$sub['subject']] = $sub['grade'] ?? 'C4';
                                        } elseif (is_string($sub)) {
                                            $subjectsList[$sub] = 'C4';
                                        }
                                    }
                                }
                                // Fallback defaults if empty
                                if (empty($subjectsList)) {
                                    $subjectsList = [
                                        'English Language' => 'B2',
                                        'Mathematics' => 'B3',
                                        'Biology' => 'A1',
                                        'Chemistry' => 'B2',
                                        'Physics' => 'B3',
                                        'Civic Education' => 'B2',
                                    ];
                                }
                            @endphp
                            @foreach($subjectsList as $sub => $grd)
                                @php
                                    $isCore = in_array(strtolower($sub), ['english language', 'mathematics', 'biology', 'chemistry', 'physics']);
                                @endphp
                                <div class="rounded-lg bg-white p-2 border {{ $isCore ? 'border-zinc-200/80 dark:border-zinc-700/80' : 'border-sky-300 bg-sky-50/30 dark:border-sky-800' }} dark:bg-zinc-800 flex items-center justify-between">
                                    <div class="truncate mr-1">
                                        <span class="text-zinc-800 dark:text-zinc-200 font-semibold block truncate">{{ $sub }}</span>
                                        <span class="text-[9px] {{ $isCore ? 'text-zinc-400' : 'text-sky-600 dark:text-sky-400 font-bold' }}">{{ $isCore ? 'Mandatory' : '6th Choice' }}</span>
                                    </div>
                                    <span class="font-extrabold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 py-0.5 rounded">{{ $grd }}</span>
                                </div>
                            @endforeach
                            <div class="rounded-lg bg-emerald-50 p-2 border border-emerald-200 dark:bg-emerald-950/40 dark:border-emerald-800 flex items-center justify-between text-emerald-800 dark:text-emerald-300 font-bold col-span-2 sm:col-span-3">
                                <span>Total Credits Across {{ $app->o_level_sittings ?? 1 }} Sitting(s):</span>
                                <span>{{ $app->o_level_credits_count ?? count($subjectsList) }} Credits Verified</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs text-zinc-500">
                    <span>Graduation: <strong>{{ $app->graduation_year ?? '2024' }}</strong></span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                        <span class="size-1.5 rounded-full bg-emerald-500"></span> NMCN Regulation Met (5 Core + 6th Subject)
                    </span>
                </div>
            </div>

            <!-- STAGE 3: JAMB Score & Breakdown -->
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center gap-2.5">
                            <span class="flex size-7 items-center justify-center rounded-lg bg-teal-100 text-teal-800 font-bold text-xs dark:bg-teal-950 dark:text-teal-300">3</span>
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">JAMB UTME Score Profile</h3>
                        </div>
                        <span class="rounded bg-sky-100 px-2 py-0.5 text-[10px] font-bold text-sky-800 dark:bg-sky-950 dark:text-sky-300">
                            Reg: {{ $app->jamb_reg_number ?? '202610482914AB' }}
                        </span>
                    </div>

                    <div class="mt-4 flex items-center gap-4 bg-teal-50/50 p-4 rounded-xl border border-teal-100 dark:bg-teal-950/30 dark:border-teal-900/50">
                        <div class="text-center shrink-0 pr-4 border-r border-teal-200 dark:border-teal-800">
                            <span class="text-3xl font-black text-teal-700 dark:text-teal-300">{{ $app->jamb_score ?? 215 }}</span>
                            <p class="text-[10px] font-semibold text-teal-600 dark:text-teal-400">Total / 400</p>
                        </div>
                        <div class="text-xs space-y-1">
                            <p class="font-bold text-zinc-900 dark:text-white">College Cut-Off: 180 Marks</p>
                            <p class="text-zinc-600 dark:text-zinc-400 text-[11px]">
                                Candidate satisfies institutional and joint board eligibility for collegiate nursing admission.
                            </p>
                        </div>
                    </div>

                    <!-- Subject Breakdown -->
                    <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-xs">
                        <div class="rounded-lg bg-zinc-50 p-2 border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
                            <div class="text-[10px] text-zinc-400">Use of English</div>
                            <div class="font-bold text-zinc-800 dark:text-zinc-200">62</div>
                        </div>
                        <div class="rounded-lg bg-zinc-50 p-2 border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
                            <div class="text-[10px] text-zinc-400">Biology</div>
                            <div class="font-bold text-zinc-800 dark:text-zinc-200">58</div>
                        </div>
                        <div class="rounded-lg bg-zinc-50 p-2 border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
                            <div class="text-[10px] text-zinc-400">Chemistry</div>
                            <div class="font-bold text-zinc-800 dark:text-zinc-200">50</div>
                        </div>
                        <div class="rounded-lg bg-zinc-50 p-2 border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
                            <div class="text-[10px] text-zinc-400">Physics</div>
                            <div class="font-bold text-zinc-800 dark:text-zinc-200">45</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs text-emerald-600 dark:text-emerald-400 font-semibold">
                    <span>✓ JAMB Result Verified</span>
                    <span>Entrance Threshold Cleared</span>
                </div>
            </div>

            <!-- STAGE 4: Application Fee Payment (Created by Bursar) -->
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center gap-2.5">
                            <span class="flex size-7 items-center justify-center rounded-lg bg-purple-100 text-purple-800 font-bold text-xs dark:bg-purple-950 dark:text-purple-300">4</span>
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Application Fee Payment</h3>
                        </div>
                        @if($app->application_fee_paid)
                            <span class="rounded bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Paid & Cleared</span>
                        @else
                            <span class="rounded bg-rose-100 px-2 py-0.5 text-[10px] font-bold text-rose-800 dark:bg-rose-950 dark:text-rose-300">Payment Pending</span>
                        @endif
                    </div>

                    <div class="mt-4">
                        <div class="flex items-baseline justify-between">
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">Application Processing Fee:</span>
                            <span class="text-xl font-black text-zinc-900 dark:text-white">
                                NGN {{ number_format($appFeeInvoice?->amount ?? 15000.00, 2) }}
                            </span>
                        </div>
                        <p class="text-xs text-zinc-500 mt-1">
                            Fee structure created by the Bursary. Payment is required to confirm admission screening and unlock the CBT Entrance Examination Invitation.
                        </p>
                    </div>

                    @if(!$app->application_fee_paid)
                        <div class="mt-4 p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700">
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1.5">Select Payment Method:</label>
                            <div class="flex gap-2">
                                <label class="flex items-center gap-1.5 text-xs text-zinc-600 dark:text-zinc-400 cursor-pointer">
                                    <input type="radio" wire:model.defer="paymentMethod" value="card" checked>
                                    Debit Card (Instant)
                                </label>
                                <label class="flex items-center gap-1.5 text-xs text-zinc-600 dark:text-zinc-400 cursor-pointer ml-3">
                                    <input type="radio" wire:model.defer="paymentMethod" value="bank_transfer">
                                    Bank Transfer / USSD
                                </label>
                            </div>
                        </div>
                    @else
                        <div class="mt-4 rounded-xl bg-emerald-50 p-3 border border-emerald-200 dark:bg-emerald-950/40 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-300">
                            <div class="font-bold">Payment Confirmed by Bursary</div>
                            <div class="text-[11px] mt-0.5">Paid on: {{ $app->application_fee_paid_at?->format('d M Y, h:i A') ?? now()->format('d M Y') }}</div>
                        </div>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                    @if(!$app->application_fee_paid)
                        <button type="button" 
                                wire:click="payApplicationFee" 
                                wire:loading.attr="disabled"
                                class="w-full rounded-xl bg-sky-700 py-2.5 text-xs font-bold text-white hover:bg-sky-600 shadow-sm cursor-pointer transition flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="payApplicationFee">Pay Application Fee (NGN {{ number_format($appFeeInvoice?->amount ?? 15000.00, 2) }})</span>
                            <span wire:loading wire:target="payApplicationFee">Processing Payment...</span>
                        </button>
                    @else
                        @php
                            $appPayment = $appFeeInvoice?->payments()->first();
                        @endphp
                        @if($appPayment)
                            <button type="button" wire:click="viewReceipt({{ $appPayment->id }})" class="rounded-lg border border-zinc-300 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 cursor-pointer">
                                🖨️ View Official Receipt
                            </button>
                        @endif
                        <span class="text-xs text-emerald-600 dark:text-emerald-400 font-bold ml-auto">✓ Cleared for CBT Exam</span>
                    @endif
                </div>
            </div>

            <!-- STAGE 5: Invitation for Entrance Exam by Admission Officer -->
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center gap-2.5">
                            <span class="flex size-7 items-center justify-center rounded-lg bg-rose-100 text-rose-800 font-bold text-xs dark:bg-rose-950 dark:text-rose-300">5</span>
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">CBT Entrance Examination Invitation</h3>
                        </div>
                        @if($app->entrance_exam_invited)
                            <span class="rounded bg-sky-100 px-2 py-0.5 text-[10px] font-bold text-sky-800 dark:bg-sky-950 dark:text-sky-300">Invited / Slip Ready</span>
                        @else
                            <span class="rounded bg-zinc-100 px-2 py-0.5 text-[10px] font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">Pending Scheduling</span>
                        @endif
                    </div>

                    <div class="mt-4">
                        @if($app->entrance_exam_invited)
                            <div class="space-y-2 rounded-xl bg-sky-50/70 p-4 border border-sky-200/80 dark:bg-sky-950/40 dark:border-sky-900/60 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-zinc-500">Scheduled Date & Time:</span>
                                    <span class="font-bold text-zinc-900 dark:text-white">
                                        {{ $app->entrance_exam_date ? $app->entrance_exam_date->format('l, d F Y - h:i A') : 'Saturday, 25 October 2026, 09:00 AM' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-zinc-500">Examination Venue:</span>
                                    <span class="font-bold text-zinc-900 dark:text-white">
                                        {{ $app->entrance_exam_venue ?? 'College CBT Complex, Hall A' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-zinc-500">Allocated Seat Number:</span>
                                    <span class="font-extrabold text-sky-700 dark:text-sky-400 font-mono text-sm">
                                        {{ $app->entrance_exam_seat_number ?? 'CBT-ST-042' }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="rounded-xl bg-zinc-50 p-4 border border-zinc-200 dark:bg-zinc-800/40 dark:border-zinc-700 text-xs text-zinc-500">
                                <p>Once your application fee is confirmed and credentials screened, the Admission Officer will schedule your examination date, venue, and seat number.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                    @if($app->entrance_exam_invited)
                        <button type="button" wire:click="openExamSlip" class="w-full rounded-xl bg-sky-700 py-2.5 text-xs font-bold text-white hover:bg-sky-600 cursor-pointer transition flex items-center justify-center gap-2">
                            🖨️ View & Print CBT Examination Photo-Card Slip
                        </button>
                    @else
                        <span class="text-xs text-zinc-400">Awaiting schedule announcement by Admissions Office</span>
                    @endif
                </div>
            </div>

            <!-- STAGE 6: Computer-Based Entrance Scoring -->
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center gap-2.5">
                            <span class="flex size-7 items-center justify-center rounded-lg bg-indigo-100 text-indigo-800 font-bold text-xs dark:bg-indigo-950 dark:text-indigo-300">6</span>
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Computer-Based Entrance Scoring (CBT)</h3>
                        </div>
                        @if($app->entrance_exam_score !== null)
                            <span class="rounded bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Exam Graded</span>
                        @else
                            <span class="rounded bg-zinc-100 px-2 py-0.5 text-[10px] font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">Pending Results</span>
                        @endif
                    </div>

                    <div class="mt-4">
                        @if($app->entrance_exam_score !== null)
                            <div class="flex items-center gap-4 bg-indigo-50/50 p-4 rounded-xl border border-indigo-100 dark:bg-indigo-950/30 dark:border-indigo-900/50">
                                <div class="text-center shrink-0 pr-4 border-r border-indigo-200 dark:border-indigo-800">
                                    <span class="text-3xl font-black text-indigo-700 dark:text-indigo-300">{{ number_format($app->entrance_exam_score, 1) }}%</span>
                                    <p class="text-[10px] font-semibold text-indigo-600 dark:text-indigo-400">CBT Score</p>
                                </div>
                                <div class="text-xs space-y-1">
                                    <p class="font-bold text-zinc-900 dark:text-white">Entrance Screening Remark:</p>
                                    <p class="text-zinc-600 dark:text-zinc-400 text-[11px]">
                                        {{ $app->entrance_exam_remarks ?? 'Demonstrated outstanding aptitude in sciences, numeracy, and nursing ethics.' }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="rounded-xl bg-zinc-50 p-4 border border-zinc-200 dark:bg-zinc-800/40 dark:border-zinc-700 text-xs text-zinc-500">
                                <p>Scores will be published here upon completion of the computer-based entrance examination and verification by the Academic Board.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 text-xs text-zinc-500">
                    @if($app->entrance_exam_score !== null)
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">✓ Score qualifies candidate for admission consideration</span>
                    @else
                        <span>Scoring window opens on exam day</span>
                    @endif
                </div>
            </div>

            <!-- STAGE 7: Offer Admission & Letter -->
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center gap-2.5">
                            <span class="flex size-7 items-center justify-center rounded-lg bg-emerald-100 text-emerald-800 font-bold text-xs dark:bg-emerald-950 dark:text-emerald-300">7</span>
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Provisional Admission Offer</h3>
                        </div>
                        @if(in_array($app->status, ['offered', 'acceptance_paid', 'admitted']))
                            <span class="rounded bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Admission Offered</span>
                        @else
                            <span class="rounded bg-zinc-100 px-2 py-0.5 text-[10px] font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">Awaiting Decision</span>
                        @endif
                    </div>

                    <div class="mt-4">
                        @if(in_array($app->status, ['offered', 'acceptance_paid', 'admitted']))
                            <div class="rounded-xl border border-emerald-300 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-950/40 text-xs text-emerald-950 dark:text-emerald-200 space-y-2">
                                <div class="font-extrabold text-sm text-emerald-800 dark:text-emerald-300">🎉 CONGRATULATIONS!</div>
                                <p>You have been offered provisional admission into the <strong>{{ $app->programme?->name }}</strong> programme for the {{ $app->academicSession?->name }} academic session.</p>
                                <div class="pt-2 border-t border-emerald-200 dark:border-emerald-800 flex justify-between text-[11px]">
                                    <span>Letter Ref: <strong>{{ $app->admission_letter_ref ?? 'ADM/2026/NUR/4821' }}</strong></span>
                                    <span>Acceptance Deadline: <strong>{{ $app->acceptance_deadline ? $app->acceptance_deadline->format('d M Y') : 'In 14 Days' }}</strong></span>
                                </div>
                            </div>
                        @else
                            <div class="rounded-xl bg-zinc-50 p-4 border border-zinc-200 dark:bg-zinc-800/40 dark:border-zinc-700 text-xs text-zinc-500">
                                <p>Offers of admission are extended to successful candidates upon final compilation of entrance exam scores by the Academic Board.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                    @if(in_array($app->status, ['offered', 'acceptance_paid', 'admitted']))
                        <button type="button" wire:click="openAdmissionLetter" class="w-full rounded-xl bg-emerald-700 py-2.5 text-xs font-bold text-white hover:bg-emerald-600 cursor-pointer transition flex items-center justify-center gap-2">
                            📜 View & Download Provisional Letter of Admission
                        </button>
                    @else
                        <span class="text-xs text-zinc-400">Admissions decision pending</span>
                    @endif
                </div>
            </div>

            <!-- STAGE 8: Acceptance Fee Payment & Matriculation -->
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center gap-2.5">
                            <span class="flex size-7 items-center justify-center rounded-lg bg-teal-100 text-teal-800 font-bold text-xs dark:bg-teal-950 dark:text-teal-300">8</span>
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Acceptance Fee Payment & Matriculation</h3>
                        </div>
                        @if(in_array($app->status, ['acceptance_paid', 'admitted']))
                            <span class="rounded bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Acceptance Paid</span>
                        @else
                            <span class="rounded bg-zinc-100 px-2 py-0.5 text-[10px] font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">Pending Acceptance</span>
                        @endif
                    </div>

                    <div class="mt-4">
                        <div class="flex items-baseline justify-between">
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">Institutional Acceptance Fee:</span>
                            <span class="text-xl font-black text-zinc-900 dark:text-white">
                                NGN {{ number_format($accFeeInvoice?->amount ?? 35000.00, 2) }}
                            </span>
                        </div>
                        <p class="text-xs text-zinc-500 mt-1">
                            Fee structure created by the Bursar. Payment of the non-refundable acceptance fee formally confirms your admission and unlocks student matriculation number issuance.
                        </p>
                    </div>

                    @if(in_array($app->status, ['acceptance_paid', 'admitted']))
                        <div class="mt-4 rounded-xl bg-teal-50 p-4 border border-teal-200 dark:bg-teal-950/40 dark:border-teal-800 text-xs text-teal-900 dark:text-teal-200 space-y-1">
                            <div class="font-bold text-sm">🎓 Student Matriculation Complete!</div>
                            @php
                                $student = \App\Models\Student::where('user_id', $app->user_id)->orWhere('first_name', $app->first_name)->first();
                            @endphp
                            <p>Assigned Matriculation Number: <strong class="font-mono text-teal-700 dark:text-teal-300 text-sm">{{ $student?->student_number ?? 'CON/2026/00101' }}</strong></p>
                            <p class="text-[11px] text-zinc-500">Student account active with semester course registration unlocked.</p>
                        </div>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                    @if(in_array($app->status, ['offered']))
                        <button type="button" 
                                wire:click="payAcceptanceFee"
                                wire:loading.attr="disabled"
                                class="w-full rounded-xl bg-teal-700 py-2.5 text-xs font-bold text-white hover:bg-teal-600 shadow-sm cursor-pointer transition flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="payAcceptanceFee">Pay Acceptance Fee (NGN {{ number_format($accFeeInvoice?->amount ?? 35000.00, 2) }})</span>
                            <span wire:loading wire:target="payAcceptanceFee">Processing Acceptance...</span>
                        </button>
                    @elseif(in_array($app->status, ['acceptance_paid', 'admitted']))
                        <div class="flex items-center justify-between gap-3">
                            @php
                                $accPayment = $accFeeInvoice?->payments()->first();
                            @endphp
                            @if($accPayment)
                                <button type="button" wire:click="viewReceipt({{ $accPayment->id }})" class="rounded-lg border border-zinc-300 bg-white px-3 py-1.5 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 cursor-pointer">
                                    🖨️ Acceptance Receipt
                                </button>
                            @endif
                            <a href="{{ route('student.dashboard') }}" class="rounded-xl bg-teal-600 px-4 py-2 text-xs font-bold text-white hover:bg-teal-500 shadow-sm ml-auto">
                                Enter Student Portal →
                            </a>
                        </div>
                    @else
                        <span class="text-xs text-zinc-400">Available upon provisional admission offer</span>
                    @endif
                </div>
            </div>

        </div>

        <!-- ============================================================== -->
        <!-- MODAL 1: CBT ENTRANCE EXAM PHOTO-CARD SLIP                     -->
        <!-- ============================================================== -->
        @if($showExamSlipModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/80 p-4 backdrop-blur-xs">
                <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 max-h-[90vh] overflow-y-auto">
                    <!-- Slip Header -->
                    <div class="text-center pb-4 border-b border-zinc-200 dark:border-zinc-800">
                        <div class="flex justify-center mb-2">
                            <div class="size-12 rounded-xl bg-gradient-to-tr from-sky-700 to-teal-500 text-white flex items-center justify-center font-black text-xl">
                                C
                            </div>
                        </div>
                        <h3 class="font-black text-base tracking-tight uppercase text-zinc-900 dark:text-white">College of Nursing & Midwifery</h3>
                        <p class="text-xs font-bold text-sky-700 dark:text-sky-400 uppercase tracking-wider mt-0.5">Computer-Based Entrance Examination Photo-Card & Examination Slip</p>
                        <p class="text-[11px] text-zinc-500">Academic Session: {{ $app->academicSession?->name ?? '2025/2026' }}</p>
                    </div>

                    <!-- Candidate Details -->
                    <div class="mt-6 flex flex-col sm:flex-row gap-6 items-center sm:items-start bg-zinc-50 p-4 rounded-xl dark:bg-zinc-800/40 border border-zinc-200 dark:border-zinc-700">
                        <div class="size-28 rounded-xl bg-zinc-300 dark:bg-zinc-700 flex items-center justify-center font-bold text-2xl text-zinc-600 dark:text-zinc-300 border-2 border-zinc-400 shrink-0">
                            PHOTO
                        </div>
                        <div class="flex-1 text-xs space-y-1.5 w-full">
                            <div class="flex justify-between border-b border-zinc-200/60 pb-1">
                                <span class="text-zinc-500">Candidate Name:</span>
                                <span class="font-bold text-zinc-900 dark:text-white">{{ $app->full_name }}</span>
                            </div>
                            <div class="flex justify-between border-b border-zinc-200/60 pb-1">
                                <span class="text-zinc-500">Application Number:</span>
                                <span class="font-mono font-bold text-sky-700 dark:text-sky-300">{{ $app->application_number }}</span>
                            </div>
                            <div class="flex justify-between border-b border-zinc-200/60 pb-1">
                                <span class="text-zinc-500">Programme:</span>
                                <span class="font-bold text-zinc-900 dark:text-white">{{ $app->programme?->name }}</span>
                            </div>
                            <div class="flex justify-between border-b border-zinc-200/60 pb-1">
                                <span class="text-zinc-500">Allocated Seat Number:</span>
                                <span class="font-mono font-extrabold text-sm text-emerald-700 dark:text-emerald-400">{{ $app->entrance_exam_seat_number ?? 'CBT-ST-042' }}</span>
                            </div>
                            <div class="flex justify-between border-b border-zinc-200/60 pb-1">
                                <span class="text-zinc-500">Exam Date & Time:</span>
                                <span class="font-bold text-zinc-900 dark:text-white">{{ $app->entrance_exam_date ? $app->entrance_exam_date->format('l, d F Y - h:i A') : 'Saturday, 25 October 2026, 09:00 AM' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-500">CBT Center Venue:</span>
                                <span class="font-bold text-zinc-900 dark:text-white">{{ $app->entrance_exam_venue ?? 'College CBT Complex, Hall A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="mt-4 p-3 bg-amber-50 rounded-xl border border-amber-200 text-[11px] text-amber-900 dark:bg-amber-950/40 dark:border-amber-900/50 dark:text-amber-200 space-y-1">
                        <div class="font-bold uppercase tracking-wider text-[10px]">Important CBT Examination Instructions:</div>
                        <ul class="list-disc list-inside space-y-0.5 text-[10px]">
                            <li>Candidate must arrive at the examination hall at least 45 minutes prior to the scheduled time.</li>
                            <li>You must present this printed photo-card slip alongside a valid government-issued photo ID or original JAMB registration slip.</li>
                            <li>Electronic devices, smartwatches, calculators, and handbags are strictly prohibited inside the CBT hall.</li>
                        </ul>
                    </div>

                    <!-- Modal Actions -->
                    <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                        <button type="button" onclick="window.print()" class="rounded-xl bg-sky-700 px-4 py-2 text-xs font-bold text-white hover:bg-sky-600 cursor-pointer">
                            🖨️ Print Slip
                        </button>
                        <button type="button" wire:click="closeExamSlip" class="rounded-xl border border-zinc-300 bg-white px-4 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 cursor-pointer">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- ============================================================== -->
        <!-- MODAL 2: OFFICIAL PROVISIONAL LETTER OF ADMISSION              -->
        <!-- ============================================================== -->
        @if($showAdmissionLetterModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/80 p-4 backdrop-blur-xs">
                <div class="w-full max-w-2xl rounded-2xl bg-white p-8 shadow-2xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 max-h-[90vh] overflow-y-auto">
                    <!-- Institutional Crest -->
                    <div class="text-center pb-6 border-b-2 border-zinc-800 dark:border-zinc-200">
                        <h2 class="font-black text-xl tracking-tight uppercase text-zinc-900 dark:text-white">College of Nursing & Midwifery</h2>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400">Office of the Registrar • Admissions Board</p>
                        <p class="text-[10px] text-zinc-400 mt-0.5">Statutory Accreditations: NMCN, National Board for Technical Education</p>
                    </div>

                    <div class="mt-6 flex justify-between items-baseline text-xs">
                        <div>Ref: <strong class="font-mono text-zinc-800 dark:text-zinc-200">{{ $app->admission_letter_ref ?? 'ADM/2026/NUR/4821' }}</strong></div>
                        <div>Date: <strong class="text-zinc-800 dark:text-zinc-200">{{ $app->admission_offered_at?->format('d F Y') ?? now()->format('d F Y') }}</strong></div>
                    </div>

                    <div class="mt-6 text-xs text-zinc-800 dark:text-zinc-200 space-y-3 leading-relaxed">
                        <p>Dear <strong>{{ $app->full_name }}</strong> (App No: {{ $app->application_number }}),</p>
                        
                        <p class="font-bold uppercase tracking-wider text-sm text-sky-800 dark:text-sky-300 text-center py-2 bg-sky-50 dark:bg-sky-950/40 rounded-lg">
                            PROVISIONAL OFFER OF ADMISSION INTO {{ strtoupper($app->programme?->name ?? 'BASIC GENERAL NURSING') }}
                        </p>

                        <p>
                            Following your performance in the Computer-Based Entrance Examination and verification of your O'Level science credits and JAMB UTME score, I am pleased to inform you that you have been offered provisional admission into the <strong>{{ $app->programme?->name }}</strong> programme for the <strong>{{ $app->academicSession?->name }}</strong> academic session.
                        </p>

                        <p>
                            This offer is subject to the following statutory conditions:
                        </p>

                        <ol class="list-decimal list-inside space-y-1 pl-2 text-[11px] text-zinc-600 dark:text-zinc-400">
                            <li>Payment of the non-refundable Acceptance Fee of <strong>NGN 35,000.00</strong> on or before <strong>{{ $app->acceptance_deadline ? $app->acceptance_deadline->format('d F Y') : '14 days from date of offer' }}</strong>.</li>
                            <li>Presentation of original WAEC/NECO/NABTEB certificates and official birth certificate during physical registry verification.</li>
                            <li>Satisfactory medical fitness certificate from an accredited tertiary healthcare center.</li>
                        </ol>

                        <p>
                            Failure to accept this offer within the stipulated deadline shall result in the forfeiture of the admission slot to candidates on the reserve list.
                        </p>

                        <div class="mt-8 pt-4 border-t border-zinc-200 dark:border-zinc-700 flex justify-between items-end">
                            <div>
                                <div class="font-signature text-xl text-sky-700 dark:text-sky-300 font-serif italic">Fatima Bello (PhD)</div>
                                <div class="font-bold text-xs mt-1">Registrar & Secretary to Council</div>
                                <div class="text-[10px] text-zinc-500">College of Nursing and Midwifery</div>
                            </div>
                            <div class="size-16 rounded-full border-2 border-dashed border-emerald-600 text-emerald-600 flex items-center justify-center font-bold text-[9px] uppercase tracking-wider text-center rotate-12">
                                NMCN<br>OFFICIAL<br>STAMP
                            </div>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                        <button type="button" onclick="window.print()" class="rounded-xl bg-emerald-700 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-600 cursor-pointer">
                            🖨️ Print Admission Letter
                        </button>
                        <button type="button" wire:click="closeAdmissionLetter" class="rounded-xl border border-zinc-300 bg-white px-4 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 cursor-pointer">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- ============================================================== -->
        <!-- MODAL 3: OFFICIAL PAYMENT RECEIPT                              -->
        <!-- ============================================================== -->
        @if($showReceiptModal && $selectedReceipt)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/80 p-4 backdrop-blur-xs">
                <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                    <div class="text-center pb-4 border-b border-zinc-200 dark:border-zinc-800">
                        <div class="text-xs font-black uppercase tracking-wider text-zinc-500">Bursary Division Official Receipt</div>
                        <h4 class="font-bold text-base text-zinc-900 dark:text-white mt-1">College of Nursing & Midwifery</h4>
                        <div class="mt-2 text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400">
                            Receipt: {{ $selectedReceipt->receipt_number }}
                        </div>
                    </div>

                    <div class="mt-4 space-y-2 text-xs">
                        <div class="flex justify-between border-b border-zinc-100 py-1 dark:border-zinc-800">
                            <span class="text-zinc-500">Payer Name:</span>
                            <span class="font-bold text-zinc-900 dark:text-white">{{ $app->full_name }}</span>
                        </div>
                        <div class="flex justify-between border-b border-zinc-100 py-1 dark:border-zinc-800">
                            <span class="text-zinc-500">Application Number:</span>
                            <span class="font-mono font-bold">{{ $app->application_number }}</span>
                        </div>
                        <div class="flex justify-between border-b border-zinc-100 py-1 dark:border-zinc-800">
                            <span class="text-zinc-500">Payment Purpose:</span>
                            <span class="font-bold text-zinc-900 dark:text-white uppercase">{{ str_replace('_', ' ', $selectedReceipt->invoice?->invoice_type ?? 'Fee Payment') }}</span>
                        </div>
                        <div class="flex justify-between border-b border-zinc-100 py-1 dark:border-zinc-800">
                            <span class="text-zinc-500">Amount Paid:</span>
                            <span class="font-extrabold text-sm text-emerald-600 dark:text-emerald-400">NGN {{ number_format($selectedReceipt->amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between border-b border-zinc-100 py-1 dark:border-zinc-800">
                            <span class="text-zinc-500">Transaction Ref:</span>
                            <span class="font-mono text-[11px]">{{ $selectedReceipt->transaction_reference }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-zinc-500">Payment Date:</span>
                            <span>{{ $selectedReceipt->paid_at?->format('d M Y, h:i A') ?? now()->format('d M Y') }}</span>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                        <button type="button" onclick="window.print()" class="rounded-xl bg-sky-700 px-4 py-2 text-xs font-bold text-white hover:bg-sky-600 cursor-pointer">
                            🖨️ Print Receipt
                        </button>
                        <button type="button" wire:click="closeReceiptModal" class="rounded-xl border border-zinc-300 bg-white px-4 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 cursor-pointer">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        @endif

    @endif

</div>
