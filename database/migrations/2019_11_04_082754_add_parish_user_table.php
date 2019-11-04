<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParishUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parish_user', function(Blueprint $table){
            $table->increments('id');
            $table->integer('parish_id');
            $table->integer('user_id');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('image')->nullable()->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parish_user');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
}
