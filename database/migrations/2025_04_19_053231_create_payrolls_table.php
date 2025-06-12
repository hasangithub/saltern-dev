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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('month'); // use 2025-04-01 to represent April 2025
            $table->integer('total_days');
            $table->decimal('present_days', 5, 2);
            $table->integer('leave_days');
            $table->integer('half_days');
            $table->integer('no_pay_days');
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('net_salary', 10, 2);
            $table->timestamps();

            $table->unique(['user_id', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
