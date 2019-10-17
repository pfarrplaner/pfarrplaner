<?php

namespace App\Http\Controllers;

use App\Absence;
use App\City;
use App\Day;
use App\Liturgy;
use App\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PDF;

class CalendarController extends Controller
{

    protected $vacationData = [];

    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function initializeMonth($year, $month)
    {
        $today = Carbon::createFromDate($year, $month, 1);
        while ($today->month == $month) {
            if ($today->dayOfWeek == 0) {
                $day = new Day([
                    'date' => $today->toDateString(),
                    'name' => '',
                    'description' => '',
                ]);
                $day->save();
            }
            $today->addDay(1);
        }
    }

    protected function redirectIfMissingParameters($route, $year, $month)
    {
        if ($month == 13) {
            return redirect()->route($route, ['year' => ++$year, 'month' => 1]);
        }
        if (($year > 0) && ($month == 0)) {
            return redirect()->route($route, ['year' => --$year, 'month' => 12]);
        }
        if ((!$year) || (!$month) || (!is_numeric($month)) || (!is_numeric($year)) || (!checkdate($month, 1, $year))) {
            return redirect()->route($route, ['year' => date('Y'), 'month' => date('m')]);
        }
        return false;
    }

    protected function getDaysInMonth($year, $month)
    {
        $monthStart = Carbon::createFromFormat('Y-m-d H:i:s', $year . '-' . $month . '-01 0:00:00');
        $monthEnd = (clone $monthStart)->addMonth(1)->subSecond(1);

        $days = Day::whereBetween('date', [$monthStart, $monthEnd])->orderBy('date', 'ASC')->get();
        if (!count($days)) {
            $this->initializeMonth($year, $month);
            $days = Day::whereBetween('date', [$monthStart, $monthEnd])->orderBy('date', 'ASC')->get();
        }
        return $days;
    }

    protected function getVacationers(Day $day)
    {
        $vacationers = [];
        /*
        if (env('VACATION_URL')) {
            if (!count($this->vacationData)) {
                $this->vacationData = json_decode(file_get_contents(env('VACATION_URL')), true);
            }
            foreach ($this->vacationData as $key => $datum) {
                $start = Carbon::createFromTimeString($datum['start']);
                $end = Carbon::createFromTimeString($datum['end']);
                if (($day->date > $start) && ($day->date < $end)) {
                    if (preg_match('/(?:U:|FB:) (\w*)/', $datum['title'], $tmp)) {
                        $vacationers[$tmp[1]] = $tmp[0];
                    }
                }
            }
        }
        **/
        $absences = Absence::with('user')
            ->where('from', '<=', $day->date)
            ->where('to', '>=', $day->date)
            ->get();
        foreach ($absences as $absence) {
            $vacationers[$absence->user->lastName()] = $absence;
        }
        return $vacationers;
    }

    public function month(Request $request, $year = 0, $month = 0)
    {
        if (false !== ($r = $this->redirectIfMissingParameters('calendar', $year, $month))) {
            return $r;
        }

        if (!Session::has('showLimitedDays')) {
            Session::put('showLimitedDays', false);
        }

        $days = $this->getDaysInMonth($year, $month);
        $nextDay = Day::where('date', '>=', Carbon::createFromTimestamp(time()))
            ->orderBy('date', 'ASC')
            ->limit(1)
            ->get()->first();

        //$cities = City::all();
        $cities = Auth::user()->getSortedCities();

        $allDays = Day::orderBy('date', 'ASC')->get();
        for ($i = $allDays->first()->date->year; $i <= $allDays->last()->date->year; $i++) {
            $years[] = $i;
        }
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = strftime('%B', mktime(0, 0, 0, $i, 1, date('Y')));
        }

        $viewName = Auth::user()->getSetting('calendar_view', 'calendar.month');


