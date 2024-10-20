<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('yahai', function (Blueprint $table) {
            $table->id(); // ID field
            $table->string('name'); // Name field
            $table->timestamps();
        });

        // Predefine Yahai names
        DB::table('yahai')->insert([
            ['name' => 'Yahai 1'],
            ['name' => 'Yahai 2'],
            ['name' => 'Yahai 3'],
            ['name' => 'Yahai 4'],
            // Add more predefined names as needed
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yahai');
    }
};
