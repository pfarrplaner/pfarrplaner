<?php
/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 27.06.2019
 * Time: 13:08
 */

namespace App\Http\Controllers;


use App\Day;
use App\Vacations;

class VacationController extends Controller
{

    protected $vacationData = [];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function vacationsByDay($dayId) {
        $day = Day::find($dayId);
        $vacations = Vacations::getByDay($day);
        return view('vacations.ajax.byDay', compact('day', 'vacations'));
    }

}