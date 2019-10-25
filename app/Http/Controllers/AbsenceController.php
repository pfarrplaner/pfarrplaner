<?php

namespace App\Http\Controllers;

use App\Absence;
use App\Day;
use App\Replacement;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    private function getHolidays(Carbon $start, Carbon $end) {
        $raw = json_decode(file_get_contents('https://ferien-api.de/api/v1/holidays/BW'), true);
        $holidays = [];
        foreach ($raw as $holiday) {
            $holiday['start'] = new Carbon($holiday['start']);
            $holiday['end'] = (new Carbon($holiday['end']))->subSecond(1);
            $holiday['name'] = ucfirst($holiday['name']);
            if (($holiday['start'] <= $end) && ($holiday['end'] >= $start)) $holidays[] = $holiday;
        }
        return $holidays;
    }



    protected function redirectIfMissingParameters(Request $request, $route, $year, $month)
    {
        $defaultMonth = Auth::user()->getSetting('display-month', date('m'));
        $defaultYear = Auth::user()->getSetting('display-year', date('Y'));

        $initialYear = $year;
        $initialMonth = $month;


        if ($month == 13) {
            $year++;
            $month = 1;
        }
        if (($year > 0) && ($month == 0)) {
            $year--;
            $month = 12;
        }

        if ((!$year) || (!$month) || (!is_numeric($month)) || (!is_numeric($year)) || (!checkdate($month, 1, $year))) {
            $year = $defaultYear;
            $month = $defaultMonth;
        }

        if (($year == $initialYear) && ($month == $initialMonth)) {
            return false;
        }

        $data = compact('month', 'year');
        $slave = $request->get('slave', 0);
        if ($slave) $data = array_merge($data, compact('slave'));

        return redirect()->route($route, $data);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $year = 0, $month = 0)
    {

        if (false !== ($r = $this->redirectIfMissingParameters($request, 'absences.index', $year, $month))) {
            return $r;
        }

        /**
        $users = User::where('manage_absences', 1)
            ->whereHas('cities', function ($query) {
                $query->whereIn('cities.id', Auth::user()->cities);
            })
            ->get();
         */

        $users = Auth::user()->getViewableAbsenceUsers();

        $now = new Carbon($year.'-'.$month.'-01 0:00:00');
        $start = (clone $now)->subMonth(1);
        $end = (clone $now)->addMonth(2)->subDay(1);

        $holidays = $this->getHolidays($start, $end);

        $allDays = Day::orderBy('date', 'ASC')->get();
        for ($i = $allDays->first()->date->year; $i <= $allDays->last()->date->year; $i++) {
            $years[] = $i;
        }
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = strftime('%B', mktime(0, 0, 0, $i, 1, date('Y')));
        }

        $absences = Absence::whereIn('user_id', $users->pluck('id'))
            ->where('to','>=', $start)
            ->where('from', '<=', $end)
            ->get();

        return view('absences.index', compact('users', 'start', 'end', 'year', 'month', 'months', 'years', 'now', 'holidays'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($year, $month, User $user)
    {
        $users = User::all();
        return view('absences.create', compact('month', 'year', 'users', 'user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'from' => 'required',
            'to' => 'required',
            'user_id' => 'required'
        ]);

        $absence = new Absence([
            'from' => Carbon::createFromFormat('d.m.Y H:i:s', ($request->get('from') ?: '').' 0:00:00'),
            'to' => Carbon::createFromFormat('d.m.Y H:i:s', ($request->get('to') ?: '').' 0:00:00'),
            'user_id' => $request->get('user_id'),
            'reason' => $request->get('reason') ?: '',
        ]);
        $absence->save();

        $this->setupReplacements($absence, $request->get('replacement') ?: []);

        return redirect()->route('absences.index', ['month' => $absence->from->format('m'), 'year' => $absence->from->format('Y')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function show(Absence $absence)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Absence $absence)
    {
        $absence->load('replacements');
        if ($request->has('startMonth')) {
            list($month, $year) = explode('-', $request->get('startMonth'));
        }
        if (!$year) $year = date('Y');
        if (!$month) $year = date('m');
        $users = User::all();
        return view('absences.edit', compact('absence', 'month', 'year', 'users'));
    }

    protected function setupReplacements(Absence $absence, $data)
    {
        $absence->load('replacements');
        if (count($absence->replacements)) {
            foreach ($absence->replacements as $replacement) {
                $replacement->delete();
            }
        }
        foreach ($data as $replacementData) {
            $replacement = new Replacement([
                'absence_id' => $absence->id,
                'from' => max(Carbon::createFromFormat('d.m.Y', $replacementData['from']), $absence->from),
                'to' => min(Carbon::createFromFormat('d.m.Y', $replacementData['to']), $absence->to),
            ]);
            $replacement->save();
            $replacement->users()->sync($replacementData['user']);
            $replacementIds[] = $replacement->id;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Absence $absence)
    {
        $request->validate([
            'from' => 'required',
            'to' => 'required',
        ]);
        $absence->from = Carbon::createFromFormat('d.m.Y H:i:s', $request->get('from').' 0:00:00');
        $absence->to = Carbon::createFromFormat('d.m.Y H:i:s', $request->get('to').' 0:00:00');
        $absence->reason = $request->get('reason') ?: '';
        $absence->save();
        $this->setupReplacements($absence, $request->get('replacement') ?: []);
        return redirect()->route('absences.index', ['month' => $absence->from->format('m'), 'year' => $absence->from->format('Y')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Absence  $absence
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Absence $absence)
    {
        $absence->delete();
        return redirect()->route('absences.index', ['month' => $request->get('month'), 'year' => $request->get('year')]);
    }
}
