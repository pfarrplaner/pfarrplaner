<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/pfarrplaner/pfarrplaner
 * @version git: $Id$
 *
 * Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
 *
 * Pfarrplaner is based on the Laravel framework (https://laravel.com).
 * This file may contain code created by Laravel's scaffolding functions.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 22.11.2019
 * Time: 12:05
 */

namespace App\Imports;


use App\City;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Psr\Http\Message\StreamInterface;

/**
 * Class OPEventsImport
 * @package App\Imports
 */
class OPEventsImport
{

    /** @var City city */
    protected $city = null;

    /**
     * OPEventsImport constructor.
     * @param City $city
     */
    public function __construct(City $city)
    {
        $this->city = $city;
    }

    /**
     * @param $events
     * @param Carbon $start
     * @param Carbon $end
     * @param bool $removeMatching
     * @return mixed
     */
    public function mix($events, Carbon $start, Carbon $end, $removeMatching = false)
    {
        if (empty($this->city->op_customer_token.$this->city->op_customer_key)) return $events;

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

    /**
     * @return mixed
     * @throws GuzzleException
     */
    public function getEvents()
    {
        if (empty($this->city->op_customer_token.$this->city->op_customer_key)) return [];
        $url = 'https://backend.online-geplant.de/public/event/' . $this->city->op_customer_token . '/' . $this->city->op_customer_key;
        $cacheKey = 'OPEventsImport_' . $url;
        if (Cache::has($cacheKey)) {
            $events = Cache::get($cacheKey);
        } else {
            $events = json_decode($this->getUrl($url), true);
            Cache::put($cacheKey, $events, 900);
        }
        return $events;
    }

    /**
     * @param $url
     * @return StreamInterface
     * @throws GuzzleException
     */
    public function getUrl($url)
    {
        $client = new Client();
        $response = $client->request(
            'GET',
            $url,
            [
                'headers' => [
                    'Origin' => 'https://' . $this->city->op_domain,
                    'Referer' => 'https://' . $this->city->op_domain,
                ]
            ]
        );
        return $response->getBody();
    }

    /**
     * @param $event
     * @throws Exception
     */
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
}
