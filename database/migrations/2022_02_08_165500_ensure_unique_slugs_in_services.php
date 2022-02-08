<?php

use App\Service;
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
        foreach (Service::all() as $service) {
            $service->update(['slug' => $service->createSlug()]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
