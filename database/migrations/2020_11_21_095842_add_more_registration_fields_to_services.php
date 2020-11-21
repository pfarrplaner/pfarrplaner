<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreRegistrationFieldsToServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('registration_active')->nullable()->default(1);
            $table->string('exclude_places')->nullable()->default('');
            $table->string('registration_phone')->nullable()->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('registration_active');
            $table->dropColumn('exclude_places');
            $table->dropColumn('registration_phone');
        });
    }
}
