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

namespace App\Http\Controllers;

use App\Absence;
use App\City;
use App\Day;
use App\Liturgy;
use App\Location;
use App\Service;
use App\Services\CalendarService;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use PDF;

/**
 * Class CalendarController
 * @package App\Http\Controllers
 */
class CalendarController extends Controller
{

    /**
     *
     */
    public const NAME_FORMAT_DEFAULT = 1;
    /**
     *
     */
    public const NAME_FORMAT_INITIAL_AND_LAST = 2;
    /**
     *
     */
    public const NAME_FORMAT_FIRST_AND_LAST = 3;


    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param Request $request
     * @param int $year
     * @param int $month
     * @return bool|Application|Factory|RedirectResponse|View
     */
    public function month(Request $request, $year = null, $month = null)
    {
        if (!is_null($year) && is_null($month)) {
            list($year, $month) = explode('-', $year);
        }

        $month ??= Auth::user()->getSetting('display-month') ?: date('m');
        $year ??= Auth::user()->getSetting('display-year') ?: date('Y');

        $monthStart = Carbon::create($year, $month, 1);
        $monthEnd = $monthStart->copy()->addMonth(1)->subSecond(1);

        if (!Session::has('showLimitedDays')) {
            Session::put('showLimitedDays', false);
        }

        $user = Auth::user();


        // city sorting
        if ($request->has('sort')) {
            $user->setSetting('sorted_cities', $request->get('sort'));
        }
        $cities = $sortedCities = $user->getSortedCities();
        $unusedCities = $user->cities->whereNotIn('id', $sortedCities->pluck('id'));

        // filter days:
        $days = Day::inMonth(Carbon::create($year, $month, 1))
            ->visibleForCities($cities)
            ->get();

        if (!count($days)) $days = CalendarService::initializeMonth($year, $month);

        $nextDay = Day::inMonth(Carbon::create($year, $month, 1))
            ->whereBetween('date', [Carbon::now()->setTime(0,0,0), Carbon::create($year, $month, 1)->addMonth(1)->subSecond(1)])
            ->first() ?? new Day(['date' => now()]);

        // name_format parameter
        $nameFormat = $request->has('name_format') ? $request->get('name_format') : $user->getSetting(
            'calendar_name_format',
            self::NAME_FORMAT_DEFAULT
        );
        $user->setSetting('calendar_name_format', $nameFormat);

        $years = Day::select(DB::raw('YEAR(days.date) as year'))->orderBy('date')->get()->pluck('year')->unique()->sort();
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = strftime('%B', mktime(0, 0, 0, $i, 1, date('Y')));
        }

        // determine orientation and view name;
        $defaultOrientation = $user->getSetting(
            'calendar_view',
            'horizontal'
        ) == 'month_vertical' ? 'vertical' : 'horizontal';
        $orientation = $request->get('orientation', $defaultOrientation);
        $user->setSetting('calendar_view', $orientation);


        // all possible locations
        $possibleLocations = Location::whereIn('city_id', $user->cities->pluck('id'))->get();
        $filteredLocations = CalendarService::getLocationsFilter($request, $possibleLocations, $user);

        // services
        $serviceQuery = Service::with('location')
            ->whereIn('city_id', $cities->pluck('id'))
            ->orderBy('time');

        if (count($filteredLocations)) {
            $serviceQuery->whereIn('location_id', $filteredLocations);
        }

        $services = $serviceQuery->get()->groupBy(['city_id', 'day_id']);

        // absences
        $absences = Absence::byPeriod($monthStart, $monthEnd)
            ->visibleForUser(Auth::user())
            ->get();

        $slave = $request->get('slave', 0);
        $highlight = $request->get('highlight', 0);

        // save current display settings for slave displays to follow
        if (!$slave) {
            $user = Auth::user();
            $currentDisplayMonth = $user->getSetting('display-month', 999);
            $currentDisplayYear = $user->getSetting('display-year', 999);
            if (($month != $currentDisplayMonth) || ($year != $currentDisplayYear)) {
                $user->setSetting('display-timestamp', time());
                $user->setSetting('display-month', $month);
                $user->setSetting('display-year', $year);
            }
        }

        return view(
            'calendar.month',
            compact(
                'year',
                'month',
                'years',
                'months',
                'days',
                'cities',
                'services',
                'nextDay',
                'absences',
                'highlight',
                'slave',
                'orientation',
                'sortedCities',
                'unusedCities',
                'nameFormat',
                'possibleLocations',
                'filteredLocations'
            )
        );
    }

    /**
     * @param Request $request
     * @param $route
     * @param $year
     * @param $month
     * @return bool|RedirectResponse
     */
    protected function redirectIfMissingParameters(Request $request, $route, $year, $month)
    {
        $defaultMonth = Auth::user()->getSetting('display-month', date('m'));
        $defaultYear = Auth::user()->getSetting('display-year', date('Y'));

        $initialYear = $year;
        $initialMonth = $month;


        if ($month == 13) {
            $year++;
            $month = 1;
        }
        if (($year > 0) && ($month == 0)) {
            $year--;
            $month = 12;
        }

        if ((!$year) || (!$month) || (!is_numeric($month)) || (!is_numeric($year)) || (!checkdate($month, 1, $year))) {
            $year = $defaultYear;
            $month = $defaultMonth;
        }

        if (($year == $initialYear) && ($month == $initialMonth)) {
            return false;
        }

        $data = compact('month', 'year');
        $slave = $request->get('slave', 0);
        if ($slave) {
            $data = array_merge($data, compact('slave'));
        }

        return redirect()->route($route, $data);
    }



    /**
     * @param bool $switch
     * @return false|string
     */
    public function showLimitedColumns(bool $switch)
    {
        Session::put('showLimitedDays', (bool)$switch);
        return json_encode(['showLimitedDays', Session::get('showLimitedDays')]);
    }
}
