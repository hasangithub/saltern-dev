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
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('source_type'); // Type of payment source (e.g., "Loan Repayment", "Weighbridge")
            $table->unsignedBigInteger('source_id')->nullable(); // Reference to the specific source
            $table->decimal('amount', 10, 2); // Payment amount
            $table->date('payment_date'); // Date of the payment
            $table->string('payment_method'); // Payment method (e.g., "Cash", "Bank Transfer")
            $table->text('description')->nullable(); // Optional description of the payment
            $table->text('notes')->nullable(); // Additional remarks
            $table->timestamps(); // Created at and updated at timestamps

            // Index for faster lookups
            $table->index(['source_type', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
