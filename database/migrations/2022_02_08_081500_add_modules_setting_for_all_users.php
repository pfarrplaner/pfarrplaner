<?php

use App\UI\Modules\Modules;
use App\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (User::where('password', '!=', '')->get() as $user) {
            $user->setSetting('modules', Modules::defaultConfig());
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (User::where('password', '!=', '')->get() as $user) {
            $user->forgetSetting('modules');
        }
    }
};
