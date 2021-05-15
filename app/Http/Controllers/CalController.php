<?php

namespace App\Http\Controllers;

use App\Absence;
use App\City;
use App\Day;
use App\Service;
use App\Services\CalendarService;
use App\Services\RedirectorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function singleDay(Day $day, City $city)
    {
        $services = Service::setEagerLoads([])
            ->with(['day', 'baptisms', 'funerals', 'weddings', 'participants'])
            ->where('day_id', $day->id)
            ->inCities([$city->id])
            ->orderBy('time')
            ->get();

        $day->load('cities');
        $canCreate = Auth::user()->can('create', Service::class);

        // absences
        $absences = Absence::with('user')->byPeriod($day->date, $day->date)
                ->visibleForUser(Auth::user())
                ->get();

        return Inertia::render('Calendar/SingleDay', compact('day', 'city', 'services', 'absences', 'canCreate'));

    }

    public function index(Request $request, $date = null, $month = null)
    {
        RedirectorService::saveCurrentRoute();
        if ($month) {
            $date .= '-' . $month;
        }
        $date = ($date ? new Carbon ($date . '-01') : Carbon::now())->setTime(0, 0, 0);
        if ($date->format('Ym') < 201801) abort(404);
        $monthEnd = $date->copy()->addMonth(1)->subSecond(1);
        $days = Day::with('cities')->inMonth($date)->orderBy('date')->get();
        if (count($days) == 0) {
            $days = CalendarService::initializeMonth($date->year, $date->month);
        }
        $user = Auth::user();
        $cities = $user->cities;

        $years = Day::select(DB::raw('YEAR(days.date) as year'))
            ->where('days.date', '>=', '2018-01-01')
            ->orderBy('date')
            ->get()
            ->pluck('year')
            ->unique()
            ->sort();

        $services = [];
        foreach ($cities as $city) foreach ($days as $day) $services[$city->id][$day->id] = [];

        $cities = array_values($user->getSortedCities()->all());

        // absences
        $absences = Absence::getByDays(
            Absence::with('user')->byPeriod($date, $monthEnd)
                ->visibleForUser(Auth::user())
                ->get(),
            $days
        );

        $canCreate = $user->can('create', Service::class);

        return Inertia::render(
            'Calendar/Calendar',
            compact('date', 'days', 'cities', 'years', 'absences', 'canCreate', 'services')
        );
    }

    public function city($date, City $city) {
        $date = ($date ? new Carbon ($date . '-01') : Carbon::now())->setTime(0, 0, 0);
        if ($date->format('Ym') < 201801) abort(404);

        $services = Service::setEagerLoads([])
            ->with(['day', 'baptisms', 'funerals', 'weddings', 'participants'])
            ->inMonthByDate($date)
            ->inCities([$city->id])
            ->orderBy('time')
            ->get()
            ->groupBy(['day_id']);

        return response()->json($services);
    }

    public function day(Day $day, City $city)
    {
        $services = Service::setEagerLoads([])
            ->with(['day', 'baptisms', 'funerals', 'weddings', 'participants'])
            ->where('day_id', $day->id)
            ->inCities([$city->id])
            ->orderBy('time')
            ->get();

        return response()->json($services);
    }
}
