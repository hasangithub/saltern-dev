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
        Schema::create('ownership_history', function (Blueprint $table) {
            $table->id(); // BIGINT
            $table->unsignedBigInteger('saltern_id'); // BIGINT
            $table->unsignedBigInteger('owner_id'); // BIGINT
            $table->date('acquired_date'); // DATE
            $table->timestamps(); // created_at and updated_at

             // Define foreign key constraints
             $table->foreign('saltern_id')->references('id')->on('salterns')->onDelete('cascade');
             $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ownership_history');
    }
};
