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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('place'); // yard, office, etc.
            $table->decimal('amount', 10, 2);
            $table->unsignedBigInteger('voucher_id')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['inuse', 'repaired', 'replaced'])->default('inuse');
            $table->unsignedBigInteger('replaced_id')->nullable();
            $table->unsignedBigInteger('created_by'); // user who created
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
