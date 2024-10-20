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
            $table->string('full_name'); // Full Name
            $table->date('dob'); // Date of Birth
            $table->string('nic'); // NIC
            $table->string('address'); // Address
            $table->string('mobile_no'); // Mobile Number
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
