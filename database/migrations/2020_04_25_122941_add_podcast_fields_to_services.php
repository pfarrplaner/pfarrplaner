<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPodcastFieldsToServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('external_url')->default('')->nullable();
            $table->string('sermon_title')->default('')->nullable();
            $table->string('sermon_reference')->default('')->nullable();
            $table->string('sermon_image')->default('')->nullable();
            $table->text('sermon_description')->nullable();
        });
        Schema::table('cities', function (Blueprint $table) {
            $table->string('podcast_title')->default('')->nullable();
            $table->string('podcast_logo')->default('')->nullable();
            $table->string('sermon_default_image')->default('')->nullable();
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
            $table->dropColumn('external_url');
            $table->dropColumn('sermon_title');
            $table->dropColumn('sermon_reference');
            $table->string('sermon_image')->default('')->nullable();
            $table->dropColumn('sermon_description');
        });
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('podcast_title');
            $table->dropColumn('podcast_logo');
            $table->dropColumn('sermon_default_image');
        });
    }
}
