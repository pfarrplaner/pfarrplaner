<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColorToSeating extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seating_sections', function (Blueprint $table) {
            $table->string('color')->nullable()->default('');
        });
        Schema::table('seating_rows', function (Blueprint $table) {
            $table->string('color')->nullable()->default('');
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
            $table->dropColumn('color');
        });
        Schema::table('seating_rows', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
}
