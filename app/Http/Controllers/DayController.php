<?php

namespace App\Http\Controllers;

use App\City;
use App\Day;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DayController extends Controller
{

    protected $dataCache = [];

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cities = City::all();
        return view('days.create', compact('cities'));
        //
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
            'date' => 'required|date|date_format:d.m.Y',
        ]);

        $today = Carbon::createFromFormat('d.m.Y', $request->get('date'))->setTime(0,0,0);

        // limited event without churches will not be created
        if (($request->get('day_type') == Day::DAY_TYPE_LIMITED) && (count($request->get('cities') ?: []) == 0))
            return redirect()->route('calendar', ['year' => $today->year, 'month' => $today->month]);

        // check if day already exists in limited form for other churches...
        // ...if so, only attach new cities
        $day = Day::where('date', $today->format('Y-m-d'))->first();
        if ($day) {
            $cities = $request->get('cities') ?: [];
            if (count($cities)) {
                foreach ($cities as $city) $day->cities()->attach($city);
            }
            if ($x = $request->get('description')) $day->description = $x;
            if ($x = $request->get('name')) $day->name = $x;
            $day->save();
            return redirect()->route('calendar', ['year' => $today->year, 'month' => $today->month])
                ->with('success', 'Der '.$today->format('d.m.Y').' wurde der Liste hinzugefügt.');
        }

        // ...if not, create a new day record
        $day = new Day([
            'date' => $today->format('Y-m-d'),
            'name' => $request->get('name') ?: '',
            'description' => $request->get('description') ?: '',
            'day_type' => $request->get('day_type') ?: 0,
        ]);
        if ($day->name == '') $day->name = $this->getLiturgicalFunction($day);
        $day->save();
        if ($request->get('day_type') == Day::DAY_TYPE_LIMITED) {
            $day->cities()->sync($request->get('cities') ?: []);
        }
        return redirect()->route('calendar', ['year' => $today->year, 'month' => $today->month])
            ->with('success', 'Der '.$today->format('d.m.Y').' wurde der Liste hinzugefügt.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $day = Day::find($id);
        $cities = City::all();
        return view('days.edit', compact('day', 'cities'));
    }


    protected function getLiturgicalFunction(Day $day) {
        if (isset($this->dataCache[$day->date->year])) {
            $data = $this->dataCache[$day->date->year];
        } else {
            $this->dataCache[$day->date->year] = $data = json_decode(
                file_get_contents(
                    'https://www.kirchenjahr-evangelisch.de/service.php?o=lcf&f=gc&r=json&year='.$day->date->year.'&dl=user'),
            true);
        }
        $names = [];
        foreach ($data['content']['days'] as $dayData) {
            if ($dayData['date'] == $day->date->format('d.m.Y')) $names[] = $dayData['title'];
        }
        return join(' / ', $names);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date|date_format:d.m.Y',
        ]);

        $day = Day::find($id);

        // if no churches are left for a limited day, the record will be deleted
        if (($request->get('day_type') == Day::DAY_TYPE_LIMITED) && (count($request->get('cities') ?: []))) {
            $date = $day->date;
            $day->delete();
            return redirect()->route('calendar', ['year' => $date->year, 'month' => $date->month]);
        }

        // else update the existing record
        $day->date = Carbon::createFromFormat('d.m.Y', $request->get('date'))->setTime(0,0,0);
        $day->name = $request->get('name') ?: $this->getLiturgicalFunction($day);
        $day->description = $request->get('description') ?: '';
        $day->day_type = $request->get('day_type') ?: 0;
        $day->save();
        if ($request->get('day_type') == Day::DAY_TYPE_LIMITED) {
            $day->cities()->sync($request->get('cities') ?: []);
        }
        return redirect()->route('calendar', ['year' => $day->date->year, 'month' => $day->date->month])
            ->with('success', 'Die Änderungen wurden gespeichert.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $day = Day::find($id);
        /** @var Carbon $date */
        $date = $day->date;

        // limited and visible for other churches?
        if ($day->day_type == Day::DAY_TYPE_LIMITED) {
            foreach (Auth::user()->cities as $city) {
                if ($day->cities->contains($city)) $day->cities()->detach($city->id);
            }
            if (count($day->cities)) {
                $day->save();
            } else {
                $day->delete();
            }
        } else {
            $day->delete();
        }


        return redirect()->route('calendar', ['year' => $date->year, 'month' => $date->month])
            ->with('success', 'Der '.$date->format('d.m.Y').' wurde aus der Liste entfernt');
    }


    public function add($year, $month) {
        if ((!$year) || (!$month) || (!is_numeric($month)) || (!is_numeric($year)) || (!checkdate($month, 1, $year))) {
            return redirect()->route('calendar', ['year' => date('Y'), 'month' => date('m')]);
        }
        $cities = City::all();
        return view('days.create', ['year' => $year, 'month' => $month, 'cities' => $cities]);
    }
}
