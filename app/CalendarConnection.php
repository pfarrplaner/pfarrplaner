<?php

namespace App;

use App\Calendars\SyncEngines\SyncEngines;
use App\Jobs\SyncSingleServiceToCalendarConnection;
use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CalendarConnection extends Model
{
    use HasEncryptedAttributes;

    public const CONNECTION_TYPE_OWN = 1;
    public const CONNECTION_TYPE_ALL = 2;

    protected $fillable = ['user_id', 'title', 'credentials1', 'credentials2', 'connection_string', 'include_hidden', 'include_alternate'];

    protected $encrypted = ['credentials1', 'credentials2', 'connection_string'];
    protected $with = ['user', 'cities'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cities()
    {
        return $this->belongsToMany(City::class)->withPivot('connection_type');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entries()
    {
        return $this->hasMany(CalendarConnectionEntry::class);
    }

    /**
     * Get all services for a city that should be synced in a full sync
     * @param City $city
     * @param false $countOnly
     * @return \Illuminate\Support\Collection|int
     */
    public function getSyncableServicesForCity(City $city, $countOnly = false)
    {
        switch ($city->pivot->connection_type) {
            case 0:
                $cityServices = new \Illuminate\Support\Collection();
                return ($countOnly ? 0 : $cityServices);
                break;
            case self::CONNECTION_TYPE_ALL:
                $cityServices = Service::inCity($city);
                break;
            case self::CONNECTION_TYPE_OWN:
                $cityServices = Service::inCity($city)->userParticipates($this->user);
        }

        // exclude hidden service if necessary
        if (!$this->include_hidden) $cityServices->notHidden();

        return $countOnly ? $cityServices->count() : $cityServices->get();
    }

    /**
     * Get the SyncEngine for this connection
     * @return Calendars\SyncEngines\AbstractSyncEngine|null
     */
    public function getSyncEngine()
    {
        $syncEngine = SyncEngines::get($this);
        if (!$syncEngine) Log::error('No sync engine found for CalendarConnection #'.$this->id);
        return $syncEngine;
    }

    /**
     * Sync all relevant services to external calendar
     */
    public function syncEntireCalendar()
    {
        // create new entries
        $services = new Collection();
        foreach ($this->cities as $city) {
            $services = $services->merge($this->getSyncableServicesForCity($city));
        }

        Log::debug('Syncing entire calendar for CalendarConnection #'.$this->id);
        Log::debug('Dispatching ' . count($services) . ' sync jobs');
        foreach ($services as $service) {
            SyncSingleServiceToCalendarConnection::dispatch($this, $service);
        }
    }

    /**
     * Get all CalendarConnections that should be concerned with a service
     * @param Service $service
     * @return mixed
     */
    public static function getConnectionsForService(Service $service)
    {
        // get connections requiring all services of this city
        $calendarConnections = CalendarConnection::whereHas(
            'cities',
            function ($query) use ($service) {
                $query->where('cities.id', $service->city->id)
                    ->where('calendar_connection_city.connection_type', CalendarConnection::CONNECTION_TYPE_ALL);
            }
        )->get();

        // get connections requiring services for a specific participant
        foreach ($service->participants as $participant) {
            $calendarConnections = $calendarConnections->merge(
                CalendarConnection::whereHas(
                    'cities',
                    function ($query) use ($service) {
                        $query->where('cities.id', $service->city->id)
                            ->where(
                                'calendar_connection_city.connection_type',
                                CalendarConnection::CONNECTION_TYPE_OWN
                            );
                    }
                )->where('user_id', $participant->id)->get()
            );
        }

        return $calendarConnections;
    }

}
