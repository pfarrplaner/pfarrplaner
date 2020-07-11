<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGoogleAuthToCities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->string('google_auth_code')->default('')->nullable();
            $table->string('google_access_token')->default('')->nullable();
            $table->string('google_refresh_token')->default('')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('google_auth_code');
            $table->dropColumn('google_access_token');
            $table->dropColumn('google_refresh_token');
        });
    }
}
