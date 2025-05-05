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
        Schema::create('owner_complaints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id'); // Foreign key to owners table
            $table->text('complaint_text')->nullable(); // For text complaints
            $table->string('complaint_voice')->nullable(); // Path to the voice file
            $table->enum('type', ['text', 'voice']); // Type of complaint
            $table->unsignedBigInteger('user_assigned')->nullable(); // Staff assigned to the complaint
            $table->unsignedBigInteger('user_assigned_by')->nullable(); // Staff who assigned the complaint
            $table->string('status')->default('open'); // Complaint status (open, resolved, in_progress, etc.)
            $table->text('reply_text')->nullable(); // Reply to the complaint
            $table->unsignedBigInteger('replied_by')->nullable(); // Staff who replied to the complaint
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owner_complaints');
    }
};
