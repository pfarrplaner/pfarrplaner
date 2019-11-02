<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateFieldsToFuneral extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('funerals', function (Blueprint $table) {
            $table->datetime('appointment')->nullable();
            $table->date('dob')->nullable();
            $table->date('dod')->nullable();
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
            $table->dropColumn('appointment');
            $table->dropColumn('dob');
            $table->dropColumn('dod');
        });
    }
}
