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
        Schema::table('receipts', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_method_id')->nullable()->after('buyer_id');
            $table->unsignedBigInteger('bank_sub_ledger_id')->nullable()->after('payment_method_id');
            $table->string('cheque_no')->nullable()->after('bank_sub_ledger_id');
            $table->date('cheque_date')->nullable()->after('cheque_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            //
        });
    }
};
