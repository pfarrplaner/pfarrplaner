<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiteDoneFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('baptisms', function (Blueprint $table) {
            $table->boolean('done')->nullable()->default(false);
        });
        Schema::table('funerals', function (Blueprint $table) {
            $table->boolean('done')->nullable()->default(false);
        });
        Schema::table('weddings', function (Blueprint $table) {
            $table->boolean('done')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('baptisms', function(Blueprint $table) {
            $table->dropColumn('done');
        });
        Schema::table('funerals', function(Blueprint $table) {
            $table->dropColumn('done');
        });
        Schema::table('weddings', function(Blueprint $table) {
            $table->dropColumn('done');
        });
    }
}
