<?php

namespace App\Jobs;

use App\CalendarConnection;
use App\Calendars\SyncEngines\SyncEngines;
use App\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncSingleServiceToCalendarConnection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Service */
    protected $service;

    /** @var CalendarConnection */
    protected $calendarConnection;

    /**
     * Create a new job instance
     * @param CalendarConnection $calendarConnection
     * @param Service $service
     */
    public function __construct(CalendarConnection $calendarConnection, Service $service)
    {
        $this->calendarConnection = $calendarConnection;
        $this->service = $service;
    }

    /**
     * Execute the job.
     * Gets the appropriate SyncEngine and syncs the service
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('Executing sync job for service #'.$this->service->id.' on CalendarConnection #'.$this->calendarConnection->id);
        $syncEngine = $this->calendarConnection->getSyncEngine();
        if ($syncEngine) {
            Log::debug('Sync engine found: '.get_class($syncEngine));
            $syncEngine->syncSingleService($this->service);
        } else {
            Log::error('No sync engine found for CalendarConnection #'.$this->calendarConnection->id);
        }
    }
}
