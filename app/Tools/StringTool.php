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
}