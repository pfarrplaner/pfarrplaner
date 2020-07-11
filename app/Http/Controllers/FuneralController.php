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
use App\Funeral;
use App\Http\Requests\FuneralStoreRequest;
use App\Location;
use App\Mail\ServiceCreated;
use App\Service;
use App\Subscription;
use App\Traits\HandlesAttachmentsTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use PDF;

/**
 * Class FuneralController
 * @package App\Http\Controllers
 */
class FuneralController extends Controller
{

    use HandlesAttachmentsTrait;

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
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Service $service)
    {
        $wizard = Session::get('wizard', 0);
        return view('funerals.create', compact('service', 'wizard'));
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function wizardStep1(Request $request) {
        $cities = Auth::user()->writableCities;
        return view('funerals.wizard.step1', compact('cities'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function wizardStep2(Request $request) {
        $request->validate([
            'date' => 'required|date|date_format:d.m.Y',
            'city' => 'required|integer',
        ]);

        $cityId = $request->get('city');
        $date = Carbon::createFromFormat('d.m.Y', $request->get('date'))->setTime(0,0,0);

        $city = City::find($cityId);

        // check if day exists
        $day = Day::where('date', $date)->first();
        if ($day) {
            // check if it visible for this city
            if ($day->day_type == Day::DAY_TYPE_LIMITED) {
                if (!$day->cities->contains($city)) $day->cities()->attach($city);
            }
        } else {
            // create day
            $day = new Day([
               'date' => $date,
                'name' => '',
                'description' => '',
                'day_type' => Day::DAY_TYPE_LIMITED,
            ]);
            $day->save();
            $day->cities()->sync([$cityId]);
        }

        $locations = Location::where('city_id', $cityId)->get();

        return view('funerals.wizard.step2', compact('day', 'city', 'locations'));

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function wizardStep3(Request $request)
    {
        $request->validate([
            'day' => 'required|integer',
            'city' => 'required|integer',
        ]);

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
                $ccLocation = $request->get('cc_location') ?: ($request->get('cc') ? $location->cc_default_location : '');
            } else {
                $time = $request->get('time') ?: '';
                $ccLocation = $request->get('cc_location') ?: '';
            }
        }

        // create the service
        $service = Service::create([
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
        ]);
        if (Auth::user()->hasRole('Pfarrer*in')) {
            $service->pastors()->sync([Auth::user()->id => ['category' => 'P']]);
        }
        Session::flash('wizard', 1);

        return redirect(route('funeral.add', compact('service')));

    }

        /**
     * Store a newly created resource in storage.
     *
     * @param  FuneralStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FuneralStoreRequest $request)
    {
        $funeral = Funeral::create($request->validated());
        $funeral->service->setDefaultOfferingValues();
        $funeral->service->save();
        $this->handleAttachments($request, $funeral);

        // delayed notification after wizard completion:
        if ($request->get('wizard') == 1) {
            Subscription::send($funeral->service, ServiceCreated::class);
            Session::remove('wizard');
        }

        return redirect(route('services.edit', ['service' => $funeral->service->id, 'tab' => 'rites']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Funeral  $funeral
     * @return \Illuminate\Http\Response
     */
    public function show(Funeral $funeral)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Funeral  $funeral
     * @return \Illuminate\Http\Response
     */
    public function edit(Funeral $funeral)
    {
        $service = Service::find($funeral->service_id);
        return view('funerals.edit', compact('service', 'funeral'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  FuneralStoreRequest $request
     * @param  \App\Funeral  $funeral
     * @return \Illuminate\Http\Response
     */
    public function update(FuneralStoreRequest $request, Funeral $funeral)
    {
        $funeral->update($request->validated());
        $funeral->service->setDefaultOfferingValues();
        $funeral->service->save();
        $this->handleAttachments($request, $funeral);

        return redirect(route('services.edit', ['service' => $funeral->service->id, 'tab' => 'rites']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Funeral  $funeral
     * @return \Illuminate\Http\Response
     */
    public function destroy(Funeral $funeral)
    {
        $serviceId = $funeral->service_id;
        $funeral->delete();
        return redirect(route('services.edit', ['service' => $serviceId, 'tab' => 'rites']));
    }

    /**
     * Create a pdf form with funeral data
     * @param \App\Funeral $funeral
     * @return \Illuminate\Http\Response
     */
    public function pdfForm(Funeral $funeral) {
        $funeral->load('service');
        $funeral->service->load('day', 'location', 'city');
        $filename = $funeral->service->day->date->format('Ymd').' '.$funeral->buried_name.' KRA.pdf';

        $pdf = PDF::loadView('funerals.pdf.form', compact('funeral'), [], ['format' => 'A5', 'useActiveForms' => true]);


        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $filename .'"');
        header('Content-Type: application/pdf');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        return $pdf->download($filename);
    }


    /**
     * @param Funeral $funeral
     * @return false|string
     */
    public function done(Funeral $funeral) {
        $funeral->done = true;
        $funeral->save();
        return json_encode(true);
    }


    /**
     * @param Funeral $funeral
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function appointmentIcal(Funeral $funeral) {
        $service = Service::find($funeral->service_id);
        $raw = View::make('funerals.appointment.ical', compact('funeral', 'service'));
        $raw = str_replace("\r\n\r\n", "\r\n", str_replace('@@@@', "\r\n", str_replace("\n", "\r\n", str_replace("\r\n", '@@@@', $raw))));
        return response($raw, 200)
            ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->header('Expires', '0')
            ->header('Content-Type', 'text/calendar')
            ->header('Content-Disposition', 'inline; filename=Trauergespraech-'.$funeral->id.'.ics');
    }


}
