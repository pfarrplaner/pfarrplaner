<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbsenceUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absence_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('absence_id');
            $table->integer('user_id');
            $table->timestamps();
        });

        Schema::table('absences', function (Blueprint $table) {
            $table->dropColumn('replacement');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('absence_user');
        Schema::table('absences', function (Blueprint $table) {
           $table->integer('replacement')->nullable();
        });
    }
}
