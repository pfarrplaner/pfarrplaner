<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPsalmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('psalms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('intro')->nullable();
            $table->text('text')->nullable();
            $table->text('copyrights')->nullable();
            $table->string('songbook')->nullable()->default('');
            $table->string('songbook_abbreviation')->nullable()->default('');
            $table->string('reference')->nullable()->default('');
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
        Schema::drop('psalms');
    }
}
