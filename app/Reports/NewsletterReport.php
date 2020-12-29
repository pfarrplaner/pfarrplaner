<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
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

namespace App\Reports;

use App\City;
use App\Day;
use App\Imports\EventCalendarImport;
use App\Imports\OPEventsImport;
use App\Liturgy;
use App\Service;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


/**
 * Class NewsletterReport
 * @package App\Reports
 */
class NewsletterReport extends AbstractWordDocumentReport
{

    /**
     * @var string
     */
    public $title = 'Newsletter';
    /**
     * @var string
     */
    public $group = 'Veröffentlichungen';
    /**
     * @var string
     */
    public $description = 'Gottesdienstliste für den Newsletter';

    /**
     * @return Application|Factory|View
     */
    public function setup()
    {
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first();
        $cities = Auth::user()->cities;
        return $this->renderSetupView(['cities' => $cities]);
    }


    /**
     * @param Request $request
     * @return Application|Factory|View|string
     */
    public function render(Request $request)
    {
        $request->validate(
            [
                'city' => 'required|int|exists:cities,id',
                'start' => 'required|date|date_format:d.m.Y',
                'end' => 'required|date|date_format:d.m.Y',
                'mixOP' => 'nullable|int',
                'mixOutlook' => 'nullable|int',
            ]
        );

        $city = City::findOrFail($request->get('city'));
        $start = Carbon::createFromFormat('d.m.Y', $request->get('start'))->setTime(0,0,0)->setTimezone('Europe/Berlin');
        $end = Carbon::createFromFormat('d.m.Y', $request->get('end'))->setTime(23,59,59)->setTimezone('Europe/Berlin');

        $days = Day::where('date', '>=', Carbon::createFromFormat('d.m.Y', $request->get('start')))
            ->where('date', '<=', Carbon::createFromFormat('d.m.Y', $request->get('end')))
            ->orderBy('date', 'ASC')
            ->get();
        $dayIds = $days->plucK('id');

        $services = Service::with(['location', 'day'])
            ->notHidden()
            ->whereIn('day_id', $dayIds)
            ->where('city_id', $city->id)
            ->whereDoesntHave('funerals')
            ->whereDoesntHave('weddings')
            ->orderBy('time', 'ASC')
            ->get();

        $events = [];
        $calendar = new EventCalendarImport($city->public_events_calendar_url);
        $events = $calendar->mix($events, $start, $end, true, ($request->get('mixOutlook', 1) != 0));

        // mix in services
        if ($request->get('mixOutlook')) {
            $events = Service::mix($events, $services, $start, $end);
        } else {
            $events = Service::mix([], $services, $start, $end);
        }

        // mix in OP events?
        if ($request->get('mixOP')) {
            $op = new OPEventsImport($city);
            $events = $op->mix($events, $start, $end, true);
        }


        return view('reports.newsletter.render', compact('events', 'days'));
    }


}
