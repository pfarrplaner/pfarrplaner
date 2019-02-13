<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('city_id');
            $table->integer('user_id');
            $table->timestamps();
        });
        Schema::table('users', function($table) {
            $table->integer('isAdmin')->nullable;
            $table->integer('canEditGeneral')->nullable;
            $table->integer('canEditChurch')->nullable;
            $table->string('canEditFields')->nullable;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('city_user');
        Schema::table('users', function($table) {
            $table->dropColumn('isAdmin');
            $table->dropColumn('canEditGeneral');
            $table->dropColumn('canEditChurch');
            $table->dropColumn('canEditFields');
        });
    }
}
