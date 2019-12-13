<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ExtendCityUserPivot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('city_user', function($table){
            $table->char('permission')->nullable()->default('w');
            $table->integer('sorting')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('city_user', function($table){
            /** @var \Doctrine\DBAL\Schema\Table $table */
            $table->dropColumn('permission');
            $table->dropColumn('sorting');
        });
    }
}
