<?php

namespace App\Http\Controllers;

use App\Day;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        $day = new Day([
            'date' => Carbon::createFromFormat('d.m.Y', $request->get('date')),
            'name' => $request->get('name') ?: '',
            'description' => $request->get('description') ?: '',
        ]);
        if ($day->name == '') $day->name = $this->getLiturgicalFunction($day);
        $day->save();
        $today = Carbon::createFromFormat('d.m.Y', $request->get('date'));
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
        return view('days.edit', compact('day'));
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
        $day->date = Carbon::createFromFormat('d.m.Y', $request->get('date'));
        $day->name = $request->get('name') ?: $this->getLiturgicalFunction($day);
        $day->description = $request->get('description') ?: '';
        $day->save();
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
        $day->delete();
        return redirect()->route('calendar', ['year' => $date->year, 'month' => $date->month])
            ->with('success', 'Der '.$date->format('d.m.Y').' wurde aus der Liste entfernt');
    }


    public function add($year, $month) {
        if ((!$year) || (!$month) || (!is_numeric($month)) || (!is_numeric($year)) || (!checkdate($month, 1, $year))) {
            return redirect()->route('calendar', ['year' => date('Y'), 'month' => date('m')]);
        }
        return view('days.create', ['year' => $year, 'month' => $month]);
    }
}
