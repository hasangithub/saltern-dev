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
        Schema::table('weighbridge_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('weighbridge_entries', 'refund_id')) {
                $table->unsignedBigInteger('refund_id')
                      ->nullable()
                      ->after('is_service_charge_paid');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weighbridge_entries', function (Blueprint $table) {
            //
        });
    }
};
