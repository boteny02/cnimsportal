<?php

namespace App\Livewire\Student;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Services\Finance\PaymentService;
use Livewire\Component;

class FeesAndReceipts extends Component
{
    public ?Student $student = null;

    public ?Invoice $activeInvoice = null;

    // Payment Simulation Modal
    public bool $showPaymentModal = false;

    public float $amountToPay = 0.0;

    public string $paymentMethod = 'paystack';

    // View Receipt Modal
    public ?Payment $selectedReceipt = null;

    public bool $showReceiptModal = false;

    public function mount(): void
    {
        $user = auth()->user();
        $this->student = ($user && $user->student) ? $user->student : Student::first();
        $this->loadInvoices();
    }

    public function loadInvoices(): void
    {
        if (! $this->student) {
            return;
        }

        $this->activeInvoice = $this->student->invoices()->with('feeStructure.items')->latest()->first();
        if ($this->activeInvoice) {
            $this->amountToPay = (float) $this->activeInvoice->balance;
        }
    }

    public function openPaymentModal(): void
    {
        if (! $this->activeInvoice) {
            return;
        }
        $this->amountToPay = (float) $this->activeInvoice->balance;
        $this->showPaymentModal = true;
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
    }

    public function processOnlinePayment(PaymentService $service): void
    {
        if (! $this->activeInvoice) {
            return;
        }

        $payment = $service->recordPayment(
            $this->activeInvoice,
            $this->amountToPay,
            $this->paymentMethod
        );

        session()->flash('success', "Payment successful! Receipt: {$payment->receipt_number}. Course registration clearance granted.");
        $this->closePaymentModal();
        $this->loadInvoices();
        $this->viewReceipt($payment->id);
    }

    public function viewReceipt(int $paymentId): void
    {
        $this->selectedReceipt = Payment::with(['invoice.feeStructure', 'student'])->findOrFail($paymentId);
        $this->showReceiptModal = true;
    }

    public function closeReceiptModal(): void
    {
        $this->showReceiptModal = false;
        $this->selectedReceipt = null;
    }

    public function render()
    {
        $payments = $this->student?->payments()->with('invoice')->latest('paid_at')->get() ?? collect();

        return view('livewire.student.fees-and-receipts', [
            'payments' => $payments,
            'activeInvoice' => $this->activeInvoice,
        ])->layout('layouts.institutional', ['title' => 'Student Fees, Invoices & Payment Receipts']);
    }
}
