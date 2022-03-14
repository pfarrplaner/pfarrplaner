<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2022 Christoph Fischer, https://christoph-fischer.org
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

class ColorService
{

    /**
     * Get an evenly spaced rainbow spectrum of colors
     * @param $a Array with keys
     * @source https://stackoverflow.com/a/7419630
     * @return array Array with colors, keys from $a
     */
    public static function rainbowArray($a) {
        $rainbow = [];
        $numberOfSteps = count($a);
        $step = 0;
        foreach($a as $key) {
            $step++;
            $h = $step / $numberOfSteps;
            $i = ~~($h*6);
            $f = $h*6-$i;
            $q = 1-$f;
            switch ($i % 6) {
                case 0: $r = 1; $g = $f; $b = 0; break;
                case 1: $r = $q; $g = 1; $b = 0; break;
                case 2: $r = 0; $g = 1; $b = $f; break;
                case 3: $r = 0; $g = $q; $b = 1; break;
                case 4: $r = $f; $g = 0; $b = 1; break;
                case 5: $r = 1; $g = 0; $b = $q; break;
            }
            $rainbow[$key] = "#"
                .str_pad(dechex(round($r*255)),2,'0', STR_PAD_LEFT)
                .str_pad(dechex(round($g*255)),2,'0', STR_PAD_LEFT)
                .str_pad(dechex(round($b*255)),2,'0', STR_PAD_LEFT);
        }
        return $rainbow;
    }

    /**
     * Find the best contrasting text color
     * @param $color base color, #rrggbb notation
     * @source https://stackoverflow.com/questions/3942878/how-to-decide-font-color-in-white-or-black-depending-on-background-color
     * @return string contrast color, #rrggbb notation
     */
    public static function contrastColor($color) {
        $r = hexdec(substr($color, 1, 2));
        $g = hexdec(substr($color, 3, 2));
        $b = hexdec(substr($color, 5, 2));
        if (($r*0.299 + $g*0.587 + $b*0.114) > 186) return  '#000000'; else return '#ffffff';
    }

}
