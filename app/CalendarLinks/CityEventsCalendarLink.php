<?php
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

class CityEventsCalendarLink extends AbstractCalendarLink
{

    protected $title = 'Kombinierter Veranstaltungskalender';
    protected $description = 'Kalender, neben den Gottesdiensten auch die Veranstaltungen (aus Outlook, Online Planer) einer Kirchengemeinde enthält.';
    protected $viewName = 'events';

    public function setupData() {
        $cities = Auth::user()->cities;
        return compact('cities');
    }

    public function setDataFromRequest(Request $request)
    {
        $request->validate(['city' => 'required']);
        $this->data['city'] = $request->get('city');
    }

    public function getRenderData(Request $request, User $user)
    {

        $city = City::findOrFail($request->get('city'));
        $events = [];

        $services = Service::with(['day', 'location'])
            ->where('city_id', $city->id)
            ->get();


        $start = Carbon::createFromDate(1970,1,1);
        $end = Carbon::createFromDate(2070, 1,1 );

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
