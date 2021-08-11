<?php

namespace App\Jobs;

use App\CalendarConnection;
use App\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncSingleServiceToCalendarConnections implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $serviceId = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Service $service)
    {
        $this->serviceId = $service->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $service = Service::findOrFail($this->serviceId);
        $calendarConnections = CalendarConnection::getConnectionsForService($service);

        /** @var CalendarConnection $calendarConnection */
        foreach ($calendarConnections as $calendarConnection) {
            $syncEngine = $calendarConnection->getSyncEngine();
            if ($syncEngine) $syncEngine->syncSingleService($service);
        }
    }
}
