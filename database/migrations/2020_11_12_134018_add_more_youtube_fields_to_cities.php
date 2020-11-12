<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreYoutubeFieldsToCities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->string('youtube_active_stream_id')->nullable()->default('');
            $table->string('youtube_passive_stream_id')->nullable()->default('');
            $table->boolean('youtube_auto_startstop')->nullable()->default(0);
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
            $table->dropColumn('youtube_active_stream_id');
            $table->dropColumn('youtube_passive_stream_id');
            $table->dropColumn('youtube_auto_startstop');
        });
    }
}
