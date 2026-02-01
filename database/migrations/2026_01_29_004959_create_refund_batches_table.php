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
        Schema::create('refund_batches', function (Blueprint $table) {
            $table->id();

            // Human readable name
            $table->string('name'); 
            // e.g. "January 2026 Service Charge Refund"

            // Refund rule
            $table->decimal('refund_percentage', 5, 2); 
            // 30.00

            // Filter window
            $table->date('date_from');
            $table->date('date_to');

            // Workflow state
            $table->enum('status', [
                'draft',
                'approved',
                'cancelled'
            ])->default('draft');

            // Audit
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['date_from', 'date_to']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refund_batches');
    }
};
