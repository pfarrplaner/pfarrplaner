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
 * Date: 02.11.2019
 * Time: 12:30
 */

namespace App\Reports;


use App\City;
use App\Imports\EventCalendarImport;
use App\Imports\OPEventsImport;
use App\Service;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Class EmbedEventsTableReport
 * @package App\Reports
 */
class EmbedEventsTableReport extends AbstractEmbedReport
{

    /**
     * @var string
     */
    public $title = 'Liste von aktuellen Veranstaltungen';
    /**
     * @var string
     */
    public $group = 'Website (Gemeindebaukasten)';
    /**
     * @var string
     */
    public $description = 'Erzeugt HTML-Code für die Einbindung einer Veranstaltungsliste in die Website der Gemeinde';
    /**
     * @var string
     */
    public $icon = 'fa fa-file-code';

    /**
     * @return Application|Factory|View
     */
    public function setup()
    {
        $cities = Auth::user()->cities;
        return $this->renderSetupView(compact('cities'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|string
     */
    public function render(Request $request)
    {
        $request->validate(
            [
                'cors-origin' => 'required|url',
                'city' => 'required',
            ]
        );
        $city = City::findOrFail($request->get('city'));
        $days = $request->get('numDays');
        $corsOrigin = $request->get('cors-origin');
        $report = $this->getKey();

        $url = route('report.embed', compact('report', 'days', 'city', 'corsOrigin'));
        $randomId = uniqid();

        return view('reports.embedeventstable.render', compact('url', 'randomId'));
    }


    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function embed(Request $request)
    {
        $city = City::findOrFail($request->get('city'));
        $days = $request->get('days');

        $start = Carbon::now('Europe/Berlin')->setTime(0, 0, 0);
        $end = $start->copy()->addDays($days)->setTime(23, 59, 59);

        $services = Service::with(['day', 'location'])
            ->notHidden()
            ->whereDoesntHave('funerals')
            ->whereDoesntHave('weddings')
            ->regularForCity($city)
            ->dateRange($start, $end)
            ->ordered()
            ->get();

        $events = [];

        $calendar = new EventCalendarImport($city->public_events_calendar_url);
        $events = $calendar->mix($events, $start, $end, true, ($request->get('mixOutlook', 1) != 0));

        // mix in services
        $events = Service::mix($events, $services, $start, $end);

        // mix in OP events?
        if ($request->get('mixOP')) {
            $op = new OPEventsImport($city);
            $events = $op->mix($events, $start, $end, true);
        }

        $customerToken = $city->op_customer_token;
        $customerKey = $city->op_customer_key;
        $randomId = uniqid();

        return $this->renderView(
            'embed',
            compact('start', 'days', 'city', 'events', 'customerKey', 'customerToken', 'randomId')
        );
    }
}
