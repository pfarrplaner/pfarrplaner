<?php

namespace App\Http\Controllers;

use App\City;
use App\Day;
use App\Liturgy;
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
        $data = $this->validateRequest();

        // check if day already exists in limited form for other churches...
        // ...if so, only attach new cities
        $day = Day::existsForDate($data['date']);
        if (false !== $day) {
            $day->update($data);
        } else {
            // ...if not, create a new day record
            $day = Day::create($data);
        }

        if (count($data['cities'])) {
            foreach ($data['cities'] as $city) $day->cities()->attach($city);
        }

        return redirect()->route('calendar', ['year' => $day->date->year, 'month' => $day->date->month])
            ->with('success', $day->date->format('d.m.Y').' wurde der Liste hinzugefügt.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Day $day
     * @return \Illuminate\Http\Response
     */
    public function show(Day $day)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Day $day
     * @return \Illuminate\Http\Response
     */
    public function edit(Day $day)
    {
        $cities = City::all();
        return view('days.edit', compact('day', 'cities'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Day $day
     * @return \Illuminate\Http\Response
     */
    public function update(Day $day)
    {
        $data = $this->validateRequest();


        $day->update($data);
        $day->cities()->sync($data['cities']);
        $date = $day->date;
        if (($data['day_type'] == Day::DAY_TYPE_LIMITED) && (count($data['cities']) == 0)) $day->delete();

        return redirect()->route('calendar', ['year' => $date->year, 'month' => $date->month])
            ->with('success', 'Die Änderungen wurden gespeichert.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Day $day
     * @return \Illuminate\Http\Response
     */
    public function destroy(Day $day)
    {
        $date = $day->date;

        // limited and visible for other churches?
        if ($day->day_type == Day::DAY_TYPE_LIMITED) {
            foreach (Auth::user()->cities as $city) {
                if ($day->cities->contains($city)) $day->cities()->detach($city->id);
            }
            if (count($day->cities) == 0) {
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

    /**
     * Validate and format data
     * @return array Validated data
     */
    protected function validateRequest(): array
    {
        $data = request()->validate([
            'date' => 'required|date|date_format:d.m.Y',
            'name' => 'nullable',
            'description' => 'nullable',
            'day_type' => 'nullable|int|in:0,1',
            'cities.*' => 'nullable|int|exists:cities,id',
        ]);
        $data['day_type'] = $data['day_type'] ?? Day::DAY_TYPE_DEFAULT;
        $data['cities'] = $data['cities'] ?? [];
        $data['date'] = Carbon::createFromFormat('d.m.Y', $data['date'])->format('Y-m-d');
        $data['name'] = $data['name'] ?? Liturgy::getDayInfo($data['date'])['title'] ?? '';
        $data['description'] = $data['description'] ?? '';
        return $data;
    }
}
