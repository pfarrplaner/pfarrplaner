<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMorePodcastFieldsToCities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->string('homepage')->nullable()->default('');
            $table->string('podcast_owner_name')->nullable()->default('');
            $table->string('podcast_owner_email')->nullable()->default('');
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
            $table->dropColumn('homepage');
            $table->dropColumn('podcast_owner_name');
            $table->dropColumn('podcast_owner_email');
        });
    }
}
