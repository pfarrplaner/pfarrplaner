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
 * Date: 27.06.2019
 * Time: 12:29
 */

namespace App;


use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class Vacations
{

    protected static function getVacationDataFromCache() {
        if (!Cache::has('vacationData')) {
            $vacationData = json_decode(file_get_contents(env('VACATION_URL')), true);
            foreach ($vacationData as $key => $datum) {
                $vacationData[$key]['start'] = Carbon::createFromTimeString($datum['start']);
                $vacationData[$key]['end'] = Carbon::createFromTimeString($datum['end']);
            }
            Cache::put('vacationData', $vacationData, 10000);
        } else {
            $vacationData = Cache::get('vacationData');
        }
        return $vacationData;
    }

    public static function getByDay($day)
    {
        $vacationers = [];
        $vacationData = self::getVacationDataFromCache();

        foreach ($vacationData as $key => $datum) {
            if (($day->date >= $datum['start']) && ($day->date <= $datum['end'])) {
                if (preg_match('/(?:U:|FB:) (\w*)/', $datum['title'], $tmp)) {
                    $vacationers[$tmp[1]] = $tmp[0];
                }
            }
        }

        return $vacationers;
    }


    protected static function findUserByLastName($lastName)
    {
        return User::with('cities')->where('name', 'like', '%' . $lastName)->first();
    }



    public static function getByPeriodAndUser($start, $end, User $user = null)
    {
        return Absence::with('replacements')->byUserAndPeriod($user, $start, $end);
    }

}
