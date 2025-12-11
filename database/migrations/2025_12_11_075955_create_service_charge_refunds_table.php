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
        Schema::create('service_charge_refunds', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('membership_id'); // no FK
            $table->decimal('total_service_charge', 10, 2);
            $table->decimal('refund_amount', 10, 2);

            $table->date('from_date');
            $table->date('to_date');

            // Only voucher_id is used
            $table->unsignedBigInteger('voucher_id')->nullable();

            $table->unsignedBigInteger('created_by'); // staff user who created refund

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_charge_refunds');
    }
};
