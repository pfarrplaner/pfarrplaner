<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreStoryFieldsToFunerals extends Migration
{
    protected $fields = [
        'spoken_name',
        'professional_life',
        'birth_place',
        'death_place',
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
