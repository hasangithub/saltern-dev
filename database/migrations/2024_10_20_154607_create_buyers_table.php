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
            $table->id(); // Auto-incrementing primary key
            $table->string('code')->unique(); // Code (unique identifier)
            $table->string('name'); // Name
            $table->decimal('credit_limit', 10, 2); // Credit Limit
            $table->boolean('service_out')->default(false); // Service Out (boolean, default false)
            $table->string('address_1'); // Address 1
            $table->string('address_2')->nullable(); // Address 2 (optional)
            $table->string('phone_no'); // Phone No
            $table->timestamps(); // Created at and updated at
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
