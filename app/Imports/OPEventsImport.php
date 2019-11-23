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

class OPEventsImport
{

    /** @var City city  */
    protected $city = null;

    public function __construct(City $city)
    {
        $this->city = $city;
    }

    public function getEvents() {
        $url = 'https://backend.online-geplant.de/public/event/'.$this->city->op_customer_token.'/'.$this->city->op_customer_key;
        return json_decode($this->getUrl($url), true);
    }

    public function getUrl($url) {
        $client = new Client();
        $response = $client->request('GET', $url, [
            'headers' => [
                'Origin' => 'https://'.$this->city->op_domain,
                'Referer' => 'https://'.$this->city->op_domain,
            ]
        ]);
        return $response->getBody();
    }


    public function mix($events, Carbon $start, Carbon $end) {
        $myEvents = $this->getEvents();
        foreach ($myEvents['data'] as $myEvent) {
            $myEvent['record_type'] = 'OP_Event';
            $myEvent['start'] = new Carbon($myEvent['startdate'], 'Europe/Berlin');
            if ($myEvent['enddate']) $myEvent['end'] = new Carbon($myEvent['enddate'], 'Europe/Berlin');
            if (($myEvent['start']) <= $end && ((!isset($myEvent['end']) || ($myEvent['end'] >= $start)))) {
                foreach ($myEvent['event_dates'] as $key => $date) {
                    $myEvent['event_dates'][$key]['start'] = new Carbon($date['startdate'], 'Europe/Berlin');
                    if ($date['enddate']) $myEvent['event_dates'][$key]['end'] = new Carbon($date['enddate'], 'Europe/Berlin');
                }
                $myEvent['place'] = $myEvent['locationtitle'] . ($myEvent['locationlocation'] ? ', ' . $myEvent['locationlocation'] : '');
                $events[$myEvent['start']->format('YmdHis')][] = $myEvent;
            }
        }
        ksort ($events);
        return $events;
    }
}