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

namespace App;

use Illuminate\Support\Facades\Cache;

/**
 * Class Liturgy
 * @package App
 */
class Liturgy
{

    /** @var Liturgy|null Instance */
    protected static $instance = null;

    /**
     * @return Liturgy|null
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get all the liturgical info for a given day
     * @param string|Day $day
     * @param bool $fallback
     * @return array
     */
    public static function getDayInfo($day, $fallback = false): array
    {
        if (!is_object($day)) {
            $day = new Day(['date' => $day]);
        }
        if (!Cache::has('liturgicalDays')) {
            $tmpData = json_decode(
                file_get_contents(
                    'https://www.kirchenjahr-evangelisch.de/service.php?o=lcf&f=gaa&r=json&dl=user'
                ),
                true
            );
            foreach ($tmpData['content']['days'] as $key => $val) {
                if (!isset($data[$val['date']])) $data[$val['date']] = $val;
            }
            Cache::put('liturgicalDays', $data, 86400);
        } else {
            $data = Cache::get('liturgicalDays');
        }

        if (isset($data[$day->date->format('d.m.Y')])) {
            return $data[$day->date->format('d.m.Y')];
        } elseif ($fallback) {
            $date = $day->date;
            while (!isset($data[$date->format('d.m.Y')])) {
                $date = $date->subDays(1);
            }
            return isset($data[$date->format('d.m.Y')]) ? $data[$date->format('d.m.Y')] : [];
        }
        return [];
    }


}
