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
        Schema::table('staff_loans', function (Blueprint $table) {
            $table->unsignedBigInteger('voucher_id')->nullable()->after('status');
            $table->tinyInteger('is_migrated')->default(0)->after('voucher_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_loans', function (Blueprint $table) {
            //
        });
    }
};
