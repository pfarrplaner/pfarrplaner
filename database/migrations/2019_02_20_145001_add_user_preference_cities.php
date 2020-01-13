<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserPreferenceCities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function($table){
            $table->string('office')->nullable()->default('');
            $table->string('address')->nullable()->default('');
            $table->string('phone')->nullable()->default('');
            $table->string('preference_cities')->nullable()->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function($table){
            $table->dropColumn('office');
            $table->dropColumn('address');
            $table->dropColumn('phone');
            $table->dropColumn('preference_cities');
        });
    }
}
