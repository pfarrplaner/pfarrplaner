<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
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

use App\City;
use App\Day;
use App\Location;
use App\Service;
use App\Traits\HandlesAttachmentsTrait;
use App\Wedding;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Class WeddingController
 * @package App\Http\Controllers
 */
class WeddingController extends Controller
{

    use HandlesAttachmentsTrait;

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
     * @param int $serviceId Service Id
     * @return Response
     */
    public function create($serviceId)
    {
        $service = Service::find($serviceId);
        return view('weddings.create', compact('service'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'spouse1_name' => 'required',
                'spouse2_name' => 'required',
                'service' => 'required|integer',
            ]
        );

        $serviceId = $request->get('service');

        $wedding = new Wedding(
            [
                'service_id' => $serviceId,
                'spouse1_name' => $request->get('spouse1_name') ?: '',
                'spouse1_birth_name' => $request->get('spouse1_birth_name') ?: '',
                'spouse1_phone' => $request->get('spouse1_phone') ?: '',
                'spouse1_email' => $request->get('spouse1_email') ?: '',
                'spouse2_name' => $request->get('spouse2_name') ?: '',
                'spouse2_birth_name' => $request->get('spouse2_birth_name') ?: '',
                'spouse2_phone' => $request->get('spouse2_phone') ?: '',
                'spouse2_email' => $request->get('spouse2_email') ?: '',
                'text' => $request->get('text') ?: '',
                'registered' => $request->get('registered') ? 1 : 0,
                'registration_document' => $request->get('candidate_email') ?: '',
                'signed' => $request->get('signed') ? 1 : 0,
                'docs_ready' => $request->get('docs_ready') ? 1 : 0,
                'docs_where' => $request->get('docs_where') ?: '',
            ]
        );
        if ($request->get('appointment')) {
            $wedding->appointment = Carbon::createFromFormat('d.m.Y', $request->get('appointment'));
        }

        if ($request->hasFile('registration_document')) {
            $wedding->registration_document = $request->file('registration_document')->store('wedding', 'public');
        }

        $wedding->save();

        $wedding->service->setDefaultOfferingValues();
        $wedding->service->save();
        $this->handleAttachments($request, $wedding);


        // delayed notification after wizard completion:
        if ($request->get('wizard') == 1) {
            Subscription::send(Service::find($serviceId), ServiceCreated::class);
            Session::remove('wizard');
        }


        return redirect(route('services.edit', ['service' => $serviceId, 'tab' => 'rites']));
    }

