<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStoryFieldsToFunerals extends Migration
{
    protected $fields = [
        'spouse',
        'parents',
        'children',
        'further_family',
        'baptism',
        'confirmation',
        'undertaker',
        'eulogies',
        'notes',
        'announcements',
        'childhood',
        'profession',
        'family',
        'further_life',
        'faith',
        'events',
        'character',
        'death',
        'life',
        'attending',
        'quotes',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'funerals',
            function (Blueprint $table) {
                foreach ($this->fields as $field) {
                    $table->text($field)->nullable();
                }
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'funerals',
            function (Blueprint $table) {
                foreach ($this->fields as $field) {
                    $table->dropColumn($field);
                }
            }
        );
    }
}
