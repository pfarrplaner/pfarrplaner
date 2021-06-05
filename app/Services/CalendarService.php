<?php
/*
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

namespace App\Services;


use App\Day;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CalendarService
{

    /**
     * @param $year
     * @param $month
     * @return Collection
     */
    public static function initializeMonth($year, $month)
    {
        $days = collect();
        $today = Carbon::create($year, $month, 1,0,0,0);
        while ($today->month == $month) {
            if ($today->dayOfWeek == 0) {
                $day = Day::create(
                    [
                        'date' => $today->format('d.m.Y'),
                        'name' => '',
                        'description' => '',
                    ]
                );
                $days->push($day);
            }
            $today->addDay(1);
        }
        return $days;
    }


    /**
     * Get the location filter either from request or from a user setting
     * @param Request $request
     * @param $possibleLocations
     * @param $user
     * @return array
     */
    public static function getLocationsFilter(Request $request, $possibleLocations, $user): array
    {
        if (!$user->hasSetting('calendar_filter_locations')) {
            $user->setSetting('calendar_filter_locations', '');
        }
        if ($request->has('filter_location')) {
            if ($request->get('filter_location') == '') {
                $filteredLocations = [];
            } else {
                $filteredLocations = (clone $possibleLocations)->filter(
                    function ($location) use ($request) {
                        return in_array($location->id, explode(',', $request->get('filter_location')));
                    }
                )->pluck('id')->toArray();
            }
            $user->setSetting('calendar_filter_locations', join(',', $filteredLocations));
        } else {
            if ('' === $user->getSetting('calendar_filter_locations', '')) {
                return [];
            }
            $filteredLocations = explode(',', $user->getSetting('calendar_filter_locations', []));
        }
        return $filteredLocations;
    }


}
