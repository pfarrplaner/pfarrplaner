<?php
/*
 * dienstplan
 *
 * Copyright (c) 2019 Christoph Fischer, https://christoph-fischer.org
 * Author: Christoph Fischer, chris@toph.de
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

class Liturgy
{

    /** @var Liturgy|null Instance */
    protected static $instance = null;

    protected static $data = [];

    public static function getInstance() {
        if (null === self::$instance) self::$instance = new self();
        return self::$instance;
    }

    public static function getDayInfo(Day $day, $fallback = false): array {
        if (!count(self::$data)) {
            $tmpData = json_decode(
                file_get_contents(
                    'https://www.kirchenjahr-evangelisch.de/service.php?o=lcf&f=gaa&r=json&dl=user'),
                true);
            foreach ($tmpData['content']['days'] as $key => $val) {
                self::$data[$val['date']] = $val;
            }
        }
        if (isset(self::$data[$day->date->format('d.m.Y')])) {
            return self::$data[$day->date->format('d.m.Y')];
        } elseif ($fallback) {
            $date = $day->date;
            while (!isset(self::$data[$date->format('d.m.Y')])) {
                $date = $date->subDays(1);
            }
            return isset(self::$data[$date->format('d.m.Y')]) ? self::$data[$date->format('d.m.Y')] : [];
        }
        return [];
    }


}
