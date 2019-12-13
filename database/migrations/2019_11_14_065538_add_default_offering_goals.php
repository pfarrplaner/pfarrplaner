<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultOfferingGoals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function(Blueprint $table) {
            $table->string('default_offering_goal')->nullable()->default('');
            $table->string('default_offering_description')->nullable()->default('');
            $table->string('default_funeral_offering_goal')->nullable()->default('');
            $table->string('default_funeral_offering_description')->nullable()->default('');
            $table->string('default_wedding_offering_goal')->nullable()->default('');
            $table->string('default_wedding_offering_description')->nullable()->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cities', function(Blueprint $table) {
            $table->dropColumn('default_offering_goal');
            $table->dropColumn('default_offering_description');
            $table->dropColumn('default_funeral_offering_goal');
            $table->dropColumn('default_funeral_offering_description');
            $table->dropColumn('default_wedding_offering_goal');
            $table->dropColumn('default_wedding_offering_description');
        });
    }
}
