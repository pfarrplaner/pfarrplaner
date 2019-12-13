<?php
/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 22.11.2019
 * Time: 12:05
 */

namespace App\Imports;


use App\City;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class OPEventsImport
{

    /** @var City city */
    protected $city = null;

    public function __construct(City $city)
    {
        $this->city = $city;
    }

    public function getEvents()
    {
        $url = 'https://backend.online-geplant.de/public/event/' . $this->city->op_customer_token . '/' . $this->city->op_customer_key;
        $cacheKey = 'OPEventsImport_' . $url;
        if (Cache::has($cacheKey)) {
            $events = Cache::get($cacheKey);
        } else {
            $events = json_decode($this->getUrl($url), true);
            Cache::put($cacheKey, $events, 15);
        }
        return $events;
    }

    public function getUrl($url)
    {
        $client = new Client();
        $response = $client->request('GET', $url, [
            'headers' => [
                'Origin' => 'https://' . $this->city->op_domain,
                'Referer' => 'https://' . $this->city->op_domain,
            ]
        ]);
        return $response->getBody();
    }


    protected function fixTimeAndDates(&$event)
    {
        $event['start'] = new Carbon($event['startdate'], 'Europe/Berlin');
        if (null !== $event['timestart']) {
            $event['start']->setTimeFromTimeString($event['timestart']);
        }
        if ($event['enddate']) {
            $event['end'] = new Carbon($event['enddate'], 'Europe/Berlin');
        }
        if (null !== $event['timeend']) {
            $event['end']->setTimeFromTimeString($event['timeend']);
        } else {
            unset($event['end']);
        }
        if (isset($event['end']) && ($event['end'] <= $event['start'])) {
            $event['end'] = $event['start']->copy()->addHour(1);
        }
    }

    public function mix($events, Carbon $start, Carbon $end, $removeMatching = false)
    {
        $myEvents = $this->getEvents();
        foreach ($myEvents['data'] as $myEvent) {
            $myEvent['record_type'] = 'OP_Event';
            $this->fixTimeAndDates($myEvent);
            if (($myEvent['start']) <= $end && ((!isset($myEvent['end']) || ($myEvent['end'] >= $start)))) {
                foreach ($myEvent['event_dates'] as $key => $date) {
                    $this->fixTimeAndDates($myEvent['event_dates'][$key]);
                }
                $myEvent['place'] = $myEvent['locationtitle'] . ($myEvent['locationlocation'] ? ', ' . $myEvent['locationlocation'] : '');

                // check for existing events
                if ($removeMatching) {
                    $matches = [];
                    if (isset($events[$myEvent['start']->format('YmdHis')])) {
                        foreach ($events[$myEvent['start']->format('YmdHis')] as $matchKey => $existingEvent) {
                            if ($existingEvent['title'] == $myEvent['title']) {
                                $matches[] = $matchKey;
                            }
                        }
                        foreach ($matches as $match) {
                            unset($events[$myEvent['start']->format('YmdHis')][$match]);
                        }
                    }
                }
                $events[$myEvent['start']->format('YmdHis')][] = $myEvent;
            }


        }

        ksort($events);
        return $events;
    }
}