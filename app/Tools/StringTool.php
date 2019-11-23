<?php
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
}