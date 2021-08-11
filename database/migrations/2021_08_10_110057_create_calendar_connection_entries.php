<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalendarConnectionEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_connection_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('calendar_connection_id');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->string('alternate_key')->nullable();
            $table->string('foreign_id');
            $table->timestamps();

            $table->foreign('calendar_connection_id')->references('id')->on('calendar_connections')
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
        Schema::dropIfExists('calendar_connection_entries');
    }
}
