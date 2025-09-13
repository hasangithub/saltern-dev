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
        Schema::table('staff_loan_repayments', function (Blueprint $table) {
            $table->unsignedBigInteger('payroll_id')->nullable()->after('staff_loan_id');
            $table->string('status')->default('pending')->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_loan_repayments', function (Blueprint $table) {
            //
        });
    }
};
