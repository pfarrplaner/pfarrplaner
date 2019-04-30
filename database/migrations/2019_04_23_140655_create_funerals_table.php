<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFuneralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funerals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_id');
            $table->text('buried_name');
            $table->text('buried_address');
            $table->text('buried_zip');
            $table->text('buried_city');
            $table->string('text');
            $table->date('announcement')->nullable();
            $table->string('type');
            $table->date('wake')->nullable();
            $table->text('relative_name');
            $table->text('relative_address');
            $table->text('relative_zip');
            $table->text('relative_city');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('funerals');
    }
}
