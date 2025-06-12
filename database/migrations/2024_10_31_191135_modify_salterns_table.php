<?php

use App\Models\Owner;
use App\Models\Yahai;
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
        Schema::table('salterns', function (Blueprint $table) {
            // Add foriegn keys
            Schema::table('salterns', function (Blueprint $table) {
                $table->foreignIdFor(Yahai::class);
                $table->foreignIdFor(Owner::class);
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salterns', function (Blueprint $table) {
            //
        });
    }
};