        $services = [];
        $vacations = [];
        $liturgy = [];
        foreach ($cities as $city) {
            foreach ($days as $day) {
                if (!isset($vacations[$day->id])) $vacations[$day->id] = $this->getVacationers($day);
                if (!isset($liturgy[$day->id])) $liturgy[$day->id] = Liturgy::getDayInfo($day);

                    $services[$city->id][$day->id] = Service::with('day', 'location')
                        ->where('city_id', $city->id)
                        ->where('day_id', '=', $day->id)
                        ->orderBy('time')
                        ->get();
            }
        }

        $slave = $request->get('slave', 0);
        $highlight = $request->get('highlight', 0);

        // save current display settings for slave displays to follow
        Auth::user()->setSetting('display-timestamp', time());
        Auth::user()->setSetting('display-month', $month);
        Auth::user()->setSetting('display-year', $year);

        return view($viewName, compact(
            'year', 'month', 'years', 'months', 'days', 'cities',
                   'services', 'nextDay', 'vacations', 'liturgy', 'highlight', 'slave'
            )
        );
    }


    public function monthJS($year = 0, $month = 0)
    {
        if (false !== ($r = $this->redirectIfMissingParameters('calendar', $year, $month))) {
            return $r;
        }

        if (!Session::has('showLimitedDays')) {
            Session::put('showLimitedDays', false);
        }

        $days = $this->getDaysInMonth($year, $month);
        $nextDay = Day::where('date', '>=', Carbon::createFromTimestamp(time()))
            ->orderBy('date', 'ASC')
            ->limit(1)
            ->get()->first();

        $cities = City::all();

        $allDays = Day::orderBy('date', 'ASC')->get();
        for ($i = $allDays->first()->date->year; $i <= $allDays->last()->date->year; $i++) {
            $years[] = $i;
        }
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = strftime('%B', mktime(0, 0, 0, $i, 1, date('Y')));
        }

        return view('calendar.monthjs', [
            'year' => $year,
            'month' => $month,
            'years' => $years,
            'months' => $months,
            'days' => $days,
            'cities' => $cities,
            'nextDay' => $nextDay,
        ]);
    }


    public function printsetup($year = 0, $month = 0)
    {
        if (false !== ($r = $this->redirectIfMissingParameters('calendar.printsetup', $year, $month))) {
            return $r;
        }

        $name = explode(' ', Auth::user()->name);
        $name = end($name);

        $cities = Auth::user()->getSortedCities();
        return view('calendar.printsetup',
            ['cities' => $cities, 'year' => $year, 'month' => $month, 'lastName' => $name]);
    }

    public function print(Request $request, $year = 0, $month = 0)
    {
        if (false !== ($r = $this->redirectIfMissingParameters('calendar.print', $year, $month))) {
            return $r;
        }

        $cities = City::whereIn('id', $request->get('includeCities', []))->get();
        $days = $this->getDaysInMonth($year, $month);

        $servicesList = [];
        foreach ($days as $key => $day) {
            $total = 0;
            foreach ($cities as $city) {
                $servicesList[$city->id][$day->id] = Service::with('day', 'location')
                    ->where('city_id', $city->id)
                    ->where('day_id', '=', $day->id)
                    ->orderBy('time')
                    ->get();
                $total += count($servicesList[$city->id][$day->id]);
            }
            if (!$total && ($request->get('excludeEmptyDays', false)) && ((!$request->get('alwaysIncludeSundays',
                        false)) || ($day->date->dayOfWeek > 0))) {
                $days->forget($key);
            }
        }

        $tableRatio = 100 / (count($days) + 1);

        $data = [
            'year' => $year,
            'month' => $month,
            'days' => $days,
            'cities' => $cities,
            'services' => $servicesList,
            'tableRatio' => $tableRatio,
            'highlight' => $request->get('highlight', '')
        ];
        $pdf = PDF::loadView('calendar.print.month', $data, [], [
            'format' => 'A4-L',
            'author' => isset(Auth::user()->name) ? Auth::user()->name : Auth::user()->email,
        ]);
        return $pdf->stream($year . '-' . str_pad($month, 2, 0, STR_PAD_LEFT) . ' Dienstplan.pdf');
    }
}
