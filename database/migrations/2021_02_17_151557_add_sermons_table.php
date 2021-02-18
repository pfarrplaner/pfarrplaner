<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSermonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sermons', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->nullable()->default('');
            $table->string('subtitle')->nullable()->default('');
            $table->string('reference')->nullable()->default('');
            $table->string('series')->nullable()->default('');
            $table->string('image')->nullable()->default('');
            $table->string('notes_header')->nullable()->default('');
            $table->string('audio_recording')->nullable()->default('');
            $table->string('video_url')->nullable()->default('');
            $table->string('external_url')->nullable()->default('');
            $table->text('summary')->nullable();
            $table->text('text')->nullable();
            $table->text('key_points')->nullable();
            $table->text('questions')->nullable();
            $table->text('literature')->nullable();
            $table->boolean('cc_license')->nullable()->default(false);
            $table->boolean('permit_handouts')->nullable()->default(true);
            $table->timestamps();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->unsignedBigInteger('sermon_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sermons');
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('sermon_id');
        });
    }
}
