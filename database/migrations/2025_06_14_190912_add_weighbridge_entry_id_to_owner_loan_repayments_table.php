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
        Schema::table('owner_loan_repayments', function (Blueprint $table) {
            $table->foreignId('weighbridge_entry_id')
                  ->nullable()
                  ->after('buyer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('owner_loan_repayments', function (Blueprint $table) {
            //
        });
    }
};
