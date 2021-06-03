<?php

use App\Baptism;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrateBaptismRegistrationForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $baptisms = Baptism::whereHas('attachments', function ($query) {
            $query->where('title', '!=', 'Anmeldeformular');
        })->get();
        foreach ($baptisms as $baptism) {
            foreach ($baptism->attachments as $attachment) {
                $attachment->update(['title' => 'Anmeldeformular']);
            }
        }
        Schema::table('baptisms', function(Blueprint $table) {
            $table->dropColumn('registration_document');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
