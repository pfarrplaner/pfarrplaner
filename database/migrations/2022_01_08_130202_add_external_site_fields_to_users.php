<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExternalSiteFieldsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('own_website')->nullable();
            $table->string('own_podcast_title')->nullable();
            $table->string('own_podcast_url')->nullable();
            $table->boolean('own_podcast_spotify')->nullable()->default(false);
            $table->boolean('own_podcast_itunes')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('own_website');
            $table->dropColumn('own_podcast_title');
            $table->dropColumn('own_podcast_url');
            $table->dropColumn('own_podcast_spotify');
            $table->dropColumn('own_podcast_itunes');
        });
    }
}
