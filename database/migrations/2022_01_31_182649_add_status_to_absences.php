<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToAbsences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('absences', function (Blueprint $table) {
            $table->integer('workflow_status')->nullable()->default(0);
            $table->text('admin_notes')->nullable();
            $table->text('approver_notes')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->dateTime('checked_at')->nullable();
            $table->dateTime('approved_at')->nullable();

            // remove unused fields
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('absences', function (Blueprint $table) {
            $table->dropColumn('workflow_status');
            $table->dropColumn('admin_notes');
            $table->dropColumn('approver_notes');
            $table->dropColumn('admin_id');
            $table->dropColumn('approver_id');
            $table->dropColumn('checked_at');
            $table->dropColumn('approved_at');
        });
    }
}
