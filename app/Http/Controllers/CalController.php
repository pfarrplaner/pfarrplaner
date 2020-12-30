<?php

namespace App\Http\Controllers;

use App\Absence;
use App\Day;
use App\Service;
use App\Services\CalendarService;
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

    public function index(Request $request, $date = null, $month = null)
    {
        if ($month) {
            $date .= '-' . $month;
        }
        $date = ($date ? new Carbon ($date . '-01') : Carbon::now())->setTime(0, 0, 0);
        $monthEnd = $date->copy()->addMonth(1)->subSecond(1);
        $days = Day::with('cities')->inMonth($date)->orderBy('date')->get();
        if (count($days) == 0) {
            $days = CalendarService::initializeMonth($date->year, $date->month);
        }
        $user = Auth::user();
        $cities = $user->cities;

        $years = Day::select(DB::raw('YEAR(days.date) as year'))->orderBy('date')->get()->pluck('year')->unique()->sort(
        );

        $services = Service::with(['baptisms', 'funerals', 'weddings', 'participants'])
            ->inMonthByDate($date)
            ->inCities($cities->pluck('id'))
            ->notHidden()
            ->get()
            ->groupBy(['city_id', 'day_id']);

        $cities = array_values($user->getSortedCities()->all());

        // absences
        $absences = Absence::getByDays(
            Absence::with('user')->byPeriod($date, $monthEnd)
                ->visibleForUser(\Illuminate\Support\Facades\Auth::user())
                ->get(),
            $days
        );

        $canCreate = $user->can('create', Service::class);


        $data = [
            'days' => fn() => $days,
            'cities' => fn() => $cities,
            'services' => fn() => $services,
            'years' => fn() => $years,
            'absences' => fn() => $absences,
            'canCreate' => fn() => $canCreate,
        ];

        return Inertia::render(
            'calendar',
            compact('date', 'days', 'cities', 'services', 'years', 'absences', 'canCreate')
        );
    }
}
