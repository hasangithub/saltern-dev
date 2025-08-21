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
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('basic_salary', 12, 2)->default(0)->after('employee_id'); 
            $table->decimal('overtime_hours', 8, 2)->default(0)->after('basic_salary');
            $table->decimal('overtime_amount', 12, 2)->default(0)->after('overtime_hours');
            $table->decimal('no_pay', 12, 2)->default(0)->after('overtime_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            //
        });
    }
};
