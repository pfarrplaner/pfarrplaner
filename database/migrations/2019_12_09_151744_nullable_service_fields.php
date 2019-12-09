<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NullableServiceFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->integer('location_id')->nullable()->change();
            $table->time('time')->nullable()->change();
            $table->mediumText('description')->nullable()->default('')->change();
            $table->integer('need_predicant')->nullable()->default(0)->change();
            $table->string('offerings_counter1')->nullable()->default('')->change();
            $table->string('offerings_counter2')->nullable()->default('')->change();
            $table->string('offering_goal')->nullable()->default('')->change();
            $table->string('offering_description')->nullable()->default('')->change();
            $table->string('offering_type')->nullable()->default('')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->integer('location_id')->change();
            $table->time('time')->change();
            $table->mediumText('description')->change();
            $table->integer('need_predicant')->change();
            $table->string('offerings_counter1')->change();
            $table->string('offerings_counter2')->change();
            $table->string('offering_goal')->change();
            $table->string('offering_description')->change();
            $table->string('offering_type')->change();
        });
    }
}
