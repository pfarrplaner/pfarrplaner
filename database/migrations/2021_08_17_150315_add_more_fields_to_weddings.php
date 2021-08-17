<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreFieldsToWeddings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->date('spouse1_dob')->nullable();
            $table->text('spouse1_address')->nullable();
            $table->text('spouse1_zip')->nullable();
            $table->text('spouse1_city')->nullable();
            $table->boolean('spouse1_needs_dimissorial')->nullable();
            $table->string('spouse1_dimissorial_issuer')->nullable();
            $table->date('spouse1_dimissorial_requested')->nullable();
            $table->date('spouse1_dimissorial_received')->nullable();
            $table->date('spouse2_dob')->nullable();
            $table->text('spouse2_address')->nullable();
            $table->text('spouse2_zip')->nullable();
            $table->text('spouse2_city')->nullable();
            $table->boolean('spouse2_needs_dimissorial')->nullable();
            $table->string('spouse2_dimissorial_issuer')->nullable();
            $table->date('spouse2_dimissorial_requested')->nullable();
            $table->date('spouse2_dimissorial_received')->nullable();
            $table->unsignedSmallInteger('needs_permission')->nullable();
            $table->date('permission_requested')->nullable();
            $table->date('permission_received')->nullable();
            $table->text('notes')->nullable();
            $table->text('music')->nullable();
            $table->text('gift')->nullable();
            $table->text('flowers')->nullable();
            $table->unsignedSmallInteger('docs_format')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->dropColumn([
                                    'notes',
                                    'spouse1_dob',
                                    'spouse1_address',
                                    'spouse1_zip',
                                    'spouse1_city',
                                    'spouse1_needs_dimissorial',
                                    'spouse1_dimissorial_issuer',
                                    'spouse1_dimissorial_requested',
                                    'spouse1_dimissorial_received',
                                    'spouse2_dob',
                                    'spouse2_address',
                                    'spouse2_zip',
                                    'spouse2_city',
                                    'spouse2_needs_dimissorial',
                                    'spouse2_dimissorial_issuer',
                                    'spouse2_dimissorial_requested',
                                    'spouse2_dimissorial_received',
                                    'needs_permission',
                                    'permission_requested',
                                    'permission_received',
                                    'music',
                                    'gift',
                                    'flowers',
                                    'docs_format',
                                ]);
        });
    }
}
