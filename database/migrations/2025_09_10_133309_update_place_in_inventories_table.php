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
            // Drop the old place column if exists
            if (Schema::hasColumn('inventories', 'place')) {
                $table->dropColumn('place');
            }

            // Add place_id without foreign key
            $table->unsignedBigInteger('place_id')->nullable()->after('warranty_to');
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
