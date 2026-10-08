<div class="py-8">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Fees, Invoices & Payment Receipts</h1>
                <p class="text-xs text-zinc-500 mt-1 dark:text-zinc-400">View semester tuition breakdowns, make secure online payments via Paystack/Flutterwave, and generate official bursary receipts.</p>
            </div>
            
            @if($activeInvoice && $activeInvoice->balance > 0)
                <div>
                    <button wire:click="openPaymentModal" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-emerald-500">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Pay Outstanding Balance (NGN {{ number_format($activeInvoice->balance, 2) }})
                    </button>
                </div>
            @endif
        </div>

        <!-- Current Semester Invoice Card -->
        @if($activeInvoice)
            <div class="mb-8 overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 bg-zinc-50 p-5 dark:border-zinc-800 dark:bg-zinc-800/50 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Current Statement</span>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-white">{{ $activeInvoice->feeStructure?->title ?? 'Semester Tuition & Levies' }}</h3>
                        <p class="text-xs font-mono text-zinc-500">{{ $activeInvoice->invoice_number }}</p>
                    </div>
                    <div>
                        <x-badge :type="$activeInvoice->status">{{ strtoupper($activeInvoice->status) }}</x-badge>
                    </div>
                </div>

                <div class="p-6">
                    <h4 class="text-xs font-bold uppercase text-zinc-400 mb-3 tracking-wider">Invoice Item Breakdown</h4>
                    <div class="divide-y divide-zinc-100 dark:divide-zinc-800 text-xs">
                        @if($activeInvoice->feeStructure)
                            @foreach($activeInvoice->feeStructure->items as $item)
                                <div class="py-2.5 flex items-center justify-between">
                                    <span class="text-zinc-700 dark:text-zinc-300 font-medium">{{ $item->name }}</span>
                                    <span class="font-bold text-zinc-900 dark:text-white">NGN {{ number_format($item->amount, 2) }}</span>
                                </div>
                            @endforeach
                        @else
                            <div class="py-2.5 flex items-center justify-between">
                                <span>Tuition & Clinical Fees</span>
                                <span class="font-bold">NGN {{ number_format($activeInvoice->amount, 2) }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800 grid grid-cols-3 gap-4 text-center">
                        <div class="rounded-xl bg-zinc-50 p-3 dark:bg-zinc-800/40">
                            <span class="text-[11px] text-zinc-400 font-medium">Total Billed</span>
                            <div class="text-sm font-bold text-zinc-900 dark:text-white">NGN {{ number_format($activeInvoice->amount, 2) }}</div>
                        </div>
                        <div class="rounded-xl bg-emerald-50 p-3 dark:bg-emerald-950/30">
                            <span class="text-[11px] text-emerald-700 dark:text-emerald-300 font-medium">Total Paid</span>
                            <div class="text-sm font-bold text-emerald-700 dark:text-emerald-300">NGN {{ number_format($activeInvoice->paid_amount, 2) }}</div>
                        </div>
                        <div class="rounded-xl bg-rose-50 p-3 dark:bg-rose-950/30">
                            <span class="text-[11px] text-rose-700 dark:text-rose-300 font-medium">Outstanding Balance</span>
                            <div class="text-sm font-bold text-rose-700 dark:text-rose-300">NGN {{ number_format($activeInvoice->balance, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Payment Receipts Table -->
        <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-2xs dark:border-zinc-800 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 bg-zinc-50 px-5 py-3 dark:border-zinc-800 dark:bg-zinc-800/60 font-bold text-xs text-zinc-900 dark:text-white">
                Payment History & Official Receipts
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 text-left text-xs dark:divide-zinc-800">
                    <thead class="bg-zinc-50/50 font-semibold text-zinc-600 dark:bg-zinc-800/40 dark:text-zinc-300">
                        <tr>
                            <th class="px-4 py-3">Receipt Number</th>
                            <th class="px-4 py-3">Payment Date</th>
                            <th class="px-4 py-3">Transaction Ref</th>
                            <th class="px-4 py-3">Amount Paid</th>
                            <th class="px-4 py-3">Channel</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($payments as $pay)
                            <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition">
                                <td class="px-4 py-3 font-mono font-bold text-emerald-700 dark:text-emerald-300">
                                    {{ $pay->receipt_number }}
                                </td>
                                <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">
                                    {{ $pay->paid_at?->format('d M Y, h:i A') }}
                                </td>
                                <td class="px-4 py-3 font-mono text-[11px] text-zinc-500">
                                    {{ $pay->transaction_reference }}
                                </td>
                                <td class="px-4 py-3 font-bold text-zinc-900 dark:text-white">
                                    NGN {{ number_format($pay->amount, 2) }}
                                </td>
                                <td class="px-4 py-3 uppercase text-[10px] font-semibold text-zinc-500">
                                    {{ $pay->payment_method }}
                                </td>
                                <td class="px-4 py-3">
                                    <x-badge type="success">{{ ucfirst($pay->status) }}</x-badge>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button wire:click="viewReceipt({{ $pay->id }})" class="rounded-md border border-zinc-300 bg-white px-2.5 py-1 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                                        View Receipt
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-xs text-zinc-400">
                                    No payment transactions recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pay Online Modal -->
        @if($showPaymentModal && $activeInvoice)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-2xs">
                <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center justify-between border-b border-zinc-200 px-6 py-4 dark:border-zinc-800">
                        <div class="flex items-center gap-2">
                            <span class="size-3 rounded-full bg-emerald-500 animate-pulse"></span>
                            <h3 class="text-base font-bold text-zinc-900 dark:text-white">Paystack / Flutterwave Gateway</h3>
                        </div>
                        <button wire:click="closePaymentModal" class="text-zinc-400 hover:text-zinc-600">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form wire:submit="processOnlinePayment" class="p-6 space-y-4 text-xs">
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900/60 dark:bg-emerald-950/30">
                            <span class="text-[10px] font-bold text-emerald-800 dark:text-emerald-300 uppercase">Secured Checkout</span>
                            <div class="mt-1 text-xl font-black text-emerald-900 dark:text-emerald-100">
                                NGN {{ number_format($amountToPay, 2) }}
                            </div>
                            <p class="text-[11px] text-emerald-700 dark:text-emerald-300 mt-0.5">Paying invoice: {{ $activeInvoice->invoice_number }}</p>
                        </div>

                        <div>
                            <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Amount to Settle (NGN)</label>
                            <input type="number" step="0.01" wire:model="amountToPay" class="mt-1 block w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm font-bold dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                        </div>

                        <div>
                            <label class="block font-semibold text-zinc-700 dark:text-zinc-300">Payment Gateway</label>
                            <div class="mt-1 grid grid-cols-2 gap-2">
                                <label class="rounded-lg border p-3 flex items-center gap-2 cursor-pointer {{ $paymentMethod === 'paystack' ? 'border-sky-600 bg-sky-50 dark:border-sky-500 dark:bg-sky-950/40' : 'border-zinc-200' }}">
                                    <input type="radio" wire:model="paymentMethod" value="paystack">
                                    <span class="font-bold">Paystack</span>
                                </label>
                                <label class="rounded-lg border p-3 flex items-center gap-2 cursor-pointer {{ $paymentMethod === 'flutterwave' ? 'border-sky-600 bg-sky-50 dark:border-sky-500 dark:bg-sky-950/40' : 'border-zinc-200' }}">
                                    <input type="radio" wire:model="paymentMethod" value="flutterwave">
                                    <span class="font-bold">Flutterwave</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                            <button type="button" wire:click="closePaymentModal" class="rounded-lg border border-zinc-300 px-4 py-2 font-semibold text-zinc-700 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                                Cancel
                            </button>
                            <button type="submit" class="rounded-lg bg-emerald-600 px-5 py-2 font-bold text-white hover:bg-emerald-500 shadow-xs">
                                Confirm & Pay Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Official Electronic Receipt Modal -->
        @if($showReceiptModal && $selectedReceipt)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-2xs">
                <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 print:m-0 print:border-none print:shadow-none">
                    
                    <div class="border-b border-zinc-200 bg-zinc-50 p-6 text-center dark:border-zinc-800 dark:bg-zinc-800/50">
                        <div class="size-10 rounded-xl bg-teal-700 text-white font-bold flex items-center justify-center mx-auto mb-2">
                            C
                        </div>
                        <h3 class="text-base font-black text-zinc-900 dark:text-white uppercase">College of Nursing & Midwifery Sciences</h3>
                        <p class="text-xs text-zinc-500">Official Electronic Payment Receipt</p>
                        <span class="mt-2 inline-block rounded-full bg-emerald-100 px-3 py-0.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                            PAYMENT CONFIRMED
                        </span>
                    </div>

                    <div class="p-6 space-y-4 text-xs">
                        <div class="grid grid-cols-2 gap-4 rounded-xl border border-zinc-200 bg-zinc-50/50 p-4 dark:border-zinc-800 dark:bg-zinc-800/40">
                            <div>
                                <span class="text-zinc-400 font-medium">Receipt Number:</span>
                                <div class="font-mono font-bold text-zinc-900 dark:text-white">{{ $selectedReceipt->receipt_number }}</div>
                            </div>
                            <div>
                                <span class="text-zinc-400 font-medium">Payment Timestamp:</span>
                                <div class="font-medium text-zinc-800 dark:text-zinc-200">{{ $selectedReceipt->paid_at?->format('d M Y, h:i A') }}</div>
                            </div>
                            <div>
                                <span class="text-zinc-400 font-medium">Student Name:</span>
                                <div class="font-bold text-zinc-900 dark:text-white">{{ $selectedReceipt->student?->full_name }}</div>
                            </div>
                            <div>
                                <span class="text-zinc-400 font-medium">Matriculation Number:</span>
                                <div class="font-mono font-bold text-sky-700 dark:text-sky-300">{{ $selectedReceipt->student?->student_number }}</div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-800 flex items-center justify-between">
                            <div>
                                <span class="font-semibold text-zinc-900 dark:text-white">Amount Paid:</span>
                                <div class="text-[11px] text-zinc-500">Channel: {{ strtoupper($selectedReceipt->payment_method) }} • Ref: {{ $selectedReceipt->transaction_reference }}</div>
                            </div>
                            <div class="text-xl font-black text-emerald-600">
                                NGN {{ number_format($selectedReceipt->amount, 2) }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between border-t border-zinc-200 bg-zinc-50 px-6 py-3 dark:border-zinc-800 dark:bg-zinc-800/50">
                        <button type="button" onclick="window.print()" class="rounded-lg border border-zinc-300 bg-white px-3 py-1.5 font-semibold text-zinc-700 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                            Print Receipt
                        </button>
                        <button type="button" wire:click="closeReceiptModal" class="rounded-lg bg-zinc-800 px-4 py-1.5 font-semibold text-white hover:bg-zinc-700">
                            Close
                        </button>
                    </div>

                </div>
            </div>
        @endif

    </div>
</div>
