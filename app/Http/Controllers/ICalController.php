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
use App\CalendarLinks\AbstractCalendarLink;
use App\CalendarLinks\CalendarLinks;
use App\Service;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;

/**
 * Class ICalController
 * @package App\Http\Controllers
 */
class ICalController extends Controller
{
    /** @var User $user */
    protected $user = null;

    public function private($name, $token)
    {
        $this->checkToken($token);
        $services = Service::with(['day', 'location'])
            ->where(
                function ($query) use ($name) {
                    $query->where('pastor', 'like', '%' . $name . '%')
                        ->orWhere('organist', 'like', '%' . $name . '%')
                        ->orWhere('sacristan', 'like', '%' . $name . '%');
                }
            )->get();
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        header('Content-Type: text/calendar');
        $raw = View::make(
            'ical.ical',
            ['services' => $services, 'action' => 'private', 'token' => $token, 'key' => $name]
        );

        $raw = str_replace(
            "\r\n\r\n",
            "\r\n",
            str_replace('@@@@', "\r\n", str_replace("\n", "\r\n", str_replace("\r\n", '@@@@', $raw)))
        );
        die ($raw);
    }

    /**
     * @param $token
     */
    protected function checkToken($token)
    {
        $users = User::all();
        $found = false;
        foreach ($users as $user) {
            if ($user->api_token == $token) {
                $found = true;
                $this->user = $user;
            }
        }
        if (!$found) {
            die('wrong token');
        }
    }

    /**
     * @param $locationIds
     * @param $token
     */
    public function byLocation($locationIds, $token)
    {
        $this->checkToken($token);
        $services = Service::with(['day', 'location'])
            ->whereIn('city_id', explode(',', $locationIds))
            ->get();
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        header('Content-Type: text/calendar');
        $raw = View::make(
            'ical.ical',
            ['services' => $services, 'action' => 'gemeinden', 'token' => $token, 'key' => $locationIds]
        );

        $raw = str_replace(
            "\r\n\r\n",
            "\r\n",
            str_replace('@@@@', "\r\n", str_replace("\n", "\r\n", str_replace("\r\n", '@@@@', $raw)))
        );
        die ($raw);
    }



    /*
     * Export absences as ical
     *
     */
    /**
     * @param User $user
     * @param $token
     */
    public function absences(User $user, $token)
    {
        $this->checkToken($token);

        $users = $user->getViewableAbsenceUsers();
        $userId = $user->id;
        $absences = Absence::whereIn('user_id', $users->pluck('id'))
            ->orWhere(
                function ($query2) use ($userId) {
                    $query2->whereHas(
                        'replacements',
                        function ($query) use ($userId) {
                            $query->where('user_id', $userId);
                        }
                    );
                }
            )->get();

        $raw = View::make('ical.absences', compact('user', 'absences', 'token'));
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        header('Content-Type: text/calendar');
        $raw = str_replace(
            "\r\n\r\n",
            "\r\n",
            str_replace('@@@@', "\r\n", str_replace("\n", "\r\n", str_replace("\r\n", '@@@@', $raw)))
        );
        die($raw);
    }


    /**
     * @return \Inertia\Response
     */
    public function connect()
    {
        $calendarLinks = CalendarLinks::all();
        $cities = Auth::user()->cities;
        return Inertia::render('OutlookExport/Connect', compact('calendarLinks', 'cities'));
    }


    /**
     * @param $key
     * @return mixed
     */
    public function setup($key)
    {
        $calendarLink = CalendarLinks::findKey($key);
        return $calendarLink->setupView();
    }

    /**
     * @param Request $request
     * @param User $user
     * @param $token
     * @param $key
     * @return Application|ResponseFactory|Response|void
     */
    public function export(Request $request, User $user, $token, $key)
    {
        if ($token != $user->getToken()) {
            return abort(403);
        }

        /** @var AbstractCalendarLink $calendarLink */
        $calendarLink = CalendarLinks::findKey($key);


        $expires = 0;
        if ($key == 'cityEvents') {
            $expires = Carbon::now()->addMinutes(60)->format('D, d M Y H:i:s \G\M\T');
            $cacheKey = 'ical_export_' . $key . '_' . $token;
            if ((!$request->has('no_cache')) && Cache::has($cacheKey)) {
                $data = Cache::get($cacheKey);
            } else {
                $data = $calendarLink->export($request, $user);
                Cache::put($cacheKey, $data, 3600);
            }
        } else {
            $data = $calendarLink->export($request, $user);
        }


        return response($data)
            ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->header('Expires', $expires)
            ->header('Content-Type', 'text/calendar');
    }
}
