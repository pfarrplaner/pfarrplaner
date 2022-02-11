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
        Schema::table('funerals', function(Blueprint $table) {
            $table->date('baptism_date')->nullable();
            $table->date('confirmation_date')->nullable();
            $table->string('confirmation_text')->nullable();
            $table->date('wedding_date')->nullable();
            $table->string('wedding_text')->nullable();
            $table->date('dod_spouse')->nullable();
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
            $table->dropColumn('baptism_date');
            $table->dropColumn('confirmation_date');
            $table->dropColumn('confirmation_text');
            $table->dropColumn('wedding_date');
            $table->dropColumn('edding_text');
            $table->dropColumn('dod_spouse');
        });
    }
};
