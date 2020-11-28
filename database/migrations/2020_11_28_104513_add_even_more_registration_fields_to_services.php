<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEvenMoreRegistrationFieldsToServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'services',
            function (Blueprint $table) {
                $table->dateTime('registration_online_start')->nullable();
                $table->dateTime('registration_online_end')->nullable();
                $table->unsignedInteger('registration_max')->nullable();
                $table->string('reserved_places')->nullable();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'services',
            function (Blueprint $table) {
                $table->dropColumn('registration_online_start');
                $table->dropColumn('registration_online_end');
                $table->dropColumn('registration_max');
                $table->dropColumn('reserved_places');
            }
        );
    }
}
