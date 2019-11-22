<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOpFieldsToCity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->string('op_domain')->nullable()->default('');
            $table->string('op_customer_key')->nullable()->default('');
            $table->string('op_customer_token')->nullable()->default('');
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
            $table->dropColumn('op_domain');
            $table->dropColumn('op_customer_key');
            $table->dropColumn('op_customer_token');
        });
    }
}
