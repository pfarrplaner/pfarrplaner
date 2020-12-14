<?php

namespace App\Http\Controllers;

use App\Day;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, $date = null)
    {
        $date = $date ? new Carbon ($date.'-01') : Carbon::now();
        $days = Day::inMonth($date)->get();
        $cities = Auth::user()->cities;

        return Inertia::render('calendar', compact('date', 'days', 'cities'));
    }
}
