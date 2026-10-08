<?php

namespace App\Livewire\Admin\Finance;

use App\Models\AcademicSession;
use App\Models\FeeStructure;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Services\Finance\PaymentService;
use Livewire\Component;
use Livewire\WithPagination;

class FinanceManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    // Record Payment Modal
    public ?Invoice $selectedInvoice = null;

    public float $paymentAmount = 0.0;

    public string $paymentMethod = 'bank_transfer';

    public bool $showPaymentModal = false;

    // Financial Clearance Modal
    public bool $showClearanceModal = false;

    public ?int $selectedStudentId = null;

    public string $clearanceRemarks = 'Approved by Bursary for First Semester Examination & Registration.';

    public function recordPaymentModal(int $invoiceId): void
    {
        $this->selectedInvoice = Invoice::with('student')->findOrFail($invoiceId);
        $this->paymentAmount = (float) $this->selectedInvoice->balance;
        $this->showPaymentModal = true;
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->selectedInvoice = null;
    }

    public function submitPayment(PaymentService $service): void
    {
        if (! $this->selectedInvoice) {
            return;
        }

        $this->authorize('payments.verify');

        $this->validate([
            'paymentAmount' => 'required|numeric|min:1',
        ]);

        $service->recordPayment(
            $this->selectedInvoice,
            $this->paymentAmount,
            $this->paymentMethod
        );

        session()->flash('success', 'Payment of NGN '.number_format($this->paymentAmount, 2)." credited to {$this->selectedInvoice->invoice_number}.");
        $this->closePaymentModal();
    }

    public function openClearanceModal(): void
    {
        $this->showClearanceModal = true;
    }

    public function closeClearanceModal(): void
    {
        $this->showClearanceModal = false;
        $this->selectedStudentId = null;
    }

    public function grantClearance(PaymentService $service): void
    {
        $this->authorize('clearance.approve');

        $this->validate([
            'selectedStudentId' => 'required|exists:students,id',
        ]);

        $student = Student::findOrFail($this->selectedStudentId);
        $session = AcademicSession::current();

        $service->grantFinancialClearance(
            $student,
            $session,
            1,
            'course_registration',
            auth()->user(),
            $this->clearanceRemarks
        );

        session()->flash('success', "Financial clearance granted to {$student->student_number}.");
        $this->closeClearanceModal();
    }

    public function overrideClearance(PaymentService $service): void
    {
        $this->authorize('clearance.override');

        $this->validate([
            'selectedStudentId' => 'required|exists:students,id',
            'clearanceRemarks' => 'required|string|min:5',
        ]);

        $student = Student::findOrFail($this->selectedStudentId);
        $session = AcademicSession::current();

        $service->overrideFinancialClearance(
            $student,
            $session,
            1,
            'course_registration',
            auth()->user(),
            $this->clearanceRemarks
        );

        session()->flash('success', "Financial clearance override granted to {$student->student_number}.");
        $this->closeClearanceModal();
    }

    public function render()
    {
        $query = Invoice::with(['student', 'feeStructure', 'academicSession']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', "%{$this->search}%")
                    ->orWhereHas('student', function ($sq) {
                        $sq->where('first_name', 'like', "%{$this->search}%")
                            ->orWhere('last_name', 'like', "%{$this->search}%")
                            ->orWhere('student_number', 'like', "%{$this->search}%");
                    });
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $invoices = $query->latest()->paginate(10);

        return view('livewire.admin.finance.finance-manager', [
            'invoices' => $invoices,
            'feeStructures' => FeeStructure::with(['programme', 'items'])->get(),
            'students' => Student::where('status', 'active')->get(),
            'recentPayments' => Payment::with(['student', 'invoice'])->latest()->take(5)->get(),
        ])->layout('layouts.institutional', ['title' => 'Finance, Fee Structures & Bursary']);
    }
}
