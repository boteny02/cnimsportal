<div class="py-6 sm:py-10">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        
        <!-- Header & Breadcrumbs -->
        <div class="mb-8 text-center">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-800 dark:bg-sky-950/60 dark:text-sky-300">
                Official Admissions Portal • 2025/2026 Academic Session
            </span>
            <h1 class="mt-3 text-2xl font-black tracking-tight text-zinc-900 sm:text-4xl dark:text-white">
                Nursing & Midwifery Admissions Application
            </h1>
            <p class="mt-2 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">
                Complete this electronic application to register for the official college screening examination.
            </p>
        </div>

        @if($step < 6)
            <!-- Multi-step Indicator -->
            <div class="mb-8">
                <div class="grid grid-cols-5 gap-2 text-center text-xs">
                    <div class="rounded-xl p-2 border {{ $step >= 1 ? 'border-sky-600 bg-sky-50 text-sky-800 font-bold dark:bg-sky-950/50 dark:border-sky-700 dark:text-sky-300' : 'border-zinc-200 text-zinc-400 dark:border-zinc-800' }}">
                        <span class="block text-[10px] uppercase">Step 1</span>
                        <span class="truncate hidden sm:inline">Bio-Data</span>
                    </div>
                    <div class="rounded-xl p-2 border {{ $step >= 2 ? 'border-sky-600 bg-sky-50 text-sky-800 font-bold dark:bg-sky-950/50 dark:border-sky-700 dark:text-sky-300' : 'border-zinc-200 text-zinc-400 dark:border-zinc-800' }}">
                        <span class="block text-[10px] uppercase">Step 2</span>
                        <span class="truncate hidden sm:inline">Programme</span>
                    </div>
                    <div class="rounded-xl p-2 border {{ $step >= 3 ? 'border-sky-600 bg-sky-50 text-sky-800 font-bold dark:bg-sky-950/50 dark:border-sky-700 dark:text-sky-300' : 'border-zinc-200 text-zinc-400 dark:border-zinc-800' }}">
                        <span class="block text-[10px] uppercase">Step 3</span>
                        <span class="truncate hidden sm:inline">2-Sitting O'Level</span>
                    </div>
                    <div class="rounded-xl p-2 border {{ $step >= 4 ? 'border-sky-600 bg-sky-50 text-sky-800 font-bold dark:bg-sky-950/50 dark:border-sky-700 dark:text-sky-300' : 'border-zinc-200 text-zinc-400 dark:border-zinc-800' }}">
                        <span class="block text-[10px] uppercase">Step 4</span>
                        <span class="truncate hidden sm:inline">JAMB UTME</span>
                    </div>
                    <div class="rounded-xl p-2 border {{ $step >= 5 ? 'border-sky-600 bg-sky-50 text-sky-800 font-bold dark:bg-sky-950/50 dark:border-sky-700 dark:text-sky-300' : 'border-zinc-200 text-zinc-400 dark:border-zinc-800' }}">
                        <span class="block text-[10px] uppercase">Step 5</span>
                        <span class="truncate hidden sm:inline">Credentials</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Card Container -->
        <div class="rounded-2xl border border-zinc-200/90 bg-white p-6 shadow-sm sm:p-8 dark:border-zinc-800 dark:bg-zinc-900">
            
            <!-- STEP 1: Personal Details -->
            @if($step === 1)
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-zinc-900 dark:text-zinc-100">Personal & Demographic Information</h2>
                    <p class="text-xs text-zinc-500 mt-1 dark:text-zinc-400">Please provide your legal names matching your O'Level certificates and birth records.</p>

                    <div class="mt-6 grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-3">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">First Name *</label>
                            <input type="text" wire:model.defer="first_name" placeholder="e.g. Blessing" class="w-full rounded-xl border border-zinc-300 bg-zinc-50/50 px-3 py-2 text-xs text-zinc-900 focus:bg-white dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            @error('first_name') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Middle Name</label>
                            <input type="text" wire:model.defer="middle_name" placeholder="e.g. Ngozi" class="w-full rounded-xl border border-zinc-300 bg-zinc-50/50 px-3 py-2 text-xs text-zinc-900 focus:bg-white dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Last Name (Surname) *</label>
                            <input type="text" wire:model.defer="last_name" placeholder="e.g. Danjuma" class="w-full rounded-xl border border-zinc-300 bg-zinc-50/50 px-3 py-2 text-xs text-zinc-900 focus:bg-white dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            @error('last_name') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Email Address *</label>
                            <input type="email" wire:model.defer="email" placeholder="blessing@example.com" class="w-full rounded-xl border border-zinc-300 bg-zinc-50/50 px-3 py-2 text-xs text-zinc-900 focus:bg-white dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            @error('email') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Phone Number *</label>
                            <input type="text" wire:model.defer="phone" placeholder="08031234567" class="w-full rounded-xl border border-zinc-300 bg-zinc-50/50 px-3 py-2 text-xs text-zinc-900 focus:bg-white dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            @error('phone') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Gender *</label>
                            <select wire:model.defer="gender" class="w-full rounded-xl border border-zinc-300 bg-zinc-50/50 px-3 py-2 text-xs text-zinc-900 focus:bg-white dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                                <option value="female">Female</option>
                                <option value="male">Male</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Date of Birth *</label>
                            <input type="date" wire:model.defer="date_of_birth" class="w-full rounded-xl border border-zinc-300 bg-zinc-50/50 px-3 py-2 text-xs text-zinc-900 focus:bg-white dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            @error('date_of_birth') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">State of Origin *</label>
                            <input type="text" wire:model.defer="state_of_origin" placeholder="e.g. Kaduna" class="w-full rounded-xl border border-zinc-300 bg-zinc-50/50 px-3 py-2 text-xs text-zinc-900 focus:bg-white dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            @error('state_of_origin') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">LGA of Origin *</label>
                            <input type="text" wire:model.defer="lga" placeholder="e.g. Zaria" class="w-full rounded-xl border border-zinc-300 bg-zinc-50/50 px-3 py-2 text-xs text-zinc-900 focus:bg-white dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            @error('lga') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Permanent Residential Address *</label>
                            <input type="text" wire:model.defer="address" placeholder="e.g. No. 14 Hospital Road, Barnawa" class="w-full rounded-xl border border-zinc-300 bg-zinc-50/50 px-3 py-2 text-xs text-zinc-900 focus:bg-white dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            @error('address') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            @endif

            <!-- STEP 2: Programme Selection -->
            @if($step === 2)
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-zinc-900 dark:text-zinc-100">Programme & Session Selection</h2>
                    <p class="text-xs text-zinc-500 mt-1 dark:text-zinc-400">Choose your desired professional nursing discipline accredited by the NMCN.</p>

                    <div class="mt-6 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Academic Session *</label>
                            <select wire:model.defer="academic_session_id" class="w-full rounded-xl border border-zinc-300 bg-zinc-50/50 px-3 py-2.5 text-xs text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                                <option value="">Select Academic Session...</option>
                                @foreach($sessions as $sess)
                                    <option value="{{ $sess->id }}">{{ $sess->name }} ({{ $sess->is_current ? 'Current Active' : 'Upcoming' }})</option>
                                @endforeach
                            </select>
                            @error('academic_session_id') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-2">Available Accredited Programmes *</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($programmes as $prog)
                                    <label class="relative flex cursor-pointer rounded-2xl border p-4 transition focus:outline-hidden {{ $programme_id == $prog->id ? 'border-sky-600 bg-sky-50/50 ring-2 ring-sky-600 dark:border-sky-500 dark:bg-sky-950/40' : 'border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700' }}">
                                        <input type="radio" wire:model="programme_id" value="{{ $prog->id }}" class="sr-only">
                                        <div class="flex flex-col justify-between w-full">
                                            <div>
                                                <span class="rounded bg-sky-100 px-2 py-0.5 text-[10px] font-bold text-sky-800 dark:bg-sky-950 dark:text-sky-300">{{ $prog->code }}</span>
                                                <h3 class="mt-2 text-sm font-bold text-zinc-900 dark:text-white">{{ $prog->name }}</h3>
                                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Duration: {{ $prog->duration_years }} Years • {{ $prog->award_title }}</p>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('programme_id') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            @endif

            <!-- STEP 3: 2-Sitting O'Level Subject Credit System -->
            @if($step === 3)
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-zinc-900 dark:text-zinc-100">2-Sitting O'Level Subject Credit System</h2>
                            <p class="text-xs text-zinc-500 mt-0.5 dark:text-zinc-400">Supports WAEC, NECO, or NABTEB across 1 or 2 sittings. English, Maths, Biology, Chemistry & Physics are strictly mandatory.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">Number of Sittings:</label>
                            <select wire:model.live="o_level_sittings" class="rounded-lg border border-zinc-300 px-2.5 py-1 text-xs dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
                                <option value="1">1 Sitting</option>
                                <option value="2">2 Sittings (Combined)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Graduation Year *</label>
                            <input type="text" wire:model.defer="graduation_year" placeholder="2024" class="w-full rounded-xl border border-zinc-300 bg-zinc-50/50 px-3 py-2 text-xs text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            @error('graduation_year') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Sitting 1 Card -->
                    <div class="mt-6 rounded-2xl border border-sky-200 bg-sky-50/30 p-4 dark:border-sky-900/60 dark:bg-sky-950/20">
                        <div class="font-bold text-xs uppercase tracking-wider text-sky-800 dark:text-sky-300 mb-3 flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <span>First Sitting Examination Details</span>
                                <span class="rounded bg-sky-100 px-2 py-0.5 text-[10px] text-sky-800 dark:bg-sky-900 dark:text-sky-200">Sitting 1</span>
                            </span>
                        </div>

                        <!-- Secondary School for Sitting 1 -->
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Secondary School Attended (Sitting 1) *</label>
                            <input type="text" wire:model.defer="secondary_school_sitting_1" placeholder="e.g. Queen Amina College, Kaduna" class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-xs text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            @error('secondary_school_sitting_1') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                            <div>
                                <label class="block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400 mb-1">Exam Body *</label>
                                <select wire:model.defer="sitting_1_exam_body" class="w-full rounded-lg border border-zinc-300 px-2 py-1.5 text-xs dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
                                    <option value="WAEC">WAEC (WASSCE)</option>
                                    <option value="NECO">NECO (SSCE)</option>
                                    <option value="NABTEB">NABTEB</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400 mb-1">Exam Year *</label>
                                <input type="text" wire:model.defer="sitting_1_exam_year" class="w-full rounded-lg border border-zinc-300 px-2 py-1.5 text-xs dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400 mb-1">Exam / Center Number *</label>
                                <input type="text" wire:model.defer="sitting_1_exam_number" placeholder="4120984128" class="w-full rounded-lg border border-zinc-300 px-2 py-1.5 text-xs dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
                            </div>
                        </div>

                        <!-- 5 Mandatory Core Subjects -->
                        <div class="mb-3">
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-[11px] font-bold text-zinc-800 dark:text-zinc-200">5 Mandatory Core Science Subjects:</label>
                                <span class="text-[10px] text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full dark:bg-emerald-950 dark:text-emerald-300 font-semibold">Strictly Mandatory</span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                @foreach(array_slice($sitting_1_subjects, 0, 5) as $index => $item)
                                    <div class="flex items-center gap-2 rounded-lg bg-white p-2 border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
                                        <div class="truncate flex-1">
                                            <span class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 block">{{ $item['subject'] }}</span>
                                            <span class="text-[9px] text-zinc-400">Mandatory</span>
                                        </div>
                                        <select wire:model="sitting_1_subjects.{{ $index }}.grade" class="rounded border border-zinc-300 px-1.5 py-0.5 text-xs font-bold dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                            <option value="A1">A1</option>
                                            <option value="B2">B2</option>
                                            <option value="B3">B3</option>
                                            <option value="C4">C4</option>
                                            <option value="C5">C5</option>
                                            <option value="C6">C6</option>
                                            <option value="D7">D7</option>
                                            <option value="E8">E8</option>
                                            <option value="F9">F9</option>
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- 6th Applicant-Supplied Subject -->
                        <div class="mt-4 p-3 rounded-xl bg-white/90 border border-sky-300 dark:bg-zinc-900/90 dark:border-sky-800">
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-[11px] font-bold text-sky-900 dark:text-sky-300">6th Subject (Applicant Supplied & Graded) *</label>
                                <span class="text-[10px] text-sky-700 bg-sky-100 px-2 py-0.5 rounded-full dark:bg-sky-950 dark:text-sky-300 font-semibold">Applicant Chosen</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <div class="sm:col-span-2">
                                    <input type="text" wire:model.defer="sitting_1_custom_subject" placeholder="e.g. Civic Education, Agricultural Science, Economics, Further Maths, Geography" class="w-full rounded-lg border border-zinc-300 px-3 py-1.5 text-xs text-zinc-900 dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
                                    @error('sitting_1_custom_subject') <span class="text-[10px] text-rose-600 mt-0.5 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <select wire:model.defer="sitting_1_custom_grade" class="w-full rounded-lg border border-zinc-300 px-2 py-1.5 text-xs font-bold dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
                                        <option value="A1">A1 (Distinction)</option>
                                        <option value="B2">B2 (Very Good)</option>
                                        <option value="B3">B3 (Good)</option>
                                        <option value="C4">C4 (Credit)</option>
                                        <option value="C5">C5 (Credit)</option>
                                        <option value="C6">C6 (Credit)</option>
                                        <option value="D7">D7 (Pass)</option>
                                        <option value="E8">E8 (Pass)</option>
                                        <option value="F9">F9 (Fail)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sitting 2 Card (Conditional) -->
                    @if($o_level_sittings === 2)
                        <div class="mt-6 rounded-2xl border border-teal-200 bg-teal-50/30 p-4 dark:border-teal-900/60 dark:bg-teal-950/20">
                            <div class="font-bold text-xs uppercase tracking-wider text-teal-800 dark:text-teal-300 mb-3 flex items-center justify-between">
                                <span class="flex items-center gap-2">
                                    <span>Second Sitting Examination Details</span>
                                    <span class="rounded bg-teal-100 px-2 py-0.5 text-[10px] text-teal-800 dark:bg-teal-900 dark:text-teal-200">Sitting 2</span>
                                </span>
                                <span class="text-[11px] font-normal text-zinc-500 dark:text-zinc-400">Can be a different secondary school</span>
                            </div>

                            <!-- Secondary School for Sitting 2 (Different School Allowed) -->
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Secondary School Attended (Sitting 2 - Can be Different School) *</label>
                                <input type="text" wire:model.defer="secondary_school_sitting_2" placeholder="e.g. Government Girls Secondary School, Zaria / External Exam Center" class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-xs text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                                @error('secondary_school_sitting_2') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                                <div>
                                    <label class="block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400 mb-1">Exam Body *</label>
                                    <select wire:model.defer="sitting_2_exam_body" class="w-full rounded-lg border border-zinc-300 px-2 py-1.5 text-xs dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
                                        <option value="NECO">NECO (SSCE)</option>
                                        <option value="WAEC">WAEC (WASSCE)</option>
                                        <option value="NABTEB">NABTEB</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400 mb-1">Exam Year *</label>
                                    <input type="text" wire:model.defer="sitting_2_exam_year" class="w-full rounded-lg border border-zinc-300 px-2 py-1.5 text-xs dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-zinc-600 dark:text-zinc-400 mb-1">Exam / Center Number *</label>
                                    <input type="text" wire:model.defer="sitting_2_exam_number" placeholder="50982314AB" class="w-full rounded-lg border border-zinc-300 px-2 py-1.5 text-xs dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
                                </div>
                            </div>

                            <!-- 5 Mandatory Core Subjects for Sitting 2 -->
                            <div class="mb-3">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-[11px] font-bold text-zinc-800 dark:text-zinc-200">5 Mandatory Core Science Subjects (Sitting 2):</label>
                                    <span class="text-[10px] text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full dark:bg-emerald-950 dark:text-emerald-300 font-semibold">Strictly Mandatory</span>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    @foreach(array_slice($sitting_2_subjects, 0, 5) as $index => $item)
                                        <div class="flex items-center gap-2 rounded-lg bg-white p-2 border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
                                            <div class="truncate flex-1">
                                                <span class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 block">{{ $item['subject'] }}</span>
                                                <span class="text-[9px] text-zinc-400">Mandatory</span>
                                            </div>
                                            <select wire:model="sitting_2_subjects.{{ $index }}.grade" class="rounded border border-zinc-300 px-1.5 py-0.5 text-xs font-bold dark:bg-zinc-700 dark:border-zinc-600 dark:text-white">
                                                <option value="A1">A1</option>
                                                <option value="B2">B2</option>
                                                <option value="B3">B3</option>
                                                <option value="C4">C4</option>
                                                <option value="C5">C5</option>
                                                <option value="C6">C6</option>
                                                <option value="D7">D7</option>
                                                <option value="E8">E8</option>
                                                <option value="F9">F9</option>
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- 6th Applicant-Supplied Subject for Sitting 2 -->
                            <div class="mt-4 p-3 rounded-xl bg-white/90 border border-teal-300 dark:bg-zinc-900/90 dark:border-teal-800">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-[11px] font-bold text-teal-900 dark:text-teal-300">6th Subject (Sitting 2 Applicant Supplied & Graded) *</label>
                                    <span class="text-[10px] text-teal-700 bg-teal-100 px-2 py-0.5 rounded-full dark:bg-teal-950 dark:text-teal-300 font-semibold">Applicant Chosen</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                    <div class="sm:col-span-2">
                                        <input type="text" wire:model.defer="sitting_2_custom_subject" placeholder="e.g. Agricultural Science, Economics, Civic Education, Further Maths" class="w-full rounded-lg border border-zinc-300 px-3 py-1.5 text-xs text-zinc-900 dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
                                        @error('sitting_2_custom_subject') <span class="text-[10px] text-rose-600 mt-0.5 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <select wire:model.defer="sitting_2_custom_grade" class="w-full rounded-lg border border-zinc-300 px-2 py-1.5 text-xs font-bold dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
                                            <option value="A1">A1 (Distinction)</option>
                                            <option value="B2">B2 (Very Good)</option>
                                            <option value="B3">B3 (Good)</option>
                                            <option value="C4">C4 (Credit)</option>
                                            <option value="C5">C5 (Credit)</option>
                                            <option value="C6">C6 (Credit)</option>
                                            <option value="D7">D7 (Pass)</option>
                                            <option value="E8">E8 (Pass)</option>
                                            <option value="F9">F9 (Fail)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-4 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-300 flex items-center gap-2">
                        <span>✓ NMCN Rule: English, Mathematics, Biology, Chemistry & Physics are verified at credit level (C6 or better) across your specified sitting(s). The 6th subject is applicant-selected. Different secondary schools attended across sittings are accepted.</span>
                    </div>
                </div>
            @endif

            <!-- STEP 4: JAMB UTME Score -->
            @if($step === 4)
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-zinc-900 dark:text-zinc-100">JAMB UTME Examination Profile</h2>
                    <p class="text-xs text-zinc-500 mt-1 dark:text-zinc-400">Provide your official JAMB registration details and subject scores.</p>

                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">JAMB Registration Number *</label>
                            <input type="text" wire:model.defer="jamb_reg_number" placeholder="202610482914AB" class="w-full rounded-xl border border-zinc-300 bg-zinc-50/50 px-3 py-2 text-xs text-zinc-900 font-mono dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            @error('jamb_reg_number') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">JAMB Total Aggregate Score (Out of 400) *</label>
                            <input type="number" wire:model.defer="jamb_score" placeholder="215" class="w-full rounded-xl border border-zinc-300 bg-zinc-50/50 px-3 py-2 text-xs text-zinc-900 font-bold dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            @error('jamb_score') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-xs font-bold text-zinc-700 dark:text-zinc-300 mb-2">JAMB Subject Breakdown:</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach($jamb_subjects as $index => $sub)
                                <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-800">
                                    <span class="block text-[11px] font-semibold text-zinc-600 dark:text-zinc-300">{{ $sub['subject'] }}</span>
                                    <input type="number" wire:model="jamb_subjects.{{ $index }}.score" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-2 py-1 text-xs font-bold dark:border-zinc-600 dark:bg-zinc-700 dark:text-white">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-6 rounded-xl bg-sky-50 p-4 border border-sky-200 dark:bg-sky-950/40 dark:border-sky-800 text-xs text-sky-900 dark:text-sky-300">
                        <p class="font-bold">Official College JAMB Cut-off: 180</p>
                        <p class="text-[11px] mt-0.5">Candidates scoring 180 and above are eligible for the Computer-Based Entrance Examination.</p>
                    </div>
                </div>
            @endif

            <!-- STEP 5: Applicant Portal Credentials Password Setup -->
            @if($step === 5)
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-zinc-900 dark:text-zinc-100">Applicant Portal Credentials & Submission</h2>
                    <p class="text-xs text-zinc-500 mt-1 dark:text-zinc-400">Set your secure access password. You will use your generated Application Number and this password to log into your applicant dashboard anytime.</p>

                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Create Access Password *</label>
                            <input type="password" wire:model.defer="password" placeholder="••••••••" class="w-full rounded-xl border border-zinc-300 bg-zinc-50/50 px-3 py-2 text-xs text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                            @error('password') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Confirm Password *</label>
                            <input type="password" wire:model.defer="password_confirmation" placeholder="••••••••" class="w-full rounded-xl border border-zinc-300 bg-zinc-50/50 px-3 py-2 text-xs text-zinc-900 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                        </div>
                    </div>

                    <!-- Summary Review Box -->
                    <div class="mt-6 rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/40 text-xs space-y-2">
                        <div class="font-bold text-zinc-900 dark:text-white uppercase tracking-wider text-[11px]">Application Summary Confirmation:</div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px]">
                            <div>Candidate: <strong>{{ $first_name }} {{ $last_name }}</strong></div>
                            <div>Email: <strong>{{ $email }}</strong></div>
                            <div>O'Level Sittings: <strong>{{ $o_level_sittings }} Sitting(s)</strong></div>
                            <div>JAMB Score: <strong>{{ $jamb_score }} / 400</strong></div>
                        </div>
                        <div class="pt-2 border-t border-zinc-200 dark:border-zinc-700 text-sky-700 dark:text-sky-300 font-semibold text-[11px]">
                            Next Step: Upon submission, your unique Application Number will be issued, and your Step 4 Application Fee Invoice (NGN 15,000.00) will be generated by the Bursary.
                        </div>
                    </div>
                </div>
            @endif

            <!-- STEP 6: Success Confirmation Screen -->
            @if($step === 6)
                <div class="text-center py-6">
                    <div class="mx-auto flex size-16 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 font-black text-2xl shadow-sm dark:bg-emerald-950 dark:text-emerald-300">
                        ✓
                    </div>

                    <h2 class="mt-4 text-2xl font-black tracking-tight text-zinc-900 dark:text-white">
                        Application Successfully Submitted!
                    </h2>

                    <p class="mt-2 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 max-w-md mx-auto">
                        Your electronic application dossier has been verified and registered with the Admissions Office.
                    </p>

                    <!-- Application Number Card -->
                    <div class="mt-6 inline-block rounded-2xl border-2 border-dashed border-sky-400 bg-sky-50 px-6 py-4 dark:bg-sky-950/50 dark:border-sky-600">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-sky-700 dark:text-sky-300">Your Official Application Number:</span>
                        <div class="mt-1 font-mono text-2xl font-extrabold text-sky-900 dark:text-sky-100 tracking-wider">
                            {{ $submittedApplicationNumber }}
                        </div>
                        <span class="text-[10px] text-zinc-500 mt-1 block">Keep this safe. You can log in at /applicant/login anytime.</span>
                    </div>

                    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                        <a href="{{ route('applicant.dashboard') }}" class="rounded-xl bg-sky-700 px-6 py-3 text-xs sm:text-sm font-bold text-white shadow-md hover:bg-sky-600 transition">
                            Enter Applicant Dashboard to Pay Application Fee (Step 4) →
                        </a>
                        <a href="{{ route('home') }}" class="rounded-xl border border-zinc-300 bg-white px-5 py-3 text-xs sm:text-sm font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                            Return to Portal Home
                        </a>
                    </div>
                </div>
            @endif

            <!-- Navigation Buttons (Steps 1 to 5) -->
            @if($step < 6)
                <div class="mt-8 flex items-center justify-between pt-5 border-t border-zinc-100 dark:border-zinc-800">
                    @if($step > 1)
                        <button type="button" wire:click="prevStep" class="rounded-xl border border-zinc-300 bg-white px-4 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 cursor-pointer">
                            ← Previous Step
                        </button>
                    @else
                        <div></div>
                    @endif

                    @if($step < 5)
                        <button type="button" wire:click="nextStep" class="rounded-xl bg-sky-700 px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-sky-600 cursor-pointer">
                            Continue to Next Step →
                        </button>
                    @else
                        <button type="button" wire:click="submitApplication" wire:loading.attr="disabled" class="rounded-xl bg-emerald-600 px-6 py-2.5 text-xs font-bold text-white shadow-md hover:bg-emerald-500 cursor-pointer transition flex items-center gap-2">
                            <span wire:loading.remove wire:target="submitApplication">Submit Application & Generate Invoice →</span>
                            <span wire:loading wire:target="submitApplication">Submitting Dossier...</span>
                        </button>
                    @endif
                </div>
            @endif

        </div>
    </div>
</div>
