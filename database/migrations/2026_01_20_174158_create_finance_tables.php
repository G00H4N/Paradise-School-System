<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Parent Wallets (Family Credit System - Video 2)
        Schema::create('parent_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('balance', 10, 2)->default(0); // Advance payments here
            $table->timestamps();
        });

        // 2. Fee Invoices (Monthly Bills)
        Schema::create('fee_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('fee_type_id')->constrained('fee_types');
            $table->string('invoice_title'); // "April 2026 Fee"
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0); // Sibling Discount logic
            $table->enum('status', ['paid', 'unpaid', 'partial'])->default('unpaid');
            $table->date('due_date');
            $table->string('session_year');
            $table->timestamps();
        });

        // 3. Fee Payments (Transactions)
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_invoice_id')->constrained('fee_invoices')->onDelete('cascade');
            $table->decimal('amount_paid', 10, 2);
            $table->string('payment_method')->default('Cash'); // Cash, Wallet, Bank
            $table->foreignId('received_by')->nullable()->constrained('users');
            $table->date('payment_date');
            $table->timestamps();
        });

        // 4. Expenses (Daily Operations)
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_category_id')->constrained('expense_categories');
            $table->string('title');
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->foreignId('added_by')->constrained('users');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        // 5. Salaries (Payroll Module 4)
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff_details')->onDelete('cascade');
            $table->string('salary_month'); // "Jan 2026"
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('bonus', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0); // Loan cuts
            $table->decimal('net_salary', 10, 2);
            $table->enum('status', ['paid', 'unpaid'])->default('unpaid');
            $table->timestamps();
        });

        // 6. Staff Loans (Auto-deduction Logic)
        Schema::create('staff_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff_details')->onDelete('cascade');
            $table->decimal('loan_amount', 10, 2);
            $table->integer('total_installments');
            $table->decimal('monthly_installment', 10, 2);
            $table->decimal('remaining_balance', 10, 2);
            $table->enum('status', ['active', 'cleared'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_loans');
        Schema::dropIfExists('salaries');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('fee_payments');
        Schema::dropIfExists('fee_invoices');
        Schema::dropIfExists('parent_wallets');
    }
};