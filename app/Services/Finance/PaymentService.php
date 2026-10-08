<?php

namespace App\Services\Finance;

use App\Models\AcademicSession;
use App\Models\Application;
use App\Models\FeeStructure;
use App\Models\FinancialClearance;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use App\Services\Audit\AuditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    public function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $random = strtoupper(Str::random(4));
        $count = Invoice::whereYear('created_at', $year)->count() + 1;

        return sprintf('INV/%s/%04d-%s', $year, $count, $random);
    }

    public function generateReceiptNumber(): string
    {
        $year = date('Y');
        $random = strtoupper(Str::random(4));
        $count = Payment::whereYear('created_at', $year)->count() + 1;

        return sprintf('REC/%s/%04d-%s', $year, $count, $random);
    }

    public function generateInvoiceForStudent(
        Student $student,
        AcademicSession $session,
        ?int $semester = null
    ): Invoice {
        $levelNumeric = $student->currentLevel?->numeric_level ?? 100;

        $feeStructure = FeeStructure::where('programme_id', $student->programme_id)
            ->where('academic_session_id', $session->id)
            ->where('level', $levelNumeric)
            ->where('fee_type', 'tuition')
            ->first();

        $amount = $feeStructure ? $feeStructure->total_amount : 150000.00;

        return Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'invoice_type' => 'tuition',
            'student_id' => $student->id,
            'fee_structure_id' => $feeStructure?->id,
            'academic_session_id' => $session->id,
            'semester' => $semester ?? 1,
            'amount' => $amount,
            'paid_amount' => 0.00,
            'balance' => $amount,
            'status' => 'unpaid',
            'due_date' => now()->addDays(30),
        ]);
    }

    public function generateInvoiceForApplication(
        Application $application,
        string $feeType = 'application_fee'
    ): Invoice {
        // Check if invoice already exists
        $existing = Invoice::where('application_id', $application->id)
            ->where('invoice_type', $feeType)
            ->first();

        if ($existing) {
            return $existing;
        }

        // Search for Bursar-configured Fee Structure
        $mappedCategory = ($feeType === 'acceptance_fee') ? 'acceptance' : 'application';
        $feeStructure = FeeStructure::where('fee_type', $mappedCategory)
            ->where('academic_session_id', $application->academic_session_id)
            ->where('is_active', true)
            ->first();

        $defaultAmount = ($feeType === 'acceptance_fee') ? 35000.00 : 15000.00;
        $amount = $feeStructure ? (float) $feeStructure->total_amount : $defaultAmount;

        return Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'invoice_type' => $feeType,
            'application_id' => $application->id,
            'student_id' => null,
            'fee_structure_id' => $feeStructure?->id,
            'academic_session_id' => $application->academic_session_id,
            'semester' => null,
            'amount' => $amount,
            'paid_amount' => 0.00,
            'balance' => $amount,
            'status' => 'unpaid',
            'due_date' => now()->addDays(21),
        ]);
    }

    public function recordPayment(
        Invoice $invoice,
        float $amount,
        string $paymentMethod = 'paystack',
        ?string $gatewayRef = null
    ): Payment {
        return DB::transaction(function () use ($invoice, $amount, $paymentMethod, $gatewayRef) {
            $ref = 'PAY-'.strtoupper(Str::random(10));
            $receiptNum = $this->generateReceiptNumber();

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'student_id' => $invoice->student_id,
                'application_id' => $invoice->application_id,
                'transaction_reference' => $ref,
                'payment_method' => $paymentMethod,
                'gateway_reference' => $gatewayRef ?? $ref,
                'amount' => $amount,
                'status' => 'successful',
                'receipt_number' => $receiptNum,
                'paid_at' => now(),
            ]);

            // Update invoice
            $newPaid = $invoice->paid_amount + $amount;
            $newBalance = max(0.00, $invoice->amount - $newPaid);
            $newStatus = $newBalance <= 0.00 ? 'paid' : 'partially_paid';

            $invoice->update([
                'paid_amount' => $newPaid,
                'balance' => $newBalance,
                'status' => $newStatus,
            ]);

            // If this is an application-related invoice (Application Fee or Acceptance Fee), update application state!
            if ($invoice->application_id && $newStatus === 'paid') {
                $application = $invoice->application;
                if ($application) {
                    if ($invoice->invoice_type === 'application_fee') {
                        $application->update([
                            'application_fee_paid' => true,
                            'application_fee_paid_at' => now(),
                            'status' => 'fee_paid',
                        ]);
                    } elseif ($invoice->invoice_type === 'acceptance_fee') {
                        $application->update([
                            'acceptance_fee_paid' => true,
                            'acceptance_fee_paid_at' => now(),
                            'status' => 'acceptance_paid',
                        ]);
                    }
                }
            }

            // If student tuition invoice is fully paid, auto-grant financial clearance for course registration!
            if ($invoice->student_id && $newStatus === 'paid') {
                FinancialClearance::updateOrCreate(
                    [
                        'student_id' => $invoice->student_id,
                        'academic_session_id' => $invoice->academic_session_id,
                        'semester' => $invoice->semester ?? 1,
                        'clearance_type' => 'course_registration',
                    ],
                    [
                        'is_cleared' => true,
                        'cleared_at' => now(),
                        'remarks' => "Automatic fee clearance upon full payment (Receipt: {$receiptNum})",
                    ]
                );
            }

            AuditService::log(
                'finance.payment_received',
                $payment,
                null,
                ['amount' => $amount, 'invoice' => $invoice->invoice_number, 'receipt' => $receiptNum],
                'Payment of NGN '.number_format($amount, 2)." processed for invoice {$invoice->invoice_number}"
            );

            return $payment;
        });
    }

    public function grantFinancialClearance(
        Student $student,
        AcademicSession $session,
        int $semester,
        string $type,
        User $officer,
        ?string $remarks = null
    ): FinancialClearance {
        $clearance = FinancialClearance::updateOrCreate(
            [
                'student_id' => $student->id,
                'academic_session_id' => $session->id,
                'semester' => $semester,
                'clearance_type' => $type,
            ],
            [
                'is_cleared' => true,
                'cleared_by' => $officer->id,
                'cleared_at' => now(),
                'remarks' => $remarks ?? 'Manual financial clearance granted by Finance Officer',
            ]
        );

        AuditService::log(
            'finance.clearance_granted',
            $clearance,
            null,
            ['type' => $type, 'session' => $session->name, 'semester' => $semester],
            "Financial clearance ({$type}) granted to {$student->student_number} by {$officer->name}",
            $officer
        );

        return $clearance;
    }

    public function overrideFinancialClearance(
        Student $student,
        AcademicSession $session,
        int $semester,
        string $type,
        User $officer,
        string $overrideReason
    ): FinancialClearance {
        // Enforce clearance.override permission
        if (! $officer->can('clearance.override')) {
            abort(403, 'Access Denied: You do not have permission to override financial clearance.');
        }

        $clearance = FinancialClearance::updateOrCreate(
            [
                'student_id' => $student->id,
                'academic_session_id' => $session->id,
                'semester' => $semester,
                'clearance_type' => $type,
            ],
            [
                'is_cleared' => true,
                'cleared_by' => $officer->id,
                'cleared_at' => now(),
                'is_overridden' => true,
                'overridden_by' => $officer->id,
                'override_reason' => $overrideReason,
                'overridden_at' => now(),
                'remarks' => "OVERRIDE GRANTED by {$officer->name}: {$overrideReason}",
            ]
        );

        AuditService::log(
            'finance.clearance_override',
            $clearance,
            null,
            ['type' => $type, 'session' => $session->name, 'reason' => $overrideReason, 'officer' => $officer->email],
            "Financial clearance override granted to {$student->student_number} by {$officer->name}. Reason: {$overrideReason}",
            $officer
        );

        return $clearance;
    }
}
