<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RefactorLiturgyItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('liturgy_items', function(Blueprint $table){
            $table->dropColumn('data_id');
            $table->text('serialized_data')->nullable();
            $table->unsignedBigInteger('sortable')->nullable()->default(0);
        });

        Schema::drop('liturgy_items_freetext');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('liturgy_items', function(Blueprint $table){
            $table->unsignedBigInteger('data_id');
            $table->dropColumn('serialized_data');
            $table->dropColumn('sortable');
        });
    }
}
