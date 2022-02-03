<?php

use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShowVacationInCalendarToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('show_vacations_with_services')->nullable()->default(false);
            $table->boolean('needs_replacement')->nullable()->default(false);
        });

        User::role('Pfarrer*in')->update(['show_vacations_with_services' => 1, 'needs_replacement' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('show_vacations_with_services');
            $table->dropColumn('needs_replacement');
        });
    }
}
