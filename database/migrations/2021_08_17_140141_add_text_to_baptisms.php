<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTextToBaptisms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('baptisms', function (Blueprint $table) {
            $table->text('text')->nullable();
            $table->text('notes')->nullable();
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
            $table->dropColumn('text');
        });
        Schema::table('baptisms', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
}
