<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBaptismsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('baptisms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_id');
            $table->text('candidate_name');
            $table->text('candidate_address');
            $table->text('candidate_zip');
            $table->text('candidate_city');
            $table->text('candidate_email');
            $table->text('candidate_phone');
            $table->string('first_contact_with');
            $table->date('first_contact_on')->nullable();
            $table->integer('registered');
            $table->string('registration_document');
            $table->integer('signed');
            $table->date('appointment')->nullable();
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
        Schema::dropIfExists('baptisms');
    }
}
