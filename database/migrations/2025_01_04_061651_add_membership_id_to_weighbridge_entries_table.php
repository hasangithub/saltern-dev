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
        Schema::table('weighbridge_entries', function (Blueprint $table) {
            $table->unsignedBigInteger('membership_id')->nullable()->after('owner_id');
            $table->foreign('membership_id')->references('id')->on('memberships')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weighbridge_entries', function (Blueprint $table) {
            //
        });
    }
};
