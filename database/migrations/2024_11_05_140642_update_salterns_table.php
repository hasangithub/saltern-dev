<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSalternsTable extends Migration
{
    public function up(): void
    {
        Schema::table('salterns', function (Blueprint $table) {
            // Drop owner_id column
            $table->dropColumn('owner_id');
            // Drop yahai_id temporarily if it exists, so we can reposition it
            $table->dropColumn('yahai_id');
        });

        Schema::table('salterns', function (Blueprint $table) {
            // Add yahai_id next to id, with foreign key constraint
            $table->unsignedBigInteger('yahai_id')->after('id');
            $table->foreign('yahai_id')->references('id')->on('yahai')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        
    }
}
