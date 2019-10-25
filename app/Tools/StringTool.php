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
    public static function timeString($s, $clockText = true, $separator = ':') {
        return Carbon::createFromTimeString($s)->formatLocalized('%H'.$separator.'%M').($clockText ? ' Uhr' : '');
    }

    public static function durationText(Carbon $from, Carbon $to): string {
        if ($from == $to) {
            return $from->format('d.m.Y');
        } elseif ($from->year == $to->year) {
            return $from->format('d.m.').' - '.$to->format('d.m.Y');
        } else {
            return $from->format('d.m.Y').' - '.$to->format('d.m.Y');
        }
        return '';
    }
}