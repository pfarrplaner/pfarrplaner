<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotationFieldsToSongs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->string('key')->nullable()->default('');
            $table->string('measure')->nullable()->default('');
            $table->string('note_length')->nullable()->default('');
            $table->text('prolog')->nullable();
            $table->text('notation')->nullable();
            $table->text('refrain_notation')->nullable();
            $table->text('refrain_text_notation')->nullable();
        });
        Schema::table('song_verses', function (Blueprint $table) {
            $table->text('notation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn('key');
            $table->dropColumn('measure');
            $table->dropColumn('note_length');
            $table->dropColumn('prolog');
            $table->dropColumn('notation');
            $table->dropColumn('refrain_notation');
            $table->dropColumn('refrain_text_notation');
        });
        Schema::table('song_verses', function (Blueprint $table) {
            $table->dropColumn('notation');
        });
    }
}
