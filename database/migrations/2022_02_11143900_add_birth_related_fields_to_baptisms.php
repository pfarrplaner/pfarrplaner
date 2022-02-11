<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('baptisms', function(Blueprint $table) {
            $table->date('dob')->nullable();
            $table->text('birth_place')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('baptismis', function (Blueprint $table) {
            $table->dropColumn('dob');
            $table->dropColumn('birth_place');
        });
    }
};
