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
        Schema::create('journal_details', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('journal_id')->constrained('journal_entries')->onDelete('cascade'); // Foreign key to journal_entries
            $table->foreignId('sub_ledger_id')->constrained('sub_ledgers')->onDelete('restrict'); // Foreign key to sub_ledgers table
            $table->decimal('debit_amount', 15, 2)->nullable()->default(0); // Debit amount
            $table->decimal('credit_amount', 15, 2)->nullable()->default(0); // Credit amount
            $table->text('description')->nullable(); // Description of the transaction
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_details');
    }
};
