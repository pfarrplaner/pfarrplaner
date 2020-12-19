<?php

namespace App\Http\Controllers;

use App\Day;
use App\Service;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $days = Day::inMonth($date)->orderBy('date')->get();
        $cities = Auth::user()->cities;

        $years = Day::select(DB::raw('YEAR(days.date) as year'))->orderBy('date')->get()->pluck('year')->unique()->sort();

        $services = Service::with(['baptisms', 'funerals', 'weddings', 'participants'])
            ->inMonth($date)
            ->inCities($cities->pluck('id'))
            ->notHidden()
            ->get()
            ->groupBy(['city_id', 'day_id']);

        return Inertia::render('calendar', compact('date', 'days', 'cities', 'services', 'years'));
    }
}
