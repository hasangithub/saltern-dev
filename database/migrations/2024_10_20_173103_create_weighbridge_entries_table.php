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
            $table->string('vehicle_id'); // Vehicle ID
            $table->decimal('initial_weight', 10, 2); // Initial weight
            $table->decimal('tare_weight', 10, 2)->nullable(); // Tare weight
            $table->date('transaction_date'); // Transaction date
            $table->foreignId('owner_id')->constrained()->onDelete('cascade'); // Owner reference
            $table->foreignId('buyer_id')->constrained()->onDelete('cascade'); // Buyer reference
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
