<?php
/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 27.06.2019
 * Time: 13:51
 */

namespace App\Http\Controllers;


use App\Day;
use App\Liturgy;

class LiturgicalDaysController extends Controller
{

    public function info($dayId) {
        $day = Day::find($dayId);
        $liturgy = Liturgy::getDayInfo($day);
        return view('liturgicalDays.ajax.info', compact('day', 'liturgy'));
    }

}