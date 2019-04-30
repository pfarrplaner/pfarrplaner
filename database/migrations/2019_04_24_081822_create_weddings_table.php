<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeddingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weddings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_id');
            $table->text('spouse1_name');
            $table->text('spouse1_birth_name');
            $table->text('spouse1_email');
            $table->text('spouse1_phone');
            $table->text('spouse2_name');
            $table->text('spouse2_birth_name');
            $table->text('spouse2_email');
            $table->text('spouse2_phone');
            $table->date('appointment')->nullable();
            $table->string('text');
            $table->integer('registered');
            $table->string('registration_document');
            $table->integer('signed');
            $table->integer('docs_ready');
            $table->string('docs_where');
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
        Schema::dropIfExists('weddings');
    }
}
