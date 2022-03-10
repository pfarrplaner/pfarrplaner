<?php

use App\Service;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function(Blueprint $table) {
            $table->datetime('date')->nullable();
            $table->unsignedInteger('day_id')->nullable()->change();
        });

        foreach (Service::all() as $service) {
            $date = $service->day->date->format('Y-m-d');
            if ($service->time) {
                $date .= ' '.trim($service->time);
            }
            $date = Carbon\Carbon::parse($date, 'Europe/Berlin')->setTimezone('UTC')->setSeconds(0);
            $service->update(['date' => $date]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('date');
        });
    }
};
