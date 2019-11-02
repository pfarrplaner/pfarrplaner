<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStreetRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('street_ranges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parish_id');
            $table->string('name');
            $table->integer('odd_start');
            $table->integer('odd_end');
            $table->integer('even_start');
            $table->integer('even_end');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('street_ranges');
    }
}
