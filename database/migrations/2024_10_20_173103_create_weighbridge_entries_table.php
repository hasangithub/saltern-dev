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
        Schema::create('weighbridge_entries', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_id'); 
            $table->decimal('initial_weight', 10, 2); // Initial weight
            $table->decimal('tare_weight', 10, 2)->nullable(); // Tare weight
            $table->decimal('net_weight', 10, 2)->nullable(); 
            $table->decimal('bags_count', 10, 2)->nullable(); // Number of bags (calculated as net weight / 50)
            $table->decimal('bag_price', 10, 2)->default(50); // Price per bag (default Rs. 50)
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->date('transaction_date'); 
            $table->foreignId('membership_id')->constrained()->onDelete('cascade'); 
            $table->foreignId('owner_id')->constrained()->onDelete('cascade'); 
            $table->foreignId('buyer_id')->constrained()->onDelete('cascade'); 
            $table->enum('status', ['approved','pending', 'completed', 'canceled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weighbridge_entries');
    }
};
