<?php

namespace App\Jobs;

use App\CalendarConnection;
use App\Calendars\SyncEngines\SyncEngines;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class SyncEntireCalendarConnection
 * @package App\Jobs
 */
class SyncEntireCalendarConnection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $calendarConnectionId = null;

    /**
     * Create a new job instance.
     * This just stores the id of the CalendarConnection.
     *
     * @return void
     */
    public function __construct(CalendarConnection $calendarConnection)
    {
        $this->calendarConnectionId = $calendarConnection->id;
    }

    /**
     * Execute the job:
     * This will reload the CalendarConnection object from the database and execute
     * a full sync with the external calendar.
     *
     * @return void
     */
    public function handle()
    {
        /** @var CalendarConnection $calendarConnection */
        if ($calendarConnection = CalendarConnection::find($this->calendarConnectionId)) {
            Log::debug('SyncEntireCalendarConnection triggered for CalendarConnection #'.$calendarConnection->id);
            $calendarConnection->syncEntireCalendar();
            Log::debug('SyncEntireCalendarConnection completed for CalendarConnection #'.$calendarConnection->id);
        } else {
            Log::error('SyncEntireCalendarConnection could not find CalendarConnection #'.$calendarConnection->id);
        }
    }
}
