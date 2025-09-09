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
        Schema::table('inventories', function (Blueprint $table) {
            $table->date('date_of_purchase')->nullable()->after('id');

            // Add stock_code after name
            $table->string('stock_code')->nullable()->after('name');

            // Add qty after stock_code
            $table->integer('qty')->nullable()->after('stock_code');

            // Add warranty period from and to after qty
            $table->date('warranty_from')->nullable()->after('qty');
            $table->date('warranty_to')->nullable()->after('warranty_from');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            //
        });
    }
};
