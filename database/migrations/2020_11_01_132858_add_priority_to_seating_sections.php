<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriorityToSeatingSections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seating_sections', function (Blueprint $table) {
            $table->unsignedInteger('priority')->nullable()->default(100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seating_sections', function (Blueprint $table) {
            $table->dropColumn('priority');
        });
    }
}
