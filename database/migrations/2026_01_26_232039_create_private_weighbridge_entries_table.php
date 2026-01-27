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
        Schema::create('private_weighbridge_entries', function (Blueprint $table) {
            $table->id();

            $table->string('vehicle_id');
            $table->date('transaction_date');

            $table->decimal('first_weight', 10, 2);
            $table->time('first_weight_time');

            $table->decimal('second_weight', 10, 2)->nullable();
            $table->time('second_weight_time')->nullable();

            $table->string('customer_name')->nullable();
            $table->unsignedBigInteger('buyer_id')->nullable();

            $table->decimal('amount', 12, 2)->nullable();

            $table->boolean('is_paid')->default(0);

            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_weighbridge_entries');
    }
};
