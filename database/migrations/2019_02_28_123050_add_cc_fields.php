<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCcFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('locations', function($table) {
            $table->string('cc_default_location');
        });
        Schema::table('services', function($table) {
            $table->string('others')->nullable();
            $table->integer('cc')->nullable();
            $table->string('cc_location')->nullable();
            $table->string('cc_lesson')->nullable();
            $table->string('cc_staff')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('locations', function($table) {
            $table->dropColumn('cc_default_location');
        });
        Schema::table('services', function($table) {
            $table->dropColumn('others');
            $table->dropColumn('cc');
            $table->dropColumn('cc_location');
            $table->dropColumn('cc_lesson');
            $table->dropColumn('cc_staff');
        });
    }
}
