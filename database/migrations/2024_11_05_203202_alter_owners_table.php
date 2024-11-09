<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOwnersTable extends Migration
{
    public function up(): void
    {
        Schema::table('owners', function (Blueprint $table) {
            $table->dropColumn(['mobile_no', 'address', 'dob', 'nic']);
            $table->string('gender')->after('full_name'); // gender after full_name
            $table->string('civil_status')->after('gender'); // civil_status after gender
            $table->string('phone_number')->nullable()->after('civil_status'); // phone_number
            $table->string('secondary_phone_number')->nullable()->after('phone_number'); // secondary_phone_number
            $table->string('email')->nullable()->after('secondary_phone_number'); // email
            $table->text('address_line_1')->nullable()->after('email'); // address_line_1
            $table->text('address_line_2')->nullable()->after('address_line_1'); // address_line_2
            $table->date('date_of_birth')->nullable()->after('civil_status'); // date_of_birth
            $table->string('nic')->after('date_of_birth');
            $table->string('profile_picture')->nullable()->after('address_line_2');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('owners');
    }
}
