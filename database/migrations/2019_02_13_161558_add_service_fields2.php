<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddServiceFields2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function($table){
            $table->integer('baptism')->nullable()->default(0);
            $table->integer('eucharist')->nullable()->default(0);
            $table->string('offerings_counter1')->nullable()->default('');
            $table->string('offerings_counter2')->nullable()->default('');
            $table->string('offering_goal')->nullable()->default('');
            $table->string('offering_description')->nullable()->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function($table) {
            $table->dropColumn('baptism');
            $table->dropColumn('eucharist');
            $table->dropColumn('offerings_counter1');
            $table->dropColumn('offerings_counter2');
            $table->dropColumn('offering_goal');
            $table->dropColumn('offering_description');
        });
    }
}
