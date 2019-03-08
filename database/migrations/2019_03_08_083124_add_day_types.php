<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDayTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_day', function (Blueprint $table) {
            $table->increments('id')->nullable();
            $table->integer('day_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->timestamps();
        });
        Schema::table('days', function($table) {
           $table->integer('day_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('city_day');
        Schema::table('days', function($table) {
            $table->dropColumn('day_type');
        });
    }
}
