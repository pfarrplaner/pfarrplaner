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
 * Date: 05.08.2019
 * Time: 17:57
 */

namespace App\Http\Controllers;


use App\Absence;
use App\Service;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class EmbedController
 * @package App\Http\Controllers
 */
class EmbedController extends Controller
{

    public function __construct()
    {
        $this->middleware('cors');
    }


    /**
     * Return a table of services for specific locations
     * @param Request $request Request
     * @param string $ids comma-separated location ids
     * @param int $limit Limit (optional)
     */
    public function embedByLocations(Request $request, $ids, $limit = 10)
    {
        $ids = explode(',', $ids);
        $title = $request->has('title') ? $request->get('title') : '';
        $services = Service::with('location')
            ->select('services.*')
            ->join('days', 'services.day_id', '=', 'days.id')
            ->whereIn('location_id', $ids)
            ->where('hidden', '!=', 1)
            ->whereHas(
                'day',
                function ($query) {
                    $query->where('date', '>=', Carbon::now('Europe/Berlin')->setTime(0, 0, 0));
                }
            )
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->limit($limit)
            ->get();
        return view('embed.services.table', compact('services', 'ids', 'title'));
    }

    /**
     * Return a table of services for specific cities
     * @param Request $request Request
     * @param string $ids comma-separated location ids
     * @param int $limit Limit (optional)
     */
    public function embedByCities(Request $request, $ids, $limit = 10)
    {
        $ids = explode(',', $ids);
        $title = $request->has('title') ? $request->get('title') : '';
        $withStreaming = $request->get('withStreaming', false);
        $services = Service::with(['location', 'participants'])
            ->select('services.*')
            ->join('days', 'services.day_id', '=', 'days.id')
            ->inCities($ids)
            ->where('hidden', '!=', 1)
            ->whereHas(
                'day',
                function ($query) {
                    $query->where('date', '>=', Carbon::now('Europe/Berlin')->setTime(0, 0, 0));
                }
            )
            ->doesntHave('funerals')
            ->doesntHave('weddings')
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->limit($limit)
            ->get();

        $id = uniqid();

        return view('embed.services.table', compact('services', 'ids', 'title', 'withStreaming', 'id'));
    }

    /**
     * Return a table of childrens-church services for specific cities
     * @param Request $request Request
     * @param string $ids comma-separated location ids
     * @param int $limit Limit (optional)
     */
    public function embedCCByCities(Request $request, $ids, $limit = 5)
    {
        $ids = explode(',', $ids);
        $title = $request->has('title') ? $request->get('title') : '';
        $services = Service::with(['location', 'participants'])
            ->select('services.*')
            ->join('days', 'services.day_id', '=', 'days.id')
            ->where('hidden', '!=', 1)
            ->whereIn('city_id', $ids)
            ->whereHas(
                'day',
                function ($query) {
                    $query->where('date', '>=', Carbon::now('Europe/Berlin')->setTime(0, 0, 0));
                }
            )
            ->doesntHave('funerals')
            ->doesntHave('weddings')
            ->where('cc', true)
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->limit($limit)
            ->get();
        return response()
            ->view('embed.services.ccTable', compact('services', 'ids', 'title'));
    }


    /**
     * Return a table of upcoming vacations and replacements for a specific user
     * @param Request $request
     * @param User $user
     * @param $userId
     */
    public function embedUserVacations(Request $request, User $user)
    {
        $start = Carbon::now();
        $end = (clone $start)->addWeek(2);
        //$vacations = Vacations::getByPeriodAndUser($start, $end, $user);
        $vacations = Absence::with('replacements')->byUserAndPeriod($user, $start, $end)->get();
        return response()
            ->view('embed.user.vacations', compact('user', 'vacations'));
    }


    /**
     * @param Request $request
     * @param User $user
     * @param $ids
     * @param int $limit
     * @param int $maxBaptisms
     * @return Application|Factory|View
     */
    public function embedByBaptismalServices(Request $request, User $user, $ids, $limit = 10, $maxBaptisms = 0)
    {
        $ids = explode(',', $ids);
        $title = $request->has('title') ? $request->get('title') : '';
        $services = Service::with('location', 'baptisms')
            ->select('services.*')
            ->join('days', 'services.day_id', '=', 'days.id')
            ->where('baptism', true)
            ->whereIn('city_id', $ids)
            ->whereHas(
                'day',
                function ($query) {
                    $query->where('date', '>=', Carbon::now('Europe/Berlin')->setTime(0, 0, 0));
                }
            )
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->limit($limit)
            ->get();

        return view('embed.services.baptismalServices', compact('services', 'ids', 'title', 'maxBaptisms'));
    }

    /**
     * @param Request $request
     * @param $report
     * @return mixed
     */
    public function embedReport(Request $request, $report)
    {
        $reportClass = 'App\\Reports\\Embed' . ucfirst($report) . 'Report';
        if (!class_exists($reportClass)) {
            abort(404);
        }
        $reportObject = new $reportClass();
        return $reportObject->embed($request);
    }

}
