<x-layouts.institutional :title="'Accredited Nursing Programmes - CNIMS'">
    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-800 dark:bg-sky-950 dark:text-sky-300">
                    Accredited Curriculum Overview
                </span>
                <h1 class="mt-3 text-3xl font-black tracking-tight text-zinc-900 sm:text-4xl dark:text-white">
                    Official Programmes of Study
                </h1>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    All programmes are fully accredited by the Nursing and Midwifery Council of Nigeria (NMCN) and affiliated with designated tertiary teaching hospitals.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                @foreach($programmes as $prog)
                    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900 flex flex-col justify-between">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <span class="rounded-md bg-sky-100 px-2 py-0.5 text-xs font-bold text-sky-800 dark:bg-sky-950 dark:text-sky-300">
                                    {{ $prog->degree_type }} Licensure
                                </span>
                                <span class="text-xs font-semibold text-zinc-400">{{ $prog->duration_years }} Years Full-Time</span>
                            </div>

                            <h3 class="mt-3 text-xl font-bold text-zinc-900 dark:text-white">{{ $prog->name }}</h3>
                            <p class="mt-2 text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ $prog->description }}</p>

                            <div class="mt-6 border-t border-zinc-100 pt-4 dark:border-zinc-800">
                                <h4 class="text-xs font-bold text-zinc-900 dark:text-white uppercase tracking-wider">Admission Requirements</h4>
                                <ul class="mt-2 space-y-1 text-xs text-zinc-600 dark:text-zinc-400 list-disc list-inside">
                                    <li>At least 5 O'Level credits (WAEC, NECO or NABTEB)</li>
                                    <li>English Language & Mathematics</li>
                                    <li>Biology, Chemistry & Physics</li>
                                    <li>Pass written entrance exam & interview</li>
                                </ul>
                            </div>
                        </div>

                        <div class="border-t border-zinc-200 bg-zinc-50 p-6 dark:border-zinc-800 dark:bg-zinc-800/40">
                            <a href="{{ route('public.apply') }}?prog={{ $prog->id }}" class="block w-full rounded-xl bg-sky-700 py-2.5 text-center text-xs font-bold text-white shadow-xs hover:bg-sky-600 transition">
                                Apply for {{ $prog->degree_type }} Programme
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-layouts.institutional>
