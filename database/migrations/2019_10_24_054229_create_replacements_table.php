<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReplacementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replacements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('absence_id');
            $table->date('from');
            $table->date('to');
            $table->timestamps();
        });
        Schema::create('replacement_user', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('replacement_id');
            $table->integer('user_id');
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
        Schema::dropIfExists('replacements');
        Schema::dropIfExists('replacement_user');
    }
}