    /**
     * Display the specified resource.
     *
     * @param Wedding $wedding
     * @return Response
     */
    public function show(Wedding $wedding)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Wedding $wedding
     * @return Response
     */
    public function edit(Wedding $wedding)
    {
        return view('weddings.edit', compact('wedding'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Wedding $wedding
     * @return Response
     */
    public function update(Request $request, Wedding $wedding)
    {
        $request->validate(
            [
                'spouse1_name' => 'required',
                'spouse2_name' => 'required',
                'service' => 'required|integer',
            ]
        );

        $serviceId = $request->get('service');
        $wedding->spouse1_name = $request->get('spouse1_name') ?: '';
        $wedding->spouse1_birth_name = $request->get('spouse1_birth_name') ?: '';
        $wedding->spouse1_phone = $request->get('spouse1_phone') ?: '';
        $wedding->spouse1_email = $request->get('spouse1_email') ?: '';
        $wedding->spouse2_name = $request->get('spouse2_name') ?: '';
        $wedding->spouse2_birth_name = $request->get('spouse2_birth_name') ?: '';
        $wedding->spouse2_phone = $request->get('spouse2_phone') ?: '';
        $wedding->spouse2_email = $request->get('spouse2_email') ?: '';
        if ($request->get('appointment')) {
            $wedding->appointment = Carbon::createFromFormat('d.m.Y', $request->get('appointment'));
        }
        $wedding->registered = $request->get('registered') ? 1 : 0;
        $wedding->signed = $request->get('signed') ? 1 : 0;
        $wedding->docs_ready = $request->get('docs_ready') ? 1 : 0;
        $wedding->docs_where = $request->get('docs_where') ?: '';

        if ($request->hasFile('registration_document') || ($request->get('removeAttachment') == 1)) {
            if ($wedding->registration_document != '') {
                Storage::delete($wedding->registration_document);
            }
            $wedding->registration_document = '';
        }
        if ($request->hasFile('registration_document')) {
            $wedding->registration_document = $request->file('registration_document')->store('baptism', 'public');
        }

        $wedding->save();

        $wedding->service->setDefaultOfferingValues();
        $wedding->service->save();
        $this->handleAttachments($request, $wedding);


        return redirect(route('services.edit', ['service' => $serviceId, 'tab' => 'rites']));
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Wedding $wedding
     * @return Response
     */
    public function destroy(Wedding $wedding)
    {
        $serviceId = $wedding->service_id;
        $wedding->delete();
        return redirect(route('services.edit', ['service' => $serviceId, 'tab' => 'rites']));
    }


    /**
     * Wedding wizard, step 1: select city and date
     * @param Request $request
     * @return Factory|View
     */
    public function wizardStep1(Request $request)
    {
        $cities = Auth::user()->writableCities;
        return view('weddings.wizard.step1', compact('cities'));
    }


    /**
     * Wedding wizard, step 2: create day (if necessary), then ask for service creation
     * @param Request $request
     * @return Factory|View
     */
    public function wizardStep2(Request $request)
    {
        $request->validate(
            [
                'date' => 'required|date|date_format:d.m.Y',
                'city' => 'required|integer',
            ]
        );

        $cityId = $request->get('city');
        $date = Carbon::createFromFormat('d.m.Y', $request->get('date'))->setTime(0, 0, 0);

        $city = City::find($cityId);

        // check if day exists
        $day = Day::where('date', $date)->first();
        if ($day) {
            // check if it visible for this city
            if ($day->day_type == Day::DAY_TYPE_LIMITED) {
                if (!$day->cities->contains($city)) {
                    $day->cities()->attach($city);
                }
            }
        } else {
            // create day
            $day = new Day(
                [
                    'date' => $date,
                    'name' => '',
                    'description' => '',
                    'day_type' => Day::DAY_TYPE_LIMITED,
                ]
            );
            $day->save();
            $day->cities()->sync([$cityId]);
        }

        $locations = Location::where('city_id', $cityId)->get();

        return view('weddings.wizard.step2', compact('day', 'city', 'locations'));
    }


    /**
     * Wedding wizard, step 3: create service, then redirect to enter wedding details
     * @param Request $request
     * @return RedirectResponse|Redirector
     */
    public function wizardStep3(Request $request)
    {
        $request->validate(
            [
                'day' => 'required|integer',
                'city' => 'required|integer',
            ]
        );

        $city = City::find($request->get('city'));
        $day = Day::find($request->get('day'));

        if ($specialLocation = ($request->get('special_location') ?: '')) {
            $locationId = 0;
            $time = $request->get('time') ?: '';
            $ccLocation = $request->get('cc_location') ?: '';
        } else {
            $locationId = $request->get('location_id') ?: 0;
            if ($locationId) {
                $location = Location::find($locationId);
                $time = $request->get('time') ?: $location->default_time;
                $ccLocation = $request->get('cc_location') ?: ($request->get(
                    'cc'
                ) ? $location->cc_default_location : '');
            } else {
                $time = $request->get('time') ?: '';
                $ccLocation = $request->get('cc_location') ?: '';
            }
        }

        // create the service
        $service = new Service(
            [
                'day_id' => $day->id,
                'location_id' => $locationId,
                'time' => $time,
                'special_location' => $specialLocation,
                'city_id' => $city->id,
                'others' => '',
                'description' => '',
                'need_predicant' => 0,
                'baptism' => 0,
                'eucharist' => 0,
                'offerings_counter1' => '',
                'offerings_counter2' => '',
                'offering_goal' => '',
                'offering_description' => '',
                'offering_type' => '',
                'cc' => 0,
                'cc_location' => '',
                'cc_lesson' => '',
                'cc_staff' => '',
            ]
        );
        $service->save();
        $service->pastors()->sync([Auth::user()->id => ['category' => 'P']]);
        Session::flash('wizard', 1);

        return redirect(route('wedding.add', compact('service')));
    }


    /**
     * @param Wedding $wedding
     * @return false|string
     */
    public function done(Wedding $wedding)
    {
        $wedding->done = true;
        $wedding->save();
        return json_encode(true);
    }

}
