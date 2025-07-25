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
            $table->string('name_with_initial')->nullable();
            $table->string('nic')->unique()->nullable(); 
            $table->string('phone_number')->nullable(); 
            $table->enum('relationship', \App\Enums\RelationshipType::values())->nullable();
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('representatives');
    }
}
