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
            $table->time('first_weight_time')->nullable()->after('initial_weight');
            $table->time('second_weight_time')->nullable()->after('tare_weight');
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
