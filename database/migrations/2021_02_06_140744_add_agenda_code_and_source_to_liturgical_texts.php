<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAgendaCodeAndSourceToLiturgicalTexts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('liturgical_texts', function (Blueprint $table) {
            $table->string('agenda_code')->nullable()->default('');
            $table->string('needs_replacement')->nullable()->default('');
            $table->string('source')->nullable()->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('liturgical_texts', function (Blueprint $table) {
            $table->dropColumn('agenda_code');
            $table->dropColumn('needs_replacement');
            $table->dropColumn('source');
        });
    }
}
