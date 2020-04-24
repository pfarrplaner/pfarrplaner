<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStreamingFieldsToServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('youtube_url')->nullable()->default('');
            $table->string('cc_streaming_url')->nullable()->default('');
            $table->string('offerings_url')->nullable()->default('');
            $table->string('meeting_url')->nullable()->default('');
            $table->string('recording_url')->nullable()->default('');
            $table->string('songsheet')->nullable()->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('youtube_url');
            $table->dropColumn('cc_streaming_url');
            $table->dropColumn('offerings_url');
            $table->dropColumn('meeting_url');
            $table->dropColumn('recording_url');
            $table->dropColumn('songsheet');
        });
    }
}
