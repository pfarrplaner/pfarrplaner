<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsForAnnouncements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
           $table->string('public_events_calendar_url')->nullable()->default('');
        });
        Schema::table('locations', function(Blueprint $table) {
            $table->string('at_text')->nullable()->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('public_events_calendar_url');
        });
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('at_text');
        });
    }
}
