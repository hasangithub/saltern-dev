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
        Schema::create('buyers', function (Blueprint $table) {
            $table->id(); 
            $table->string('business_name')->nullable();
            $table->string('business_registration_number')->nullable();
            $table->string('full_name'); 
            $table->decimal('credit_limit', 10, 2); 
            $table->boolean('service_out')->default(false); 
            $table->string('address_1'); 
            $table->string('phone_number'); 
            $table->string('secondary_phone_number')->nullable(); 
            $table->string('whatsapp_number')->nullable();
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buyers');
    }
};
