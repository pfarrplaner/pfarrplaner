<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUserFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn(['isAdmin', 'canEditGeneral', 'canEditChurch', 'canEditFields']);
            $table->string('first_name')->nullable()->default('');
            $table->string('last_name')->nullable()->default('');
            $table->string('title')->nullable()->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function($table) {
            $table->integer('isAdmin')->nullable;
            $table->integer('canEditGeneral')->nullable;
            $table->integer('canEditChurch')->nullable;
            $table->string('canEditFields')->nullable;
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('title');
        });
    }
}
