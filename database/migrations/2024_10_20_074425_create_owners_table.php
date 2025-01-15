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
        Schema::create('owners', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->string('full_name'); 
            $table->string('name_with_initial')->nullable();     
            $table->string('gender'); 
            $table->string('civil_status')->nullable(); 
            $table->string('phone_number')->nullable(); 
            $table->string('secondary_phone_number')->nullable(); 
            $table->string('whatsapp_number')->nullable();
            $table->string('email')->nullable(); 
            $table->text('address_line_1')->nullable(); 
            $table->date('date_of_birth')->nullable(); 
            $table->string('nic');
            $table->string('profile_picture')->nullable();
            $table->timestamps(); // Created at and updated at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owners');
    }
};
