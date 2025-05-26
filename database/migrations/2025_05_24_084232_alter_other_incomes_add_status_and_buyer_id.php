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
        Schema::table('other_incomes', function (Blueprint $table) {
            // Add 'status' field next to 'description'
            $table->string('status')->default('pending')->after('description');

            // Add 'buyer_id' field next to 'income_category_id'
            $table->unsignedBigInteger('buyer_id')->nullable()->after('income_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
