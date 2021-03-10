<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreYoutubeFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table){
            $table->boolean('youtube_self_declared_for_children')->nullable()->default(false);
        });
        Schema::table('services', function (Blueprint $table){
            $table->text('youtube_prefix_description')->nullable();
            $table->text('youtube_postfix_description')->nullable();
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
            $table->dropColumn('youtube_self_declared_for_children');
        });
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('youtube_prefix_description');
            $table->dropColumn('youtube_postfix_description');
        });
    }
}
