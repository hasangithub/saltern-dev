<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepresentativesTable extends Migration
{
    public function up(): void
    {
        Schema::create('representatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_id')->constrained('memberships')->onDelete('cascade');
            $table->string('full_name');
            $table->enum('gender', ['male', 'female', 'other']); // Gender field
            $table->enum('civil_status', ['single', 'married', 'divorced', 'widowed']); // Civil status field
            $table->date('date_of_birth'); // Date of birth field
            $table->string('nic')->unique(); // NIC field
            $table->string('phone_number'); // Phone number field
            $table->string('secondary_phone_number')->nullable(); // Secondary phone number field
            $table->string('email')->nullable(); // Email field
            $table->text('address_line_1')->nullable(); // Address line 1
            $table->text('address_line_2')->nullable(); // Address line 2
            $table->string('profile_picture')->nullable(); // Profile picture field
            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('representatives');
    }
}
