<div class="py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Student Registry & Institutional Profiles</h1>
                <p class="text-xs text-zinc-500 mt-1 dark:text-zinc-400">Search officially matriculated nursing students, inspect multi-year transcripts, clinical evaluations, and fee clearances.</p>
            </div>
            <div>
                <a href="{{ route('public.verify-certificate') }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-300 bg-white px-3 py-2 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Registry Verification Tool
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-4 rounded-xl border border-zinc-200/80 bg-white p-4 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
            <div>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search name, matric number (CON/...), or phone..." 
                    class="block w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs shadow-2xs focus:border-sky-500 focus:ring-sky-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                >
            </div>
            <div>
                <select wire:model.live="programmeFilter" class="block w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs shadow-2xs focus:border-sky-500 focus:ring-sky-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    <option value="">All Programmes</option>
                    @foreach($programmes as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="levelFilter" class="block w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs shadow-2xs focus:border-sky-500 focus:ring-sky-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    <option value="">All Levels</option>
                    @foreach($levels as $l)
                        <option value="{{ $l->id }}">{{ $l->name }} ({{ $l->code }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="statusFilter" class="block w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs shadow-2xs focus:border-sky-500 focus:ring-sky-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    <option value="all">All Enrolment Statuses</option>
                    <option value="active">Active</option>
                    <option value="graduated">Graduated</option>
                    <option value="suspended">Suspended</option>
                    <option value="withdrawn">Withdrawn</option>
                </select>
            </div>
        </div>

        <!-- Students Table -->
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-left text-xs dark:divide-zinc-800">
                    <thead class="bg-zinc-50 font-semibold text-zinc-600 dark:bg-zinc-800/60 dark:text-zinc-300">
                        <tr>
                            <th class="px-4 py-3">Matriculation No.</th>
                            <th class="px-4 py-3">Full Name</th>
                            <th class="px-4 py-3">Programme</th>
                            <th class="px-4 py-3">Current Level</th>
                            <th class="px-4 py-3">Gender / Origin</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($students as $st)
                            <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition">
                                <td class="px-4 py-3 font-mono font-bold text-sky-700 dark:text-sky-300">
                                    {{ $st->student_number }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-zinc-900 dark:text-white">{{ $st->full_name }}</div>
                                    <div class="text-[11px] text-zinc-500">{{ $st->phone }}</div>
                                </td>
                                <td class="px-4 py-3 font-medium text-zinc-700 dark:text-zinc-300">
                                    {{ $st->programme?->name }} ({{ $st->programme?->degree_type }})
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded bg-sky-50 px-2 py-0.5 text-xs font-semibold text-sky-700 dark:bg-sky-950/60 dark:text-sky-300">
                                        {{ $st->currentLevel?->code }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">
                                    {{ ucfirst($st->gender) }} • {{ $st->state_of_origin ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3">
                                    <x-badge :type="$st->status">{{ ucfirst($st->status) }}</x-badge>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button wire:click="viewProfile({{ $st->id }})" class="rounded-md bg-sky-700 px-3 py-1 text-xs font-semibold text-white hover:bg-sky-600 shadow-2xs">
                                        View Dossier
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-xs text-zinc-500">
                                    No students match the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $students->links() }}
            </div>
        </div>

        <!-- Student Dossier Modal / Slide-over -->
        @if($selectedStudent)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-2xs">
                <div class="w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex flex-col max-h-[85vh]">
                    
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b border-zinc-200 px-6 py-4 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50">
                        <div class="flex items-center gap-3">
                            <div class="size-12 rounded-full bg-sky-600 text-white flex items-center justify-center font-bold text-lg">
                                {{ substr($selectedStudent->first_name, 0, 1) }}{{ substr($selectedStudent->last_name, 0, 1) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-bold text-zinc-900 dark:text-white">{{ $selectedStudent->full_name }}</h3>
                                    <x-badge :type="$selectedStudent->status">{{ ucfirst($selectedStudent->status) }}</x-badge>
                                </div>
                                <p class="text-xs text-zinc-500 font-mono">{{ $selectedStudent->student_number }} • {{ $selectedStudent->programme?->name }} ({{ $selectedStudent->currentLevel?->code }})</p>
                            </div>
                        </div>
                        <button wire:click="closeProfile" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 p-1">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Dossier Tabs -->
                    <div class="border-b border-zinc-200 bg-white px-6 dark:border-zinc-800 dark:bg-zinc-900 flex gap-6 text-xs font-semibold">
                        <button wire:click="$set('activeTab', 'personal')" class="py-3 border-b-2 {{ $activeTab === 'personal' ? 'border-sky-600 text-sky-600' : 'border-transparent text-zinc-500 hover:text-zinc-800' }}">
                            Personal & Bio-Data
                        </button>
                        <button wire:click="$set('activeTab', 'academics')" class="py-3 border-b-2 {{ $activeTab === 'academics' ? 'border-sky-600 text-sky-600' : 'border-transparent text-zinc-500 hover:text-zinc-800' }}">
                            Academic Courses & Results ({{ $selectedStudent->results->count() }})
                        </button>
                        <button wire:click="$set('activeTab', 'clinical')" class="py-3 border-b-2 {{ $activeTab === 'clinical' ? 'border-sky-600 text-sky-600' : 'border-transparent text-zinc-500 hover:text-zinc-800' }}">
                            Clinical Postings & Logbook ({{ $selectedStudent->logbooks->count() }})
                        </button>
                        <button wire:click="$set('activeTab', 'finance')" class="py-3 border-b-2 {{ $activeTab === 'finance' ? 'border-sky-600 text-sky-600' : 'border-transparent text-zinc-500 hover:text-zinc-800' }}">
                            Financial Ledger & Invoices
                        </button>
                    </div>

                    <!-- Tab Contents -->
                    <div class="p-6 overflow-y-auto flex-1 text-xs">
                        
                        <!-- TAB 1: Personal -->
                        @if($activeTab === 'personal')
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <h4 class="font-bold text-zinc-900 dark:text-white uppercase tracking-wider text-[11px]">Demographic Profile</h4>
                                    <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 space-y-2 dark:border-zinc-800 dark:bg-zinc-800/50">
                                        <div><span class="text-zinc-400 font-medium">Gender:</span> <strong class="text-zinc-800 dark:text-zinc-200">{{ ucfirst($selectedStudent->gender) }}</strong></div>
                                        <div><span class="text-zinc-400 font-medium">Date of Birth:</span> <span class="text-zinc-800 dark:text-zinc-200">{{ $selectedStudent->date_of_birth?->format('d M Y') ?? 'N/A' }}</span></div>
                                        <div><span class="text-zinc-400 font-medium">State & LGA:</span> <span class="text-zinc-800 dark:text-zinc-200">{{ $selectedStudent->state_of_origin }}, {{ $selectedStudent->lga }}</span></div>
                                        <div><span class="text-zinc-400 font-medium">Residential Address:</span> <span class="text-zinc-800 dark:text-zinc-200">{{ $selectedStudent->address ?? 'N/A' }}</span></div>
                                        <div><span class="text-zinc-400 font-medium">Phone Number:</span> <span class="text-zinc-800 dark:text-zinc-200">{{ $selectedStudent->phone }}</span></div>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <h4 class="font-bold text-zinc-900 dark:text-white uppercase tracking-wider text-[11px]">Medical & Emergency Contact</h4>
                                    <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 space-y-2 dark:border-zinc-800 dark:bg-zinc-800/50">
                                        <div><span class="text-zinc-400 font-medium">Blood Group:</span> <strong class="text-rose-600">{{ $selectedStudent->blood_group ?? 'O+' }}</strong></div>
                                        <div><span class="text-zinc-400 font-medium">Genotype:</span> <strong class="text-zinc-800 dark:text-zinc-200">{{ $selectedStudent->genotype ?? 'AA' }}</strong></div>
                                        <div><span class="text-zinc-400 font-medium">Emergency Next-of-Kin:</span> <span class="text-zinc-800 dark:text-zinc-200">{{ $selectedStudent->emergency_contact_name }} ({{ $selectedStudent->emergency_contact_relationship }})</span></div>
                                        <div><span class="text-zinc-400 font-medium">Emergency Phone:</span> <span class="text-zinc-800 dark:text-zinc-200">{{ $selectedStudent->emergency_contact_phone }}</span></div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- TAB 2: Academics -->
                        @if($activeTab === 'academics')
                            <div class="space-y-4">
                                <h4 class="font-bold text-zinc-900 dark:text-white uppercase tracking-wider text-[11px]">Recorded Examination Results</h4>
                                <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-800">
                                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                                        <thead class="bg-zinc-50 text-left font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                                            <tr>
                                                <th class="px-3 py-2">Course Code & Title</th>
                                                <th class="px-3 py-2">Units</th>
                                                <th class="px-3 py-2">CA (30)</th>
                                                <th class="px-3 py-2">Exam (70)</th>
                                                <th class="px-3 py-2">Total (100)</th>
                                                <th class="px-3 py-2">Grade</th>
                                                <th class="px-3 py-2">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                            @forelse($selectedStudent->results as $res)
                                                <tr>
                                                    <td class="px-3 py-2 font-medium">{{ $res->course?->code }} - {{ $res->course?->title }}</td>
                                                    <td class="px-3 py-2">{{ $res->course?->credit_units }}</td>
                                                    <td class="px-3 py-2">{{ $res->ca_score }}</td>
                                                    <td class="px-3 py-2">{{ $res->exam_score }}</td>
                                                    <td class="px-3 py-2 font-bold">{{ $res->total_score }}</td>
                                                    <td class="px-3 py-2 font-mono font-bold text-sky-600">{{ $res->grade }} ({{ $res->grade_point }})</td>
                                                    <td class="px-3 py-2"><x-badge :type="$res->status">{{ ucwords(str_replace('_', ' ', $res->status)) }}</x-badge></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="7" class="px-3 py-4 text-center text-zinc-400">No examination scores entered yet.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <!-- TAB 3: Clinical -->
                        @if($activeTab === 'clinical')
                            <div class="space-y-4">
                                <h4 class="font-bold text-zinc-900 dark:text-white uppercase tracking-wider text-[11px]">Hospital Rotations</h4>
                                <div class="grid grid-cols-1 gap-3">
                                    @forelse($selectedStudent->postings as $post)
                                        <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-800/50">
                                            <div class="flex items-center justify-between">
                                                <h5 class="font-bold text-zinc-900 dark:text-white">{{ $post->title }}</h5>
                                                <x-badge type="success">{{ $post->pivot->performance_rating ?? 'Satisfactory' }}</x-badge>
                                            </div>
                                            <p class="mt-1 text-zinc-600 dark:text-zinc-300">{{ $post->facility?->name }} • Ward: {{ $post->ward?->name }}</p>
                                            <div class="mt-2 text-zinc-500">Attendance: <strong>{{ $post->pivot->attendance_rate }}%</strong> • Remarks: "{{ $post->pivot->supervisor_comments ?? 'Active' }}"</div>
                                        </div>
                                    @empty
                                        <p class="text-zinc-400">No clinical rotations assigned.</p>
                                    @endforelse
                                </div>

                                <h4 class="font-bold text-zinc-900 dark:text-white uppercase tracking-wider text-[11px] pt-4">Clinical Procedure Logbook</h4>
                                <div class="divide-y divide-zinc-200 rounded-lg border border-zinc-200 dark:divide-zinc-800 dark:border-zinc-800">
                                    @forelse($selectedStudent->logbooks as $log)
                                        <div class="p-3 flex items-center justify-between">
                                            <div>
                                                <div class="font-semibold text-zinc-900 dark:text-white">{{ $log->procedure?->title }}</div>
                                                <p class="text-zinc-500 text-[11px]">{{ $log->procedure_date?->format('d M Y') }} • Patient Ref: {{ $log->patient_reference_code }} • Reflection: "{{ $log->student_reflection }}"</p>
                                            </div>
                                            <div class="text-right">
                                                <x-badge :type="$log->status">{{ ucfirst($log->status) }}</x-badge>
                                                <span class="block text-[10px] text-zinc-400 mt-1 uppercase">{{ $log->competency_level }}</span>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="p-4 text-center text-zinc-400">No procedures logged yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        @endif

                        <!-- TAB 4: Finance -->
                        @if($activeTab === 'finance')
                            <div class="space-y-4">
                                <h4 class="font-bold text-zinc-900 dark:text-white uppercase tracking-wider text-[11px]">Invoices & Fee Statements</h4>
                                <div class="divide-y divide-zinc-200 rounded-lg border border-zinc-200 dark:divide-zinc-800 dark:border-zinc-800">
                                    @forelse($selectedStudent->invoices as $inv)
                                        <div class="p-4 flex items-center justify-between">
                                            <div>
                                                <div class="font-mono font-bold text-zinc-900 dark:text-white">{{ $inv->invoice_number }}</div>
                                                <p class="text-zinc-500 text-[11px]">Due Date: {{ $inv->due_date?->format('d M Y') ?? 'N/A' }}</p>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-bold text-zinc-900 dark:text-white">NGN {{ number_format($inv->amount, 2) }}</div>
                                                <x-badge :type="$inv->status">{{ strtoupper($inv->status) }}</x-badge>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="p-4 text-center text-zinc-400">No invoices generated.</p>
                                    @endforelse
                                </div>
                            </div>
                        @endif

                    </div>

                    <!-- Footer -->
                    <div class="border-t border-zinc-200 bg-zinc-50 px-6 py-3 dark:border-zinc-800 dark:bg-zinc-800/50 flex justify-end">
                        <button wire:click="closeProfile" class="rounded-lg bg-zinc-800 px-4 py-2 text-xs font-semibold text-white hover:bg-zinc-700 dark:bg-zinc-700">
                            Close Dossier
                        </button>
                    </div>

                </div>
            </div>
        @endif

    </div>
</div>
