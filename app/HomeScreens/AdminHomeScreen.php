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
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Rap2hpoutre\LaravelLogViewer\LaravelLogViewer;

/**
 * Class AdminHomeScreen
 * @package App\HomeScreens
 */
class AdminHomeScreen extends AbstractHomeScreen
{
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

        $logViewer = new LaravelLogViewer();
        $logViewer->setFolder('apache2handler');
        $logs = $logViewer->all();

        return $this->renderView('homescreen.admin', compact('user', 'logs'));
    }

}
