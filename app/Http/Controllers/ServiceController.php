<?php

namespace App\Http\Controllers;

use App\City;
use App\Day;
use App\Location;
use App\Mail\ServiceCreated;
use App\Mail\ServiceUpdated;
use App\Service;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ServiceController extends Controller
{

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
            'special_location' => $specialLocation,
            'city_id' => $request->get('city_id'),
            'pastor' => '',
            'organist' => '',
            'sacristan' => '',
            'others' => '',
            'description' => $request->get('description') ?: '',
            'need_predicant' => $request->get('need_predicant') ? 1 : 0,
            'baptism' => $request->get('baptism') ? 1 : 0,
            'eucharist' => $request->get('eucharist') ? 1 : 0,
            'offerings_counter1' => $request->get('offerings_counter1') ?: '',
            'offerings_counter2' => $request->get('offerings_counter2') ?: '',
            'offering_goal' => $request->get('offering_goal') ?: '',
            'offering_description' => $request->get('offering_description') ?: '',
            'offering_type' => $request->get('offering_type') ?: '',
            'cc' => $request->get('cc') ? 1: 0,
            'cc_location' => $ccLocation,
            'cc_lesson' => $request->get('cc_lesson') ?: '',
            'cc_staff' => $request->get('cc_staff') ?: '',
        ]);

        $service->save();

        // participants:
        $participants = [];
        foreach (($request->get('participants') ?: []) as $category => $participantList) {
            foreach ($participantList as $participant) {
                if ((!is_numeric($participant)) || (User::find($participant) === false)) {
                    $user = new User([
                        'name' => $participant,
                        'office' => '',
                        'phone' => '',
                        'address' => '',
                        'preference_cities' => '',
                        'first_name' => '',
                        'last_name' => '',
                        'title' => '',
                    ]);
                    $user->save();
                    $participant = $user->id;
                }
                $participants[$category][$participant] = ['category' => $category];
            }
        }
        $service->pastors()->sync(isset($participants['P']) ? $participants['P'] : []);
        $service->organists()->sync(isset($participants['O']) ? $participants['O'] : []);
        $service->sacristans()->sync(isset($participants['M']) ? $participants['M'] : []);
        $service->otherParticipants()->sync(isset($participants['A']) ? $participants['A'] : []);


        //$service->location_id = $location->id;
        //$service->day_id = $day->id;
        //$service->notifyOfCreation(Auth::user(), '%s hat soeben folgenden Gottesdienst angelegt:');

        // notify:
        // (needs to happen before save, so the model is still dirty
        $subscribers = User::whereHas('cities', function($query) use ($service) {
            $query->where('city_id', $service->city_id);
        })->where('notifications', 1)
            ->get();

        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber)->send(new ServiceCreated($service, $subscriber));
        }

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
     * @param Request $request
     * @param  int $id
     * @param  string $tab optional tab name
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id, $tab = 'home')
    {

        $service = Service::find($id);
        $service->load(['day', 'location', 'comments', 'baptisms', 'funerals', 'weddings']);


        $days = Day::orderBy('date', 'ASC')->get();
        $locations = Location::where('city_id', '=', $service->city_id)->get();
        $users = User::all()->sortBy('name');

        $backRoute = $request->get('back') ?: '';

        return view('services.edit', compact('service', 'days', 'locations', 'users', 'tab', 'backRoute'));
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
        $original = clone $service;

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

        // participants:
        $participants = [];
        foreach (($request->get('participants') ?: []) as $category => $participantList) {
            foreach ($participantList as $participant) {
                if ((!is_numeric($participant)) || (User::find($participant) === false)) {
                    $user = new User([
                        'name' => $participant,
                        'office' => '',
                        'phone' => '',
                        'address' => '',
                        'preference_cities' => '',
                        'first_name' => '',
                        'last_name' => '',
                        'title' => '',
                    ]);
                    $user->save();
                    $participant = $user->id;
                }
                $participants[$category][$participant] = ['category' => $category];
            }
        }
        $service->pastors()->sync(isset($participants['P']) ? $participants['P'] : []);
        $service->organists()->sync(isset($participants['O']) ? $participants['O'] : []);
        $service->sacristans()->sync(isset($participants['M']) ? $participants['M'] : []);
        $service->otherParticipants()->sync(isset($participants['A']) ? $participants['A'] : []);

        // notify:
        // (needs to happen before save, so the model is still dirty
        $subscribers = User::whereHas('cities', function($query) use ($service) {
            $query->where('city_id', $service->city_id);
        })->where('notifications', 1)
            ->get();

        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber)->send(new ServiceUpdated($service, $original, $subscriber));
        }



        $service->save();

        $route = $request->get('routeBack') ?: '';
        if ($route) {
            return redirect($route)->with('success', 'Der Gottesdienst wurde mit geänderten Angaben gespeichert.');
        } else {
            // default: redirect to calendar
            return redirect()->route('calendar', ['year' => $day->date->year, 'month' => $day->date->month])
                ->with('success', 'Der Gottesdienst wurde mit geänderten Angaben gespeichert.');

        }
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
        $users = User::all()->sortBy('name');

        return view('services.create', compact('day', 'city', 'days', 'locations', 'users'));
    }
}
