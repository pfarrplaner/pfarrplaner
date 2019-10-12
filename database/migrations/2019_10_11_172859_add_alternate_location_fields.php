<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAlternateLocationFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('locations', function(Blueprint $table){
            $table->integer('alternate_location_id')->nullable();
            $table->string('general_location_name')->nullable()->default('');
        });

        Schema::table('services', function(Blueprint $table){
            $table->string('location_description')->nullable()->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('locations', function(Blueprint $table){
            $table->dropColumn('alternate_location_id');
            $table->dropColumn('general_location_name');
        });
        Schema::table('services', function(Blueprint $table){
            $table->dropColumn('location_description');
        });
    }
}
