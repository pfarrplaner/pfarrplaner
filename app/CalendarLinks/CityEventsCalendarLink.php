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
 * Date: 29.10.2019
 * Time: 13:45
 */

namespace App\CalendarLinks;


use App\City;
use App\Imports\EventCalendarImport;
use App\Imports\OPEventsImport;
use App\Service;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class CityEventsCalendarLink
 * @package App\CalendarLinks
 */
class CityEventsCalendarLink extends AbstractCalendarLink
{

    /**
     * @var string
     */
    protected $title = 'Kombinierter Veranstaltungskalender';
    /**
     * @var string
     */
    protected $description = 'Kalender, neben den Gottesdiensten auch die Veranstaltungen (aus Outlook, Online Planer) einer Kirchengemeinde enthält.';
    /**
     * @var string
     */
    protected $viewName = 'events';

    /** @var string[]  */
    protected $needs = ['cities', 'includeHidden'];

    /**
     * @return array
     */
    public function setupData()
    {
        $cities = Auth::user()->cities;
        return compact('cities');
    }

    /**
     * @param Request $request
     */
    public function setDataFromRequest(Request $request)
    {
        $request->validate(['city' => 'required']);
        $this->data['city'] = $request->get('city');
    }

    /**
     * @param Request $request
     * @param User $user
     * @return array|mixed
     */
    public function getRenderData(Request $request, User $user)
    {
        $city = City::findOrFail($request->get('city'));
        $events = [];

        $servicesQuery = Service::with(['location'])
            ->startingFrom(Carbon::now()->subMonth(1))
            ->whereDoesntHave('funerals')
            ->where('city_id', $city->id);

        if (!$request->get('includeHidden', 0)) $servicesQuery->notHidden();
        $services = $servicesQuery->get();

        $start = Carbon::now()->subMonth(1);
        $end = Carbon::createFromDate(2070, 1, 1);

        if (isset($city->public_events_calendar_url)) {
            $calendar = new EventCalendarImport($city->public_events_calendar_url);
            $calendar->timeZone = 'Europe/Berlin';
            $events = $calendar->mix($events, $start, $end, true);
        }

        $events = Service::mix($events, $services, $start, $end);

        if (($city->op_domain != '') && ($city->op_customer_key != '') && ($city->op_customer_token != '')) {
            $op = new OPEventsImport($city);
            $events = $op->mix($events, $start, $end, true);
        }

        return $events;
    }


}
