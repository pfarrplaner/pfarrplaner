<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatingSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'seating_sections',
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedInteger('location_id');
                $table->string('seating_model')->default('standard');
                $table->string('title');
                $table->timestamps();

                $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seating_sections');
    }
}
