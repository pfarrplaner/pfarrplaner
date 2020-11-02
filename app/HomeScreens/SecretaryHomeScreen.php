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

/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 24.04.2019
 * Time: 14:32
 */

namespace App\HomeScreens;


use App\Service;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

/**
 * Class SecretaryHomeScreen
 * @package App\HomeScreens
 */
class SecretaryHomeScreen extends AbstractHomeScreen
{
    /**
     * @var bool
     */
    protected $hasConfiguration = true;

    /**
     * @return Factory|View|mixed
     */
    public function render()
    {
        /** @var User $user */
        $user = Auth::user();
        $name = $user->lastName();

        $start = Carbon::now();
        $end = Carbon::now()->addMonth(2);

        $services = Service::with(['baptisms', 'weddings', 'funerals', 'location', 'day'])
            ->select(['services.*', 'days.date'])
            ->join('days', 'days.id', '=', 'day_id')
            ->whereIn('city_id', $user->writableCities->pluck('id'))
            ->whereHas(
                'day',
                function ($query) use ($start, $end) {
                    $query->where('date', '>=', $start)
                        ->where('date', '<=', $end);
                }
            )
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->get();

        $end = Carbon::now()->addYear(1);

        $baptisms = Service::with(['baptisms', 'location', 'day'])
            ->select(['services.*', 'days.date'])
            ->join('days', 'days.id', '=', 'day_id')
            ->whereIn('city_id', $user->writableCities->pluck('id'))
            ->whereHas('baptisms')
            ->whereHas(
                'day',
                function ($query) use ($start, $end) {
                    $query->where('date', '>=', $start)
                        ->where('date', '<=', $end);
                }
            )
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->get();
        $baptisms->load('day');

        $funerals = Service::with(['funerals', 'location', 'day'])
            ->select(['services.*', 'days.date'])
            ->join('days', 'days.id', '=', 'day_id')
            ->whereIn('city_id', $user->writableCities->pluck('id'))
            ->whereHas('funerals')
            ->whereHas(
                'day',
                function ($query) use ($start, $end) {
                    $query->where('date', '>=', $start);
                }
            )
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->get();
        $funerals->load('day');

        $weddings = Service::with(['weddings', 'location', 'day'])
            ->select(['services.*', 'days.date'])
            ->join('days', 'days.id', '=', 'day_id')
            ->whereIn('city_id', $user->writableCities->pluck('id'))
            ->whereHas('weddings')
            ->whereHas(
                'day',
                function ($query) use ($start, $end) {
                    $query->where('date', '>=', $start)
                        ->where('date', '<=', $end);
                }
            )
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->get();
        $weddings->load('day');

        $missing = Service::withOpenMinistries(unserialize($user->getSetting('homeScreen_ministries', '')) ?: []);

        $registrable = Service::where('needs_reservations', 1)
            ->select(['services.*', 'days.date'])
            ->join('days', 'days.id', '=', 'day_id')
            ->whereIn('city_id', $user->writableCities->pluck('id'))
            ->whereHas(
                'day',
                function ($query) use ($start, $end) {
                    $query->where('date', '>=', $start)
                        ->where('date', '<=', $end);
                }
            )
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->get();


        return $this->renderView(
            'homescreen.secretary',
            compact('user', 'services', 'funerals', 'baptisms', 'weddings', 'missing', 'registrable')
        );
    }

    /**
     * @return array|string
     * @throws Throwable
     */
    public function renderConfigurationView()
    {
        return view('homescreen.pastor.config')->render();
    }


    /**
     * @param Request $request
     */
    public function setConfiguration(Request $request)
    {
        parent::setConfiguration($request);
        $data = $request->get('homeScreen');
        Auth::user()->setSetting('homeScreen_ministries', serialize($data['ministries']));
    }

}
