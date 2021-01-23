<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBasicLiturgyTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('liturgy_blocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('service_id');
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->timestamps();
        });

        Schema::create('liturgy_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->unsignedBigInteger('liturgy_block_id');
            $table->unsignedBigInteger('data_id');
            $table->string('data_type');
            $table->timestamps();
        });

        Schema::create('liturgy_items_freetext', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('description')->nullable();
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
        Schema::drop('liturgy_blocks');
    }
}
