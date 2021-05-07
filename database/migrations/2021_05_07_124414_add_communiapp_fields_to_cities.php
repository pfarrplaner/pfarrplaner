<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommuniappFieldsToCities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->string('communiapp_url')->nullable()->default('');
            $table->text('communiapp_token')->nullable();
            $table->unsignedBigInteger('communiapp_default_group_id')->nullable();
            $table->boolean('communiapp_use_outlook')->nullable();
            $table->boolean('communiapp_use_op')->nullable();
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
            $table->dropColumn(['communiapp_url', 'communiapp_token', 'communiapp_default_group_id', 'communiapp_use_outlook', 'communiapp_use_op']);
        });
    }
}
