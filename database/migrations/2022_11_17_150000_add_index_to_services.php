<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->index('slug', 'slug_index');
            $table->index('date', 'date_index');
            $table->index('city_id', 'city_index');
            $table->index(['date','city_id'], 'date_city_index');
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
            $table->dropIndex('slug_index');
            $table->dropIndex('date_index');
            $table->dropIndex('city_index');
            $table->dropIndex('date_city_index');
        });
    }
};
