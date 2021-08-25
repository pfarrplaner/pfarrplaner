<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/pfarrplaner/pfarrplaner
 * @version git: $Id$
 *
 * Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
 *
 * Pfarrplaner is based on the Laravel framework (https://laravel.com).
 * This file may contain code created by Laravel's scaffolding functions.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App\Http\Controllers;

use App\Attachment;
use App\Broadcast;
use App\City;
use App\Day;
use App\Events\ServiceBeforeDelete;
use App\Events\ServiceBeforeStore;
use App\Events\ServiceBeforeUpdate;
use App\Events\ServiceCreated;
use App\Events\ServiceUpdated;
use App\Http\Requests\ServiceRequest;
use App\Liturgy\LiturgySheets\LiturgySheets;
use App\Location;
use App\Service;
use App\ServiceGroup;
use App\Services\RedirectorService;
use App\Tag;
use App\Traits\HandlesAttachmentsTrait;
use App\User;
use App\Vacations;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;

/**
 * Class ServiceController
 * @package App\Http\Controllers
 */
class ServiceController extends Controller
{

    use HandlesAttachmentsTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ServiceRequest $request
     * @return Response
     */
    public function store(ServiceRequest $request)
    {
        $validatedData = $request->validated();
        $service = new Service($validatedData);

        // emit event so that integrations may react
        event(new ServiceBeforeStore($service, $validatedData));

        $service->setDefaultOfferingValues();
        $service->save();
        $this->updateFromRequest($request, $service);
        $this->handleAttachments($request, $service);
        $this->handleIndividualAttachment($request, $service, 'songsheet');
        $this->handleIndividualAttachment($request, $service, 'sermon_image');

        // emit event so that integrations may react
        event(new ServiceCreated($service));

        return redirect()->route('calendar', $service->day->date->format('Y-m'))
            ->with('success', 'Der Gottesdienst wurde hinzugefügt');
    }

    /**
     * @param ServiceRequest $request
     * @param Service $service
     */
    protected function updateFromRequest(ServiceRequest $request, Service $service): void
    {
        $service->associateParticipants($request, $service);
        $service->checkIfPredicantNeeded();

        $service->tags()->sync($request->get('tags') ?: []);
        $service->serviceGroups()->sync(ServiceGroup::createIfMissing($request->get('serviceGroups') ?: []));
    }

    /**
     * Display the specified resource.
     *
     * @param Service $service
     * @return Response
     */
    public function show(Service $service)
    {
        return redirect()->route('service.edit', $service);
    }


    public function update2(ServiceRequest $request, Service $service)
    {
        $data = $request->validated();
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param Service $service
     * @param string $tab optional tab name
     * @return Response
     */
    public function edit(Request $request, Service $service, $tab = 'home')
    {
        $tab = $request->get('tab', 'home');
        $service->load(['attachments', 'comments', 'bookings', 'liturgyBlocks', 'tags', 'serviceGroups']);
        $service->setAppends(
            [
                'pastors',
                'organists',
                'sacristans',
                'otherParticipants',
                'descriptionText',
                'locationText',
                'dateText',
                'timeText',
                'baptismsText',
                'descriptionText',
                'liturgy',
                'ministriesByCategory',
                'isShowable',
                'isEditable',
                'isDeletable',
                'isMine',
                'titleText',
                'liveDashboardUrl',
                'datetime',
                'seating',
                'remainingCapacity',
                'maximumCapacity',
                'freeSeatsText',
                'hasSeats',

            ]
        );

        $days = Day::select(['id', 'date'])->visibleForCities(collect($service->city))
            ->orderByDesc('date')->get()->makeHidden(['liturgy'])->toArray();

        $ministries = DB::table('service_user')->select('category')->distinct()->get();
        $locations = Location::whereIn('city_id', Auth::user()->cities->pluck('id'))->get();
        $liturgySheets = LiturgySheets::all();

        $users = User::all();

        $backRoute = RedirectorService::backRoute();

        $tags = Tag::all();
        $serviceGroups = ServiceGroup::all();
        return Inertia::render(
            'serviceEditor',
            compact(
                'service',
                'locations',
                'users',
                'tab',
                'tags',
                'serviceGroups',
                'ministries',
                'days',
                'liturgySheets',
                'backRoute',
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ServiceRequest $request
     * @param Service $service
     * @return Response
     */
    public function update(ServiceRequest $request, Service $service)
    {
        $service->trackChanges();
        $originalParticipants = $service->participants;

        $validatedData = $request->validated();

        // emit event, so that integrations can react to the intended update
        event(new ServiceBeforeUpdate($service, $validatedData));

        $service->fill($validatedData);
        $service->setDefaultOfferingValues();
        $this->updateFromRequest($request, $service);
        $service->save();
        $this->handleAttachments($request, $service);
        $this->handleIndividualAttachment($request, $service, 'songsheet');
        $this->handleIndividualAttachment($request, $service, 'sermon_image');

        $success = '';
        if ($service->isChanged()) {
            $service->storeDiff();
            event(new ServiceUpdated($service, $originalParticipants));
            $success = 'Der Gottesdienst wurde mit geänderten Angaben gespeichert.';

            // update YouTube as well (but only if there's a connected account for this city
            if (($service->youtube_url != '') && ($service->city->google_access_token != '')) {
                Broadcast::get($service)->update();
                $success .= ' Diese wurden automatisch auch auf YouTube aktualisiert.';
            }
        }

        $service->refresh();

        $closeAfterSaving = $request->get('closeAfterSaving', 1);
        if ($closeAfterSaving) {
            $route = $request->get('routeBack') ?: '';
            if ($route) {
                return redirect($route)->with('success', $success);
            } else {
                return RedirectorService::back();
            }
        } else {
            return redirect()->route('service.edit', $service->id);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Service $service
     * @return Response
     */
    public function destroy(Service $service)
    {
        $day = Day::find($service->day_id);

        // emit event so that integrations may react to impending delete
        event(new ServiceBeforeDelete($service));

        $service->delete();
        return redirect()->route('calendar', $day->date->format('Y-m'))
            ->with('success', 'Der Gottesdiensteintrag wurde gelöscht.');
    }

    /**
     * @param $date
     * @param $city
     * @return Application|Factory|\Illuminate\View\View
     */
    public function add($date, City $city)
    {
        $day = Day::find($date);
        $service = Service::create(
            [
                'city_id' => $city->id,
                'day_id' => $day->id,
                'location_id' => $city->locations->first()->id,
            ]
        );
        return redirect()->route('service.edit', $service);
    }

    /**
     * @param $cityId
     * @param $dayId
     * @return Application|Factory|\Illuminate\View\View
     */
    public function servicesByCityAndDay($cityId, $dayId)
    {
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
    public function ical($service)
    {
        $services = [Service::findOrFail($service)];
        $raw = View::make('ical.ical', ['services' => $services, 'token' => null]);

        $raw = str_replace(
            "\r\n\r\n",
            "\r\n",
            str_replace('@@@@', "\r\n", str_replace("\n", "\r\n", str_replace("\r\n", '@@@@', $raw)))
        );
        return response($raw, 200)
            ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->header('Expires', '0')
            ->header('Content-Type', 'text/calendar')
            ->header('Content-Disposition', 'inline; filename=' . $service . '.ics');
    }

    /**
     * Return timestamp of last update
     */
    public function lastUpdate()
    {
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

        $route = route(
            'calendar',
            [
                'year' => $service->day->date->format('Y'),
                'month' => $service->day->date->format('m'),
                'highlight' => $service->id,
                'slave' => 1
            ]
        );

        // tie in automatic month switching
        $timestamp2 = Carbon::createFromTimestamp(Auth::user()->getSetting('display-timestamp', 0));
        if ($timestamp2 > $timestamp) {
            $timestamp = $timestamp2;
            $route = route(
                'calendar',
                [
                    'year' => Auth::user()->getSetting('display-year', date('Y')),
                    'month' => Auth::user()->getSetting('display-month', date('m')),
                    'slave' => 1
                ]
            );
        }

        $update = $timestamp->setTimeZone('UTC')->format('Ymd\THis\Z');

        return response()->json(compact('route', 'update', 'service'));
    }

    /**
     * @param Request $request
     * @param Service $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function attach(Request $request, Service $service)
    {
        $this->handleAttachments($request, $service);
        return response()->json($service->attachments);
    }

    /**
     * @param Request $request
     * @param Service $service
     * @param Attachment $attachment
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function detach(Request $request, Service $service, Attachment $attachment)
    {
        Storage::delete($attachment->file);
        $service->attachments()->where('id', $attachment->id)->delete();
        $attachment->delete();
        return response()->json($service->attachments);
    }
}
