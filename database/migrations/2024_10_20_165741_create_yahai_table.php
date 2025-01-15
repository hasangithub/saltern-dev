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
            $table->id(); 
            $table->string('name'); 
            $table->unsignedBigInteger('side_id');
            $table->timestamps();
        });

        // Predefine Yahai names
        DB::table('yahai')->insert([
            ['name' => 'Yahai 1', 'side_id' => 1],
            ['name' => 'Yahai 2', 'side_id' => 1],
            ['name' => 'Yahai 3', 'side_id' => 1],
            ['name' => 'Yahai 4', 'side_id' => 1],
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
