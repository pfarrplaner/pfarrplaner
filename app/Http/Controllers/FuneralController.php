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
use App\City;
use App\Day;
use App\Events\ServiceUpdated;
use App\Funeral;
use App\Http\Requests\FuneralStoreRequest;
use App\Liturgy\PronounSets\PronounSets;
use App\Location;
use App\Mail\ServiceCreated;
use App\Service;
use App\Subscription;
use App\Traits\HandlesAttachmentsTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
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
     * @return Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Service $service)
    {
        $funeral = Funeral::create(
            [
                'service_id' => $service->id,
                'buried_name' => '',
            ]
        );
        return redirect()->route('funerals.edit', $funeral->id);
    }


    /**
     * @param Request $request
     * @return \Inertia\Response
     */
    public function wizard(Request $request)
    {
        $cities = Auth::user()->writableCities;
        $locations = Location::whereIn('city_id', $cities->pluck('id'))->get();
        $people = User::all();
        $user = Auth::user();
        return Inertia::render('Rites/FuneralWizard', compact('cities', 'locations', 'people', 'user'));
    }

    public function wizardSave(Request $request)
    {
        $data = $request->validate(
            [
                'date' => 'required|date_format:d.m.Y H:i',
                'city' => 'required|exists:cities,id',
                'location' => 'required',
                'name' => 'required|string',
                'pastor' => 'nullable',
            ]
        );
        if (is_numeric($data['location'])) {
            $request->validate(['location' => 'exists:locations,id']);
        }
        $data['date'] = Carbon::createFromFormat('d.m.Y H:i', $data['date']);

        $city = City::find($data['city']);

        // check if day exists
        $day = Day::where('date', $data['date']->format('Y-m-d'))->first();
        if ($day) {
            // check if it visible for this city
            if ($day->day_type == Day::DAY_TYPE_LIMITED) {
                if (!$day->cities->contains($data['city'])) {
                    $day->cities()->attach($city);
                }
            }
        } else {
            // create day
            $day = new Day(
                [
                    'date' => $data['date'],
                    'name' => '',
                    'description' => '',
                    'day_type' => Day::DAY_TYPE_LIMITED,
                ]
            );
            $day->save();
            $day->cities()->sync([$data['city']]);
        }

        $location = $specialLocation = null;
        if ((!is_numeric($data['location'])) || (null === Location::find($data['location']))) {
            $specialLocation = $data['location'];
            $data['location'] = 0;
            $time = $data['date']->format('H:i');
            $ccLocation = $request->get('cc_location') ?: '';
        } else {
            if ($data['location']) {
                $location = Location::find($data['location']);
                $time = $data['date']->format('H:i');
                $ccLocation = $request->get('cc_location') ?: ($request->get(
                    'cc'
                ) ? $location->cc_default_location : '');
            } else {
                $time = $data['date']->format('H:i');
                $ccLocation = $request->get('cc_location') ?: '';
            }
        }

        $service = Service::create(
            [
                'day_id' => $day->id,
                'location_id' => ($location ? $location->id : null),
                'time' => $time,
                'special_location' => $specialLocation ?? null,
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
        $service->update(['slug' => $service->createSlug()]);
        if (!is_array($data['pastor'])) {
            if (Auth::user()->hasRole('Pfarrer*in')) {
                $service->pastors()->sync([Auth::user()->id => ['category' => 'P']]);
            }
        } else {
            $sync = [];
            foreach ($data['pastor'] as $person) {
                $sync[($person['id'] ?? $person)] = ['category' => 'P'];
            }
            $service->pastors()->sync($sync);
        }


        $funeral = Funeral::create(
            [
                'service_id' => $service->id,
                'buried_name' => $data['name']
            ]
        );

        return redirect()->route('funerals.edit', $funeral->id);
    }


    /**
     * Display the specified resource.
     *
     * @param Funeral $funeral
     * @return Response
     */
    public function show(Funeral $funeral)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Funeral $funeral
     * @return Response
     */
    public function edit(Funeral $funeral)
    {
        $funeral->load('service');
        $funeral->service->load('sermon');
        $pronounSets = PronounSets::toArray();
        return Inertia::render('Rites/FuneralEditor', compact('funeral', 'pronounSets'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param FuneralStoreRequest $request
     * @param Funeral $funeral
     * @return Response
     */
    public function update(FuneralStoreRequest $request, Funeral $funeral)
    {
        $funeral->update($request->validated());
        $funeral->service->setDefaultOfferingValues();
        $funeral->service->save();
        $this->handleAttachments($request, $funeral);
        ServiceUpdated::dispatch($funeral->service, $funeral->service->participants);

        return redirect(route('service.edit', ['service' => $funeral->service->slug, 'tab' => 'rites']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Funeral $funeral
     * @return Response
     */
    public function destroy(Funeral $funeral)
    {
        $serviceSlug = $funeral->service->slug;
        $funeral->delete();
        return redirect(route('service.edit', ['service' => $serviceSlug, 'tab' => 'rites']));
    }

    /**
     * Create a pdf form with funeral data
     * @param Funeral $funeral
     * @return Response
     */
    public function pdfForm(Funeral $funeral)
    {
        $funeral->load('service');
        $funeral->service->load('day', 'location', 'city');
        $filename = $funeral->service->day->date->format('Ymd') . ' ' . $funeral->buried_name . ' KRA.pdf';

        $pdf = PDF::loadView('funerals.pdf.form', compact('funeral'), [], ['format' => 'A5', 'useActiveForms' => true]);


        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $filename . '"');
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
    public function done(Funeral $funeral)
    {
        $funeral->done = true;
        $funeral->save();
        return json_encode(true);
    }


    /**
     * @param Funeral $funeral
     * @return Application|ResponseFactory|Response
     */
    public function appointmentIcal(Funeral $funeral)
    {
        $service = Service::find($funeral->service_id);
        $raw = View::make('funerals.appointment.ical', compact('funeral', 'service'));
        $raw = str_replace(
            "\r\n\r\n",
            "\r\n",
            str_replace('@@@@', "\r\n", str_replace("\n", "\r\n", str_replace("\r\n", '@@@@', $raw)))
        );
        return response($raw, 200)
            ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->header('Expires', '0')
            ->header('Content-Type', 'text/calendar')
            ->header('Content-Disposition', 'inline; filename=Trauergespraech-' . $funeral->id . '.ics');
    }


    /**
     * @param Request $request
     * @param Funeral $funeral
     * @return \Illuminate\Http\JsonResponse
     */
    public function attach(Request $request, Funeral $funeral)
    {
        $this->handleAttachments($request, $funeral);
        $funeral->refresh();
        return response()->json($funeral->attachments);
    }

    /**
     * @param Request $request
     * @param Funeral $funeral
     * @param Attachment $attachment
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function detach(Request $request, Funeral $funeral, Attachment $attachment)
    {
        $file = $attachment->file;
        $funeral->attachments()->where('id', $attachment->id)->delete();
        Storage::delete($file);
        $attachment->delete();
        $funeral->refresh();
        return response()->json($funeral->attachments);
    }

}
