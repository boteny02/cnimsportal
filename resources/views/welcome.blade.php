<x-layouts.institutional :title="'CNIMS - College of Nursing Information Management System'">
    <!-- Hero Section -->
    <section class="relative overflow-hidden py-12 sm:py-20 border-b border-zinc-200 dark:border-zinc-800 bg-gradient-to-b from-sky-50/50 via-white to-zinc-50 dark:from-sky-950/20 dark:via-zinc-950 dark:to-zinc-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            
            <span class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-3.5 py-1 text-xs font-semibold text-sky-800 shadow-2xs dark:border-sky-800 dark:bg-sky-950/60 dark:text-sky-300">
                <span class="size-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Accredited by the Nursing & Midwifery Council of Nigeria (NMCN)
            </span>

            <h1 class="mt-6 text-3xl font-black tracking-tight text-zinc-900 sm:text-5xl dark:text-white">
                College of Nursing Information <br class="hidden sm:inline">Management System
            </h1>
            
            <p class="mx-auto mt-6 max-w-2xl text-sm sm:text-base text-zinc-600 dark:text-zinc-400 leading-relaxed">
                A purpose-built institutional platform powering student admissions, automated curriculum management, teaching hospital rotation allocations, digital preceptor logbooks, and OSCE clinical assessments.
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('public.apply') }}" class="rounded-xl bg-sky-700 px-6 py-3 text-xs sm:text-sm font-bold text-white shadow-md hover:bg-sky-600 transition">
                    Start 2025/2026 Nursing Application
                </a>
                <a href="{{ route('admin.dashboard') }}" class="rounded-xl border border-zinc-300 bg-white px-5 py-3 text-xs sm:text-sm font-bold text-zinc-800 shadow-xs hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white dark:hover:bg-zinc-700 transition">
                    Access Staff Portal
                </a>
                <a href="{{ route('student.dashboard') }}" class="rounded-xl border border-teal-600 bg-teal-50 px-5 py-3 text-xs sm:text-sm font-bold text-teal-800 shadow-xs hover:bg-teal-100 dark:bg-teal-950/40 dark:text-teal-300 dark:border-teal-800 transition">
                    Access Student Portal
                </a>
            </div>

            <!-- Live Metrics Strip -->
            <div class="mt-12 sm:mt-16 grid grid-cols-2 gap-4 sm:grid-cols-4 max-w-4xl mx-auto text-center">
                <div class="rounded-xl border border-zinc-200 bg-white/80 p-4 backdrop-blur-2xs dark:border-zinc-800 dark:bg-zinc-900/60">
                    <div class="text-2xl font-black text-sky-700 dark:text-sky-400">100%</div>
                    <div class="text-xs text-zinc-500 font-medium mt-0.5">Council Pass Rate</div>
                </div>
                <div class="rounded-xl border border-zinc-200 bg-white/80 p-4 backdrop-blur-2xs dark:border-zinc-800 dark:bg-zinc-900/60">
                    <div class="text-2xl font-black text-teal-700 dark:text-teal-400">3 Hospitals</div>
                    <div class="text-xs text-zinc-500 font-medium mt-0.5">Teaching Centers</div>
                </div>
                <div class="rounded-xl border border-zinc-200 bg-white/80 p-4 backdrop-blur-2xs dark:border-zinc-800 dark:bg-zinc-900/60">
                    <div class="text-2xl font-black text-purple-700 dark:text-purple-400">Digital</div>
                    <div class="text-xs text-zinc-500 font-medium mt-0.5">Procedural Logbook</div>
                </div>
                <div class="rounded-xl border border-zinc-200 bg-white/80 p-4 backdrop-blur-2xs dark:border-zinc-800 dark:bg-zinc-900/60">
                    <div class="text-2xl font-black text-rose-700 dark:text-rose-400">OSCE Ready</div>
                    <div class="text-xs text-zinc-500 font-medium mt-0.5">Objective Scoring</div>
                </div>
            </div>

        </div>
    </section>

    <!-- Core Institutional Features Section -->
    <section class="py-12 sm:py-16 bg-white dark:bg-zinc-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <h2 class="text-xs font-bold text-sky-700 uppercase tracking-widest dark:text-sky-400">Modular Architecture</h2>
                <p class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-zinc-900 dark:text-white">
                    Comprehensive Institutional Nursing Ecosystem
                </p>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                
                <div class="rounded-2xl border border-zinc-200 p-6 bg-zinc-50/50 dark:border-zinc-800 dark:bg-zinc-800/40">
                    <div class="size-10 rounded-xl bg-sky-600 text-white flex items-center justify-center font-bold mb-4">
                        1
                    </div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-white">Admissions & Screening</h3>
                    <p class="mt-2 text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        Multi-step applicant wizard, O'Level subject credit verification, computer-based entrance scoring, and 1-click student matriculation.
                    </p>
                </div>

                <div class="rounded-2xl border border-zinc-200 p-6 bg-zinc-50/50 dark:border-zinc-800 dark:bg-zinc-800/40">
                    <div class="size-10 rounded-xl bg-teal-600 text-white flex items-center justify-center font-bold mb-4">
                        2
                    </div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-white">Hospital Clinical Rotations</h3>
                    <p class="mt-2 text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        Teaching hospital ward placements, capacity limits, preceptor allocation, and attendance tracking for surgical, medical, and labour wards.
                    </p>
                </div>

                <div class="rounded-2xl border border-zinc-200 p-6 bg-zinc-50/50 dark:border-zinc-800 dark:bg-zinc-800/40">
                    <div class="size-10 rounded-xl bg-purple-600 text-white flex items-center justify-center font-bold mb-4">
                        3
                    </div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-white">Digital Clinical Logbook</h3>
                    <p class="mt-2 text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        Paperless procedural logging, reflective nursing notes, preceptor sign-off workflow, and 5-tier competency evolution engine.
                    </p>
                </div>

                <div class="rounded-2xl border border-zinc-200 p-6 bg-zinc-50/50 dark:border-zinc-800 dark:bg-zinc-800/40">
                    <div class="size-10 rounded-xl bg-rose-600 text-white flex items-center justify-center font-bold mb-4">
                        4
                    </div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-white">OSCE & Academic Results</h3>
                    <p class="mt-2 text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        Station rubrics scoring on tablets, lecturer score entry sheets, departmental verification, board publishing, and transcript generation.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- Accredited Programmes Section -->
    <section class="py-12 sm:py-16 bg-zinc-50 dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-white">Accredited Nursing Programmes</h2>
                    <p class="text-xs text-zinc-500 mt-1 dark:text-zinc-400">Approved professional programmes leading to statutory licensure by the Nursing and Midwifery Council.</p>
                </div>
                <a href="{{ route('public.apply') }}" class="rounded-lg bg-sky-700 px-4 py-2 text-xs font-semibold text-white hover:bg-sky-600 self-start sm:self-auto">
                    Apply for 2025/2026 Session
                </a>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
                    <span class="rounded bg-sky-100 px-2.5 py-1 text-xs font-bold text-sky-800 dark:bg-sky-950 dark:text-sky-300">RN Certification</span>
                    <h3 class="mt-3 text-lg font-bold text-zinc-900 dark:text-white">Basic General Nursing</h3>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Duration: 3 Years Full-Time</p>
                    <p class="mt-3 text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed">
                        Comprehensive foundational and clinical nursing training encompassing medical-surgical, pharmacology, anatomy, and critical patient care.
                    </p>
                    <div class="mt-5 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-center text-xs">
                        <span class="text-zinc-500">Entry: 5 O'Level Credits</span>
                        <a href="{{ route('public.apply') }}" class="font-bold text-sky-600 hover:text-sky-500">Apply →</a>
                    </div>
                </div>

                <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
                    <span class="rounded bg-teal-100 px-2.5 py-1 text-xs font-bold text-teal-800 dark:bg-teal-950 dark:text-teal-300">RM Certification</span>
                    <h3 class="mt-3 text-lg font-bold text-zinc-900 dark:text-white">Post-Basic Midwifery</h3>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Duration: 1.5 - 2 Years Full-Time</p>
                    <p class="mt-3 text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed">
                        Specialized training in maternal, ante-natal, labour suite delivery, neonatal resuscitation, and postpartum care.
                    </p>
                    <div class="mt-5 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-center text-xs">
                        <span class="text-zinc-500">Entry: Registered Nurse (RN)</span>
                        <a href="{{ route('public.apply') }}" class="font-bold text-teal-600 hover:text-teal-500">Apply →</a>
                    </div>
                </div>

                <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
                    <span class="rounded bg-purple-100 px-2.5 py-1 text-xs font-bold text-purple-800 dark:bg-purple-950 dark:text-purple-300">RPHN Certification</span>
                    <h3 class="mt-3 text-lg font-bold text-zinc-900 dark:text-white">Public Health Nursing</h3>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Duration: 2 Years Full-Time</p>
                    <p class="mt-3 text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed">
                        Epidemiology, community healthcare, immunisation management, primary healthcare facility coordination, and family health.
                    </p>
                    <div class="mt-5 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-center text-xs">
                        <span class="text-zinc-500">Entry: RN or 5 Credits</span>
                        <a href="{{ route('public.apply') }}" class="font-bold text-purple-600 hover:text-purple-500">Apply →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.institutional>
