<?php

namespace App\Http\Controllers;

use App\City;
use App\Day;
use App\Location;
use App\Mail\ServiceCreated;
use App\Mail\ServiceUpdated;
use App\Service;
use App\ServiceGroup;
use App\Subscription;
use App\Tag;
use App\User;
use App\Vacations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

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

        $tags = $request->get('tags') ?: [];
        $service->tags()->sync($tags);

        $serviceGroups = $request->get('serviceGroups') ?: [];
        $service->serviceGroups()->sync(ServiceGroup::createIfMissing($serviceGroups));

        //$service->location_id = $location->id;
        //$service->day_id = $day->id;
        //$service->notifyOfCreation(Auth::user(), '%s hat soeben folgenden Gottesdienst angelegt:');

        // notify:
        // (needs to happen before save, so the model is still dirty
        Subscription::send($service, ServiceCreated::class);

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
        $tags = Tag::all();
        $serviceGroups = ServiceGroup::all();

        $backRoute = $request->get('back') ?: '';

        return view('services.edit', compact('service', 'days', 'locations', 'users', 'tab', 'backRoute', 'tags', 'serviceGroups'));
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
        foreach (['P', 'O', 'M', 'A'] as $key) {
            $originalParticipants[$key] = $original->participantsText($key);
        }

        // participants first
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
        $service->save();


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

        $tags = $request->get('tags') ?: [];
        $service->tags()->sync($tags);

        $serviceGroups = $request->get('serviceGroups') ?: [];
        $service->serviceGroups()->sync(ServiceGroup::createIfMissing($serviceGroups));

        // notify:
        // (needs to happen before save, so the model is still dirty
        Subscription::send($service, ServiceUpdated::class, compact('original', 'originalParticipants'));

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
        $tags = Tag::all();
        $serviceGroups = ServiceGroup::all();

        return view('services.create', compact('day', 'city', 'days', 'locations', 'users', 'tags', 'serviceGroups'));
    }

    public function servicesByCityAndDay($cityId, $dayId) {
        $day = Day::find($dayId);
        $city = City::find($cityId);
        $vacations = [$dayId => Vacations::getByDay($day)];
        $services = Service::with('day', 'location')
            ->where('city_id', $cityId)
            ->where('day_id', '=', $dayId)
            ->orderBy('time')
            ->get();
        return view('services.ajax.byCityAndDay', compact('services', 'day', 'vacations', 'city'));
    }

    /**
     * Export a service as ical
     * @param $service Service
     */
    public function ical($service) {
        $services = [Service::findOrFail($service)];
        $raw = View::make('ical.ical', ['services' => $services, 'token' => null]);

        $raw = str_replace("\r\n\r\n", "\r\n", str_replace('@@@@', "\r\n", str_replace("\n", "\r\n", str_replace("\r\n", '@@@@', $raw))));
        return response($raw, 200)
            ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->header('Expires', '0')
            ->header('Content-Type', 'text/calendar')
            ->header('Content-Disposition', 'inline; filename='.$service.'.ics');
    }


    /**
     * Return timestamp of last update
     */
    public function lastUpdate() {
        $lastUpdated = Service::whereIn('city_id', Auth::user()->cities->pluck('id'))
            ->orderBy('updated_at', 'DESC')
            ->first();
        $lastCreated = Service::whereIn('city_id', Auth::user()->cities->pluck('id'))
            ->orderBy('created_at', 'DESC')
            ->first();

        if ($lastUpdated->updated_at > $lastCreated->created_at) {
            $service = $lastUpdated;
            $timestamp = $service->updated_at;
        } else {
            $service = $lastCreated;
            $timestamp = $service->created_at;
        }

        $update = $timestamp->setTimeZone('UTC')->format('Ymd\THis\Z');
        $route = route('calendar', ['year' => $service->day->date->format('Y'), 'month' => $service->day->date->format('m'), 'highlight' => $service->id, 'slave' => 1]);

        return response()->json(compact('route', 'update', 'service'));
    }
}
