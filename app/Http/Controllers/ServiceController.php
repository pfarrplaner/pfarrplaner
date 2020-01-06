<?php

namespace App\Http\Controllers;

use App\City;
use App\Day;
use App\Http\Requests\StoreServiceRequest;
use App\Location;
use App\Mail\ServiceCreated;
use App\Mail\ServiceUpdated;
use App\Participant;
use App\Service;
use App\ServiceGroup;
use App\Subscription;
use App\Tag;
use App\User;
use App\Vacations;
use Carbon\Carbon;
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
     * @param  StoreServiceRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreServiceRequest $request)
    {

        $service = new Service($request->all());
        $service->setTimeAndPlaceFromRequest($request);
        $service->setDefaultOfferingValues();
        $service->save();

        $this->updateFromRequest($request, $service);

        // notify:
        Subscription::send($service, ServiceCreated::class);

        return redirect()->route('calendar', ['year' => $service->day->date->year, 'month' => $service->day->date->month])
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

        $service = Service::with(['day', 'location', 'comments', 'baptisms', 'funerals', 'weddings'])->find($id);

        $ministries = Participant::all()
            ->pluck('category')
            ->unique()
            ->reject(function($value, $key){
                return in_array($value, ['P', 'O', 'M', 'A']);
            });

        $days = Day::orderBy('date', 'ASC')->get();
        $locations = Location::whereIn('city_id', Auth::user()->cities->pluck('id'))->get();
        $users = User::all()->sortBy('name');
        $tags = Tag::all();
        $serviceGroups = ServiceGroup::all();

        $backRoute = $request->get('back') ?: '';

        return view('services.edit', compact('service', 'days', 'locations', 'users', 'tab', 'backRoute', 'tags', 'serviceGroups', 'ministries'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  StoreServiceRequest $request
     * @param  Service $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreServiceRequest $request, Service $service)
    {
        $original = clone $service;
        foreach (['P', 'O', 'M', 'A'] as $key) {
            $originalParticipants[$key] = $original->participantsText($key);
        }

        $service->fill($request->all());
        $service->setTimeAndPlaceFromRequest($request);
        $service->setDefaultOfferingValues();

        $this->updateFromRequest($request, $service);

        // notify:
        // (needs to happen before save, so the model is still dirty
        Subscription::send($service, ServiceUpdated::class, compact('original', 'originalParticipants'));

        $service->save();

        $route = $request->get('routeBack') ?: '';
        if ($route) {
            return redirect($route)->with('success', 'Der Gottesdienst wurde mit geänderten Angaben gespeichert.');
        } else {
            // default: redirect to calendar
            return redirect()->route('calendar', ['year' => $service->day->date->year, 'month' => $service->day->date->month])
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

        $ministries = Participant::all()
            ->pluck('category')
            ->unique()
            ->reject(function($value, $key){
                return in_array($value, ['P', 'O', 'M', 'A']);
            });



        $locations = Location::whereIn('city_id', Auth::user()->cities->pluck('id'))->get();
        $users = User::all()->sortBy('name');
        $tags = Tag::all();
        $serviceGroups = ServiceGroup::all();

        return view('services.create', compact('day', 'city', 'days', 'locations', 'users', 'tags', 'serviceGroups', 'ministries'));
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

        $route = route('calendar', ['year' => $service->day->date->format('Y'), 'month' => $service->day->date->format('m'), 'highlight' => $service->id, 'slave' => 1]);

        // tie in automatic month switching
        $timestamp2 = Carbon::createFromTimestamp(Auth::user()->getSetting('display-timestamp', 0));
        if ($timestamp2 > $timestamp) {
            $timestamp = $timestamp2;
            $route = route('calendar', ['year' => Auth::user()->getSetting('display-year', date('Y')), 'month' => Auth::user()->getSetting('display-month', date('m')), 'slave' => 1]);
        }

        $update = $timestamp->setTimeZone('UTC')->format('Ymd\THis\Z');

        return response()->json(compact('route', 'update', 'service'));
    }

    /**
     * @param StoreServiceRequest $request
     * @param Service $service
     */
    protected function updateFromRequest(StoreServiceRequest $request, Service $service): void
    {
        $service->associateParticipants($request, $service);
        $service->checkIfPredicantNeeded();

        $service->tags()->sync($request->get('tags') ?: []);
        $service->serviceGroups()->sync(ServiceGroup::createIfMissing($request->get('serviceGroups') ?: []));
    }
}
