<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProcessedColumnToRites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('baptisms', function (Blueprint $table) {
            $table->boolean('processed')->nullable()->default(false);
        });
        Schema::table('funerals', function (Blueprint $table) {
            $table->boolean('processed')->nullable()->default(false);
        });
        Schema::table('weddings', function (Blueprint $table) {
            $table->boolean('processed')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('baptism', function (Blueprint $table) {
            $table->dropColumn('processed');
        });
        Schema::table('funerals', function (Blueprint $table) {
            $table->dropColumn('processed');
        });
        Schema::table('weddings', function (Blueprint $table) {
            $table->dropColumn('processed');
        });
    }
}
