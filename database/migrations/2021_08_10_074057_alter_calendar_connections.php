<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCalendarConnections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calendar_connections', function (Blueprint $table) {
            $table->dropColumn('credentials');
        });
        Schema::table('calendar_connections', function (Blueprint $table) {
            $table->dropColumn('connection_type');
            $table->text('credentials1')->nullable();
            $table->text('credentials2')->nullable();
            $table->text('connection_string')->nullable();
            $table->boolean('include_hidden')->default(0);
            $table->boolean('include_alternate')->default(0);
        });

        Schema::create('calendar_connection_city', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('calendar_connection_id');
            $table->unsignedInteger('city_id');
            $table->unsignedInteger('connection_type');

            $table->foreign('calendar_connection_id')->references('id')->on('calendar_connections')
                ->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')
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
        Schema::table('calendar_connections', function (Blueprint $table) {
            $table->text('credentials');
            $table->unsignedInteger('connection_type');
        });
        Schema::table('calendar_connections', function (Blueprint $table) {
            $table->dropColumn('credentials1');
        });
        Schema::table('calendar_connections', function (Blueprint $table) {
            $table->dropColumn('credentials2');
        });
        Schema::table('calendar_connections', function (Blueprint $table) {
            $table->dropColumn('connection_string');
        });

        Schema::drop('calendar_connection_city');
    }
}
