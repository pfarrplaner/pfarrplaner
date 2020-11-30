<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatingRowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seating_rows', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('seating_section_id');
            $table->string('title');
            $table->unsignedInteger('seats')->nullable()->default(0);
            $table->unsignedInteger('divides_into')->nullable()->default(0);
            $table->unsignedInteger('spacing')->nullable()->default(0);
            $table->timestamps();

            $table->foreign('seating_section_id')->references('id')->on('seating_sections')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seating_rows');
    }
}
