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
            $table->integer('bags_count')->nullable(); // Number of bags (calculated as net weight / 50)
            $table->float('bag_price')->default(50); // Price per bag (default Rs. 50)
            $table->float('total_amount')->nullable(); 
            $table->date('transaction_date'); 
            $table->foreignId('membership_id')->constrained()->onDelete('cascade'); 
            $table->foreignId('owner_id')->constrained()->onDelete('cascade'); 
            $table->foreignId('buyer_id')->constrained()->onDelete('cascade'); 
            $table->enum('status', ['pending', 'completed', 'canceled'])->default('pending');
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
