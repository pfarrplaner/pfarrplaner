<?php

namespace App\Http\Controllers;

use App\City;
use App\Day;
use App\Location;
use App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(Service::class, 'service');

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
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'day_id' => 'required|integer',
        ]);//

        if ($specialLocation = ($request->get('special_location') ?: '')) {
            $locationId = 0;
            $time = $request->get('time') ?: '';
            $ccLocation = $request->get('cc_location') ?: '';
        } else {
            $locationId = $request->get('location_id') ?: 0;
            if ($locationId) {
                $location = Location::find($locationId);
                $time = $request->get('time') ?: $location->default_time;
                $ccLocation = $request->get('cc_location') ?: ($request->get('cc') ? $location->cc_default_location : '');
            } else {
                $time = $request->get('time') ?: '';
                $ccLocation = $request->get('cc_location') ?: '';
            }
        }

        $day = Day::find($request->get('day_id'));

        $service = new Service([
            'day_id' => $day->id,
            'location_id' => $locationId,
            'time' => $time,
            'pastor' => $request->get('pastor') ?: '',
            'organist' => $request->get('organist') ?: '',
            'sacristan' => $request->get('sacristan') ?: '',
            'description' => $request->get('description') ?: '',
            'special_location' => $specialLocation,
            'city_id' => $request->get('city_id'),
            'need_predicant' => $request->get('need_predicant') ? 1 : 0,
            'baptism' => $request->get('baptism') ? 1 : 0,
            'eucharist' => $request->get('eucharist') ? 1 : 0,
            'offerings_counter1' => $request->get('offerings_counter1') ?: '',
            'offerings_counter2' => $request->get('offerings_counter2') ?: '',
            'offering_goal' => $request->get('offering_goal') ?: '',
            'offering_description' => $request->get('offering_description') ?: '',
            'offering_type' => $request->get('offering_type') ?: '',
            'others' => $request->get('others') ?: '',
            'cc' => $request->get('cc') ? 1: 0,
            'cc_location' => $ccLocation,
            'cc_lesson' => $request->get('cc_lesson') ?: '',
            'cc_staff' => $request->get('cc_staff') ?: '',
        ]);
        //$service->location_id = $location->id;
        //$service->day_id = $day->id;
        $service->save();
        $service->notifyOfCreation(Auth::user(), '%s hat soeben folgenden Gottesdienst angelegt:');

        return redirect()->route('calendar', ['year' => $day->date->year, 'month' => $day->date->month])
            ->with('success', 'Der Gottesdienst wurde hinzugefügt');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $service = Service::find($id);
        $days = Day::orderBy('date', 'ASC')->get();
        $locations = Location::where('city_id', '=', $service->city_id)->get();
        return view('services.edit', ['service' => $service, 'days' => $days, 'locations' => $locations]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'day_id' => 'required|integer',
        ]);//

        $service = Service::find($id);
        $day = Day::find($request->get('day_id'));

        if ($specialLocation = ($request->get('special_location') ?: '')) {
            $locationId = 0;
            $time = $request->get('time') ?: '';
            $ccLocation = $request->get('cc_location') ?: '';
        } else {
            $locationId = $request->get('location_id') ?: 0;
            if ($locationId) {
                $location = Location::find($locationId);
                $ccLocation = $request->get('cc_location') ?: ($request->get('cc') ? $location->cc_default_location : '');
                $time = $request->get('time') ?: $location->default_time;
            } else {
                $time = $request->get('time') ?: '';
                $ccLocation = $request->get('cc_location') ?: '';
            }
        }

        $service->day_id = $day->id;
        $service->location_id = $locationId;
        $service->time = $time;
        $service->pastor = $request->get('pastor') ?: '';
        $service->organist = $request->get('organist') ?: '';
        $service->sacristan = $request->get('sacristan') ?: '';
        $service->description = $request->get('description') ?: '';
        $service->city_id = $request->get('city_id');
        $service->special_location = $specialLocation;
        $service->need_predicant = $request->get('need_predicant') ? 1 : 0;
        $service->baptism = $request->get('baptism') ? 1 : 0;
        $service->eucharist = $request->get('eucharist') ? 1 : 0;
        $service->offerings_counter1 = $request->get('offerings_counter1') ?: '';
        $service->offerings_counter2 = $request->get('offerings_counter2') ?: '';
        $service->offering_goal = $request->get('offering_goal') ?: '';
        $service->offering_description = $request->get('offering_description') ?: '';
        $service->offering_type = $request->get('offering_type') ?: '';
        $service->others = $request->get('others') ?: '';
        $service->cc = $request->get('cc') ? 1 : 0;
        $service->cc_location = $ccLocation;
        $service->cc_lesson = $request->get('cc_lesson') ?: '';
        $service->cc_staff = $request->get('cc_staff') ?: '';

        $service->notifyOfChanges(Auth::user(), '%s hat soeben folgende Änderungen an einem Gottesdienst vorgenommen');
        $service->save();

        return redirect()->route('calendar', ['year' => $day->date->year, 'month' => $day->date->month])
            ->with('success', 'Der Gottesdienst wurde mit geänderten Angaben gespeichert.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $service = Service::find($id);
        $day = Day::find($service->day_id);
        $service->delete();
        return redirect()->route('calendar', ['year' => $day->date->year, 'month' => $day->date->month])
            ->with('success', 'Der Gottesdiensteintrag wurde gelöscht.');
    }

    public function add($date, $city)
    {
        $day = Day::find($date);
        $city = City::find($city);

        $days = Day::orderBy('date', 'ASC')->get();

        $locations = Location::where('city_id', '=', $city->id)->get();

        return view('services.create', ['day' => $day, 'city' => $city, 'days' => $days, 'locations' => $locations]);
    }
}
