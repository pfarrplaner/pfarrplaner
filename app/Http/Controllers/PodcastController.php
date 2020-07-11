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

namespace App\Http\Controllers;

use App\City;
use App\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class PodcastController
 * @package App\Http\Controllers
 */
class PodcastController extends Controller
{
    /**
     * @param Request $request
     * @param $cityName
     * @return Response
     */
    public function podcast(Request $request, $cityName)
    {
        $city = City::where('name', $cityName)->first();
        if (null === $city) {
            abort(404);
        }

        $services = Service::where('city_id', $city->id)
            ->select('services.*')
            ->join('days', 'days.id', 'services.day_id')
            ->where('recording_url', '!=', '')
            ->orderBy('days.date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        return response()->view('podcasts.podcast', compact('city', 'services'))
            ->header('Content-Type', 'text/xml');
    }
}
