<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSongsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('refrain')->nullable();
            $table->text('copyrights')->nullable();
            $table->string('songbook')->nullable()->default('');
            $table->string('songbook_abbreviation')->nullable()->default('');
            $table->string('reference')->nullable()->default('');
            $table->timestamps();
        });
        Schema::create('song_verses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('song_id');
            $table->string('number');
            $table->text('text');
            $table->boolean('refrain_before')->nullable()->default(false);
            $table->boolean('refrain_after')->nullable()->default(false);
            $table->timestamps();

            $table->foreign('song_id')->references('id')->on('songs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('songs');
        Schema::drop('song_verses');
    }
}
