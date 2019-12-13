<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParishesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parishes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('city_id');
            $table->string('name');
            $table->string('code');
            $table->string('address')->nullable()->default('');
            $table->string('zip')->nullable()->default('');
            $table->string('city')->nullable()->default('');
            $table->string('phone')->nullable()->default('');
            $table->string('email')->nullable()->default('');
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
        Schema::dropIfExists('parishes');
    }
}
