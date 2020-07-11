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
 * Date: 11.10.2019
 * Time: 19:03
 */

namespace App\Tools;


use Carbon\Carbon;

class StringTool
{
    public static function timeString($s, $clockText = true, $separator = ':')
    {
        return Carbon::createFromTimeString($s)->formatLocalized('%H' . $separator . '%M') . ($clockText ? ' Uhr' : '');
    }

    public static function durationText(Carbon $from, Carbon $to): string
    {
        if ($from == $to) {
            return $from->format('d.m.Y');
        } elseif ($from->year == $to->year) {
            return $from->format('d.m.') . ' - ' . $to->format('d.m.Y');
        } else {
            return $from->format('d.m.Y') . ' - ' . $to->format('d.m.Y');
        }
        return '';
    }

    public static function pluralString($count, $singular, $plural, $zeroString = '')
    {
        if ($count == 0 ) return $zeroString ?: $plural;
        return ($count == 1) ? $singular : $plural;
    }

    public static function trimToLen($s, $maxLength) {
        if (strlen($s) > $maxLength)
        {
            $offset = ($maxLength - 3) - strlen($s);
            $s = substr($s, 0, strrpos($s, ' ', $offset)) . '...';
        }
        return $s;
    }

    public static function timeText($time, $uhr = true, $separator=':', $skipMinutes = false, $nbsp = false, $leadingZero = false)  {
        if (!is_numeric($time)) $time = strtotime($time);
        $format = ($leadingZero ? '%H' : '%k').$separator.'%M';
        if ($skipMinutes) {
            if ((int)strftime('%M', $time) == 0) $format = '%H';
        }
        return trim(strftime($format, $time).($uhr ? ($nbsp ? '&nbsp;' : ' ').'Uhr' : ''));

    }

    public static function indent($text) {
        $indent = 0;
        $lines = explode("\n", $text);
        foreach ($lines as $index => $line) {
            $line = trim($line);
            if (substr($line, 0, 2) == '</') $indent--;
            elseif (substr($line, 0, 1) == '<') $indent++;
            $lines[$index] = str_pad($line, 4*$indent, ' ', STR_PAD_LEFT);
        }
        return join("\n", $lines);
    }
}
