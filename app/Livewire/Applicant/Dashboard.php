<?php

namespace App\Livewire\Applicant;

use App\Models\Application;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Services\Admissions\ApplicationService;
use App\Services\Finance\PaymentService;
use Livewire\Component;

class Dashboard extends Component
{
    public ?Application $application = null;

    public string $paymentMethod = 'card';

    public bool $showExamSlipModal = false;

    public bool $showAdmissionLetterModal = false;

    public ?Payment $selectedReceipt = null;

    public bool $showReceiptModal = false;

    public function mount(): void
    {
        $this->loadApplication();
    }

    public function loadApplication(): void
    {
        $user = auth()->user();

        if ($user) {
            $this->application = Application::with(['programme', 'academicSession', 'invoices.payments', 'screener', 'examScorer'])
                ->where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->latest()
                ->first();

            if ($this->application && $user->hasRole('applicant')) {
                $this->authorize('view', $this->application);
            }
        }

        // Fallback for demo preview by admins/staff if no user application exists yet
        if (! $this->application && ($user && ! $user->hasRole('applicant'))) {
            $this->application = Application::with(['programme', 'academicSession', 'invoices.payments', 'screener', 'examScorer'])
                ->latest()
                ->first();
        }
    }

    public function payApplicationFee(PaymentService $paymentService): void
    {
        if (! $this->application) {
            return;
        }

        // Ensure invoice exists
        $invoice = $this->application->invoices()->where('invoice_type', 'application_fee')->first();
        if (! $invoice) {
            $invoice = $paymentService->generateInvoiceForApplication($this->application, 'application_fee');
        }

        if ($invoice->status === 'paid') {
            session()->flash('success', 'Application fee is already paid.');

            return;
        }

        // Process payment
        $payment = $paymentService->recordPayment(
            $invoice,
            (float) $invoice->balance,
            $this->paymentMethod
        );

        $this->application->fresh();
        $this->selectedReceipt = $payment;
        $this->showReceiptModal = true;

        session()->flash('success', 'Application fee of NGN '.number_format($invoice->amount, 2).' paid successfully! Receipt: '.$payment->receipt_number);
    }

    public function payAcceptanceFee(PaymentService $paymentService, ApplicationService $appService): void
    {
        if (! $this->application) {
            return;
        }

        $invoice = $this->application->invoices()->where('invoice_type', 'acceptance_fee')->first();
        if (! $invoice) {
            $invoice = $paymentService->generateInvoiceForApplication($this->application, 'acceptance_fee');
        }

        if ($invoice->status === 'paid') {
            session()->flash('success', 'Acceptance fee is already paid.');

            return;
        }

        // Process payment
        $payment = $paymentService->recordPayment(
            $invoice,
            (float) $invoice->balance,
            $this->paymentMethod
        );

        // Auto matriculate upon acceptance fee payment if not yet matriculated
        if ($this->application->status !== 'admitted') {
            $officer = auth()->user() ?? User::first();
            $student = $appService->admit($this->application, $officer);
            session()->flash('success', "Acceptance fee paid successfully! You have been matriculated with Student Number: {$student->student_number}.");
        } else {
            session()->flash('success', 'Acceptance fee of NGN '.number_format($invoice->amount, 2).' paid successfully!');
        }

        $this->application->fresh();
        $this->selectedReceipt = $payment;
        $this->showReceiptModal = true;
    }

    public function viewReceipt(int $paymentId): void
    {
        $this->selectedReceipt = Payment::with(['invoice', 'application'])->findOrFail($paymentId);
        $this->showReceiptModal = true;
    }

    public function closeReceiptModal(): void
    {
        $this->showReceiptModal = false;
        $this->selectedReceipt = null;
    }

    public function openExamSlip(): void
    {
        $this->showExamSlipModal = true;
    }

    public function closeExamSlip(): void
    {
        $this->showExamSlipModal = false;
    }

    public function openAdmissionLetter(): void
    {
        $this->showAdmissionLetterModal = true;
    }

    public function closeAdmissionLetter(): void
    {
        $this->showAdmissionLetterModal = false;
    }

    public function render()
    {
        $this->loadApplication();

        $appFeeInvoice = $this->application?->invoices()->where('invoice_type', 'application_fee')->first();
        $accFeeInvoice = $this->application?->invoices()->where('invoice_type', 'acceptance_fee')->first();

        return view('livewire.applicant.dashboard', [
            'app' => $this->application,
            'appFeeInvoice' => $appFeeInvoice,
            'accFeeInvoice' => $accFeeInvoice,
        ])->layout('layouts.institutional', ['title' => 'Applicant Admissions Portal & Lifecycle Desk']);
    }
}
