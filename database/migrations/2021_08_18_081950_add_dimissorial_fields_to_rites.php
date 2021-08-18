<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDimissorialFieldsToRites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('baptisms', function (Blueprint $table) {
            $table->boolean('needs_dimissorial')->nullable()->default(false);
            $table->string('dimissorial_issuer')->nullable();
            $table->date('dimissorial_requested')->nullable();
            $table->date('dimissorial_received')->nullable();
        });
        Schema::table('funerals', function (Blueprint $table) {
            $table->boolean('needs_dimissorial')->nullable()->default(false);
            $table->string('dimissorial_issuer')->nullable();
            $table->date('dimissorial_requested')->nullable();
            $table->date('dimissorial_received')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('baptisms', function (Blueprint $table) {
            $table->dropColumn(['needs_dimissorial', 'dimissorial_issuer', 'dimissorial_requested', 'dimissorial_received']);
        });
        Schema::table('funerals', function (Blueprint $table) {
            $table->dropColumn(['needs_dimissorial', 'dimissorial_issuer', 'dimissorial_requested', 'dimissorial_received']);
        });
    }
}
