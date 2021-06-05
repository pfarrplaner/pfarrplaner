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
 * Class MinistryHomeScreen
 * @package App\HomeScreens
 */
class MinistryHomeScreen extends AbstractHomeScreen
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

        $start = Carbon::now();
        $end = Carbon::now()->addMonth(2);

        $services = Service::with(['baptisms', 'weddings', 'funerals', 'location', 'day'])
            ->select(['services.*', 'days.date'])
            ->join('days', 'days.id', '=', 'day_id')
            ->whereHas(
                'participants',
                function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }
            )->whereHas(
                'day',
                function ($query) use ($start, $end) {
                    $query->where('date', '>=', $start)
                        ->where('date', '<=', $end);
                }
            )
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->get();

        $missing = Service::withOpenMinistries(unserialize($user->getSetting('homeScreen_ministries', '')) ?: []);

        return $this->renderView('homescreen.ministry', compact('user', 'services', 'missing'));
    }

    /**
     * @return array|string
     * @throws Throwable
     */
    public function renderConfigurationView()
    {
        return view('homescreen.ministry.config')->render();
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
