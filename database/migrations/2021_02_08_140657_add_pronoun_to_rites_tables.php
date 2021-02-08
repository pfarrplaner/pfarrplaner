<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPronounToRitesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('funerals', function (Blueprint $table) {
            $table->string('pronoun_set')->default('')->nullable();
        });
        Schema::table('baptisms', function (Blueprint $table) {
            $table->string('pronoun_set')->default('')->nullable();
        });
        Schema::table('weddings', function (Blueprint $table) {
            $table->string('pronoun_set1')->default('')->nullable();
            $table->string('pronoun_set2')->default('')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('funerals', function (Blueprint $table) {
            $table->dropColumn('pronoun_set');
        });
        Schema::table('baptisms', function (Blueprint $table) {
            $table->dropColumn('pronoun_set');
        });
        Schema::table('weddings', function (Blueprint $table) {
            $table->dropColumn('pronoun_set1');
            $table->dropColumn('pronoun_set2');
        });
    }
}
