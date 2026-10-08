<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programme_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->integer('level')->default(100);
            $table->string('fee_type')->default('tuition'); // application, acceptance, tuition, clinical
            $table->string('title'); // e.g. 2025/2026 Application Fee, 2025/2026 Acceptance Fee, etc.
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('fee_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_structure_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Application Processing, Acceptance Levy, Tuition, Clinical Levy, Exam Fee
            $table->decimal('amount', 10, 2);
            $table->boolean('is_mandatory')->default(true);
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // e.g., INV/2026/00481
            $table->string('invoice_type')->default('tuition'); // application_fee, acceptance_fee, tuition
            $table->foreignId('student_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('application_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('fee_structure_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('semester')->nullable();
            $table->decimal('amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0.00);
            $table->decimal('balance', 12, 2);
            $table->enum('status', ['unpaid', 'partially_paid', 'paid'])->default('unpaid');
            $table->date('due_date')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('application_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('transaction_reference')->unique(); // e.g., PAY-65F93A-9817
            $table->string('payment_method')->default('card'); // card, paystack, bank_transfer, pos
            $table->string('gateway_reference')->nullable();
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending', 'successful', 'failed', 'refunded'])->default('successful');
            $table->string('receipt_number')->unique()->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('financial_clearances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('semester')->default(1);
            $table->boolean('is_cleared')->default(false);
            $table->enum('clearance_type', ['course_registration', 'exam_clearance', 'graduation'])->default('course_registration');
            $table->foreignId('cleared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cleared_at')->nullable();
            $table->boolean('is_overridden')->default(false);
            $table->foreignId('overridden_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('override_reason')->nullable();
            $table->timestamp('overridden_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'academic_session_id', 'semester', 'clearance_type'], 'student_fin_clearance_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_clearances');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('fee_items');
        Schema::dropIfExists('fee_structures');
    }
};
