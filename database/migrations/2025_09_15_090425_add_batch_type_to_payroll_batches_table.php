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
        Schema::table('payroll_batches', function (Blueprint $table) {
            $table->dropUnique('payroll_batches_pay_period_unique');
            $table->enum('batch_type', ['permanent', 'first_half', 'second_half'])
                  ->after('pay_period')
                  ->nullable();
                  $table->unique(['pay_period', 'batch_type'], 'pay_period_batch_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_batches', function (Blueprint $table) {
            //
        });
    }
};
