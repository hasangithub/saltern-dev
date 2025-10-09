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
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('mercantile_days', 5, 2)->nullable()->change();
            $table->decimal('mercantile_days_amount', 10, 2)->nullable()->change();
            $table->decimal('extra_full_days', 5, 2)->nullable()->change();
            $table->decimal('extra_full_days_amount', 10, 2)->nullable()->change();
            $table->decimal('extra_half_days', 5, 2)->nullable()->change();
            $table->decimal('extra_half_days_amount', 10, 2)->nullable()->change();
            $table->decimal('poovarasan_kuda_allowance_150', 10, 2)->nullable()->change();
            $table->decimal('poovarasan_kuda_allowance_150_amount', 10, 2)->nullable()->change();
            $table->decimal('labour_hours', 5, 2)->nullable()->change();
            $table->decimal('labour_amount', 10, 2)->nullable()->change();
            $table->decimal('epf_employee', 10, 2)->nullable()->change();
            $table->decimal('epf_employer', 10, 2)->nullable()->change();
            $table->decimal('etf', 10, 2)->nullable()->change();
            $table->decimal('gross_earnings', 12, 2)->nullable()->change();
            $table->decimal('total_deductions', 12, 2)->nullable()->change();
            $table->decimal('net_pay', 12, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            //
        });
    }
};
