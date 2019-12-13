<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbsencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absences', function (Blueprint $table) {
            $table->increments('id');
            $table->date('from');
            $table->date('to');
            $table->integer('user_id');
            $table->integer('replacement')->nullable();
            $table->text('reason');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('manage_absences')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('absences');
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('manage_absences');
        });
    }
}
