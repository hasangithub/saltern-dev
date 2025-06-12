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
        Schema::create('owner_loan_repayments', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('owner_loan_id'); // Foreign key to owner_loans
            $table->decimal('amount', 10, 2); // Repayment amount
            $table->date('repayment_date'); // Date of repayment
            $table->string('payment_method')->nullable(); // Optional payment method (e.g., cash, bank transfer)
            $table->text('notes')->nullable(); // Optional notes about the repayment
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owner_loan_repayments');
    }
};
