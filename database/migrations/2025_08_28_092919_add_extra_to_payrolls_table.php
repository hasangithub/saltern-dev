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
            $table->integer('mercantile_days')->default(0)->after('no_pay');
            $table->decimal('mercantile_days_amount', 12, 2)->default(0)->after('mercantile_days');

            $table->integer('extra_full_days')->default(0)->after('mercantile_days_amount');
            $table->decimal('extra_full_days_amount', 12, 2)->default(0)->after('extra_full_days');

            $table->integer('extra_half_days')->default(0)->after('extra_full_days_amount');
            $table->decimal('extra_half_days_amount', 12, 2)->default(0)->after('extra_half_days');

            $table->integer('poovarasan_kuda_allowance_150')->default(0)->after('extra_half_days');
            $table->decimal('poovarasan_kuda_allowance_150_amount', 12, 2)->default(0)->after('poovarasan_kuda_allowance_150');
            $table->decimal('labour_amount', 12, 2)->default(0)->after('poovarasan_kuda_allowance_150_amount');
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
