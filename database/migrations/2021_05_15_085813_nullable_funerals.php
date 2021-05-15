<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NullableFunerals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('funerals', function (Blueprint $table) {
            $table->text('buried_address')->nullable()->change();
            $table->text('buried_zip')->nullable()->change();
            $table->text('buried_city')->nullable()->change();
            $table->string('text')->nullable()->change();
            $table->string('type')->nullable()->change();
            $table->text('relative_name')->nullable()->change();
            $table->text('relative_address')->nullable()->change();
            $table->text('relative_zip')->nullable()->change();
            $table->text('relative_city')->nullable()->change();
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
            $table->text('buried_address')->nullable(false)->change();
            $table->text('buried_zip')->nullable(false)->change();
            $table->text('buried_city')->nullable(false)->change();
            $table->string('text')->nullable(false)->change();
            $table->string('type')->nullable(false)->change();
            $table->text('relative_name')->nullable(false)->change();
            $table->text('relative_address')->nullable(false)->change();
            $table->text('relative_zip')->nullable(false)->change();
            $table->text('relative_city')->nullable(false)->change();
        });
    }
}
