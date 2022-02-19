<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_service', function(Blueprint $table) {
            $table->id();
            $table->unsignedInteger('city_id');
            $table->unsignedInteger('service_id');

            $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();
            $table->foreign('service_id')->references('id')->on('services')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('city_service');
    }
};
