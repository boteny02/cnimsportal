<div class="py-8">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Finance, Bursary & Fee Clearance</h1>
                <p class="text-xs text-zinc-500 mt-1 dark:text-zinc-400">Institutional tuition and clinical levy structures, student ledger invoices, bank payments, and clearance approvals.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <button wire:click="openClearanceModal" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-700 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-emerald-600">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Grant Manual Financial Clearance
                </button>
            </div>
        </div>

        <!-- Approved Institutional Fee Structures Preview -->
        <div class="mb-8 rounded-xl border border-zinc-200 bg-white p-5 shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
            <h3 class="text-sm font-bold text-zinc-900 dark:text-white mb-3">Approved Nursing Fee Schedules</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($feeStructures as $fs)
                    <div class="rounded-xl border border-zinc-200/80 bg-zinc-50/50 p-4 dark:border-zinc-800 dark:bg-zinc-800/40">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-sky-700 dark:text-sky-300">{{ $fs->programme?->name }}</span>
                            <span class="rounded bg-sky-100 px-1.5 py-0.5 text-[10px] font-bold text-sky-800 dark:bg-sky-950 dark:text-sky-300">{{ $fs->level }}L</span>
                        </div>
                        <div class="mt-2 text-lg font-black text-zinc-900 dark:text-white">
                            NGN {{ number_format($fs->total_amount, 2) }}
                        </div>
                        <ul class="mt-2 space-y-1 text-[11px] text-zinc-500 dark:text-zinc-400">
                            @foreach($fs->items as $it)
                                <li class="flex justify-between">
                                    <span>{{ $it->name }}:</span>
                                    <span class="font-medium">NGN {{ number_format($it->amount, 0) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Invoices Ledger Filter -->
        <div class="mb-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="w-full sm:w-72">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search invoice number, student name, matric..." 
                    class="block w-full rounded-lg border border-zinc-300 px-3 py-2 text-xs shadow-2xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white"
                >
            </div>
            <div>
                <select wire:model.live="statusFilter" class="rounded-lg border border-zinc-300 px-3 py-2 text-xs dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                    <option value="all">All Invoices</option>
                    <option value="unpaid">Unpaid</option>
                    <option value="partially_paid">Partially Paid</option>
                    <option value="paid">Fully Settled</option>
                </select>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-left text-xs dark:divide-zinc-800">
                    <thead class="bg-zinc-50 font-semibold text-zinc-600 dark:bg-zinc-800/60 dark:text-zinc-300">
                        <tr>
                            <th class="px-4 py-3">Invoice Number</th>
                            <th class="px-4 py-3">Student Name</th>
                            <th class="px-4 py-3">Total Amount</th>
                            <th class="px-4 py-3">Paid Amount</th>
                            <th class="px-4 py-3">Balance</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($invoices as $inv)
                            <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition">
                                <td class="px-4 py-3 font-mono font-bold text-sky-700 dark:text-sky-300">
                                    {{ $inv->invoice_number }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-zinc-900 dark:text-white">{{ $inv->student?->full_name }}</div>
                                    <div class="text-[11px] font-mono text-zinc-500">{{ $inv->student?->student_number }}</div>
                                </td>
                                <td class="px-4 py-3 font-bold text-zinc-900 dark:text-white">
                                    NGN {{ number_format($inv->amount, 2) }}
                                </td>
                                <td class="px-4 py-3 text-emerald-600 font-semibold">
                                    NGN {{ number_format($inv->paid_amount, 2) }}
                                </td>
                                <td class="px-4 py-3 font-bold {{ $inv->balance > 0 ? 'text-rose-600' : 'text-zinc-400' }}">
                                    NGN {{ number_format($inv->balance, 2) }}
                                </td>
                                <td class="px-4 py-3">
                                    <x-badge :type="$inv->status">{{ strtoupper($inv->status) }}</x-badge>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($inv->status !== 'paid')
                                        <button wire:click="recordPaymentModal({{ $inv->id }})" class="rounded-md bg-amber-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-amber-500 shadow-2xs">
                                            Post Payment
                                        </button>
                                    @else
                                        <span class="text-xs text-emerald-600 font-medium">Cleared</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-xs text-zinc-500">
                                    No invoices match current filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
                {{ $invoices->links() }}
            </div>
        </div>

        <!-- Post Payment Modal -->
        @if($showPaymentModal && $selectedInvoice)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-2xs">
                <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center justify-between border-b border-zinc-200 px-6 py-4 dark:border-zinc-800">
                        <h3 class="text-base font-bold text-zinc-900 dark:text-white">Post Payment: {{ $selectedInvoice->invoice_number }}</h3>
                        <button wire:click="closePaymentModal" class="text-zinc-400 hover:text-zinc-600">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form wire:submit="submitPayment" class="p-6 space-y-4 text-xs">
                        <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-800">
                            <div><strong>Student:</strong> {{ $selectedInvoice->student?->full_name }} ({{ $selectedInvoice->student?->student_number }})</div>
                            <div class="mt-1"><strong>Outstanding Balance:</strong> <span class="text-rose-600 font-bold">NGN {{ number_format($selectedInvoice->balance, 2) }}</span></div>
                        </div>

                        <div>
                            <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Amount to Credit (NGN) *</label>
                            <input type="number" step="0.01" wire:model="paymentAmount" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm font-bold dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                        </div>

                        <div>
                            <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Payment Channel</label>
                            <select wire:model="paymentMethod" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                                <option value="bank_transfer">Direct Bank Transfer / Remita</option>
                                <option value="pos">Hospital / Bursary POS</option>
                                <option value="paystack">Paystack Online Payment</option>
                                <option value="flutterwave">Flutterwave Gateway</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                            <button type="button" wire:click="closePaymentModal" class="rounded-lg border border-zinc-300 px-4 py-2 font-semibold text-zinc-700 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                                Cancel
                            </button>
                            <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2 font-semibold text-white hover:bg-amber-500">
                                Confirm & Issue Receipt
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Manual Clearance Modal -->
        @if($showClearanceModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-2xs">
                <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center justify-between border-b border-zinc-200 px-6 py-4 dark:border-zinc-800">
                        <h3 class="text-base font-bold text-zinc-900 dark:text-white">Grant Financial Clearance</h3>
                        <button wire:click="closeClearanceModal" class="text-zinc-400 hover:text-zinc-600">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form wire:submit="grantClearance" class="p-6 space-y-4 text-xs">
                        <div>
                            <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Select Student *</label>
                            <select wire:model="selectedStudentId" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                                <option value="">Select Student</option>
                                @foreach($students as $st)
                                    <option value="{{ $st->id }}">{{ $st->student_number }} - {{ $st->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Bursary Remarks</label>
                            <input type="text" wire:model="clearanceRemarks" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                            <button type="button" wire:click="closeClearanceModal" class="rounded-lg border border-zinc-300 px-4 py-2 font-semibold text-zinc-700 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                                Cancel
                            </button>
                            <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 font-semibold text-white hover:bg-emerald-500">
                                Grant Clearance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</div>
