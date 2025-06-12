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
        Schema::create('owner_loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('membership_id');
            $table->decimal('requested_amount', 15, 2);
            $table->text('purpose')->nullable(); // Reason for the loan
            $table->decimal('approved_amount', 15, 2)->nullable();
            $table->text('approval_comments')->nullable(); // Comments by staff during approval/rejection
            $table->timestamp('approval_date')->nullable(); // Date of approval
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owner_loans');
    }
};
