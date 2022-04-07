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
use App\Liturgy\PronounSets\PronounSets;
use App\Location;
use App\Service;
use App\Traits\HandlesAttachmentsTrait;
use App\User;
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
use Inertia\Inertia;

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
    public function create(Service $service)
    {
        $wedding = Wedding::create([
                                       'service_id' => $service->id,
                                       'spouse1_name' => '',
                                       'spouse1_birth_name' => '',
                                       'spouse1_email' => '',
                                       'spouse1_phone' => '',
                                       'spouse2_name' => '',
                                       'spouse2_birth_name' => '',
                                       'spouse2_email' => '',
                                       'spouse2_phone' => '',
                                       'text' => '',
                                        'registered' => false,
                                        'registration_document' => '',
            'signed' => false,
            'docs_ready' => false,
            'docs_where' => '',
                                   ]);
        return redirect()->route('weddings.edit', $wedding->id);
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
        $data = $this->validateRequest($request);
        $serviceId = $data['service_id'] = $data['service'];
        $wedding = new Wedding($data);
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


        return redirect(route('service.edit', ['service' => $wedding->service->slug, 'tab' => 'rites']));
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
     * @return \Inertia\Response
     */
    public function edit(Wedding $wedding)
    {
        $wedding->load('service');
        $pronounSets = PronounSets::toArray();
        return Inertia::render('Rites/WeddingEditor', compact('wedding', 'pronounSets'));
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
        $data = $this->validateRequest($request);
        if (isset($data['service'])) {
            $serviceId = $data['service_id'] = $data['service'];
        }
        $wedding->update($data);

        $wedding->service->setDefaultOfferingValues();
        $wedding->service->save();
        ServiceUpdated::dispatch($wedding->service, $wedding->service->participants);

        return redirect(route('service.edit', ['service' => $wedding->service->slug, 'tab' => 'rites']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Wedding $wedding
     * @return Response
     */
    public function destroy(Wedding $wedding)
    {
        $serviceSlug = $wedding->service->slug;
        $wedding->delete();
        return redirect(route('service.edit', ['service' => $serviceSlug, 'tab' => 'rites']));
    }


    /**
     * Wedding wizard, step 1: select city and date
     * @param Request $request
     * @return Factory|View
     */
    public function wizard(Request $request)
    {
        $cities = Auth::user()->writableCities;
        $locations = Location::whereIn('city_id', $cities->pluck('id'))->get();
        $people = User::all();
        $user = Auth::user();
        return Inertia::render('Rites/WeddingWizard', compact('cities', 'locations', 'people', 'user'));
    }

    public function wizardSave(Request $request)
    {
        $data = $request->validate(
            [
                'date' => 'required|date_format:d.m.Y H:i',
                'city' => 'required|exists:cities,id',
                'location' => 'required',
                'spouse1_name' => 'required|string',
                'spouse2_name' => 'required|string',
                'pastor' => 'nullable',
            ]
        );

        if (is_numeric($data['location'])) {
            $request->validate(['location' => 'exists:locations,id']);
        }
        $data['date'] = Carbon::createFromFormat('d.m.Y H:i', $data['date']);

        $city = City::find($data['city']);

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
                'date' => $data['date'],
                'location_id' => ($location ? $location->id : null),
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
                if (null !== $person) $sync[(is_numeric($person) ? $person : $person['id'])] = ['category' => 'P'];

            }
            $service->pastors()->sync($sync);
        }

        $wedding = Wedding::create(
            [
                'service_id' => $service->id,
                'spouse1_name' => $data['spouse1_name'],
                'spouse2_name' => $data['spouse2_name'],
                'spouse1_birth_name' => '',
                'spouse2_birth_name' => '',
                'spouse1_email' => '',
                'spouse2_email' => '',
                'spouse1_phone' => '',
                'spouse2_phone' => '',
                'text' => '',
                'registered' => 0,
                'signed' => 0,
                'docs_ready' => 0,
                'docs_where' => '',
                'registration_document' => '',
            ]
        );

        return redirect()->route('weddings.edit', $wedding->id);


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

    /**
     * @param Request $request
     * @param Wedding $wedding
     * @return \Illuminate\Http\JsonResponse
     */
    public function attach(Request $request, Wedding $wedding)
    {
        $this->handleAttachments($request, $wedding);
        $wedding->refresh();
        return response()->json($wedding->attachments);
    }

    /**
     * @param Request $request
     * @param Wedding $wedding
     * @param Attachment $attachment
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function detach(Request $request, Wedding $wedding, Attachment $attachment)
    {
        $file = $attachment->file;
        $wedding->attachments()->where('id', $attachment->id)->delete();
        Storage::delete($file);
        $attachment->delete();
        $wedding->refresh();
        return response()->json($wedding->attachments);
    }


    protected function validateRequest(Request $request)
    {
        $data = $request->validate(
            [
                'service' => 'int|exists:services,id',
                'spouse1_name' => 'required|string',
                'spouse1_birth_name' => 'nullable|string',
                'pronoun_set1' => 'nullable|string',
                'spouse1_phone' => 'nullable|phone_number',
                'spouse1_email' => 'nullable|email',
                'spouse2_name' => 'required|string',
                'spouse2_birth_name' => 'nullable|string',
                'spouse2_phone' => 'nullable|phone_number',
                'spouse2_email' => 'nullable|email',
                'pronoun_set2' => 'nullable|string',
                'text' => 'nullable|string',
                'registered' => 'nullable|bool',
                'signed' => 'nullable|bool',
                'docs_ready' => 'nullable|bool',
                'docs_where' => 'nullable|string',
                'appointment' => 'nullable|date',
                'spouse1_dob' => 'nullable|date_format:"d.m.Y"',
                'spouse1_address' => 'nullable|string',
                'spouse1_zip' => 'nullable|string',
                'spouse1_city' => 'nullable|string',
                'spouse1_needs_dimissorial' => 'nullable|int',
                'spouse1_dimissorial_issuer' => 'nullable|string',
                'spouse1_dimissorial_requested' => 'nullable|date_format:"d.m.Y"',
                'spouse1_dimissorial_received' => 'nullable|date_format:"d.m.Y"',
                'spouse2_dob' => 'nullable|date_format:"d.m.Y"',
                'spouse2_address' => 'nullable|string',
                'spouse2_zip' => 'nullable|string',
                'spouse2_city' => 'nullable|string',
                'spouse2_needs_dimissorial' => 'nullable|int',
                'spouse2_dimissorial_issuer' => 'nullable|string',
                'spouse2_dimissorial_requested' => 'nullable|date_format:"d.m.Y"',
                'spouse2_dimissorial_received' => 'nullable|date_format:"d.m.Y"',
                'needs_permission' => 'nullable|int',
                'permission_requested' => 'nullable|date_format:"d.m.Y"',
                'permission_received' => 'nullable|date_format:"d.m.Y"',
                'music' => 'nullable|string',
                'gift' => 'nullable|string',
                'flowers' => 'nullable|string',
                'docs_format' => 'nullable|int',
                'notes' => 'nullable|string',
                'processed' => 'nullable|integer|between:0,1'
            ]
        );
        if (!isset($data['text'])) {
            $data['text'] = '';
        }
        if (!isset($data['docs_where'])) {
            $data['docs_where'] = '';
        }
        if (!isset($data['registration_document'])) {
            $data['registration_document'] = '';
        }
        if (!isset($data['registered'])) {
            $data['registered'] = 0;
        }
        if (!isset($data['signed'])) {
            $data['signed'] = 0;
        }
        if (!isset($data['docs_ready'])) {
            $data['docs_ready'] = 0;
        }

        // dates
        if (isset($data['appointment'])) {
            $data['appointment'] = Carbon::parse($data['appointment'], 'Europe/Berlin')->setTimezone('UTC');
        }

        if (isset($data['spouse1_dob'])) {
            $data['spouse1_dob'] = Carbon::createFromFormat('d.m.Y', $data['spouse1_dob']);
        }
        if (isset($data['spouse1_dimissorial_requested'])) {
            $data['spouse1_dimissorial_requested'] = Carbon::createFromFormat(
                'd.m.Y',
                $data['spouse1_dimissorial_requested']
            );
        }
        if (isset($data['spouse1_dimissorial_received'])) {
            $data['spouse1_dimissorial_received'] = Carbon::createFromFormat(
                'd.m.Y',
                $data['spouse1_dimissorial_received']
            );
        }
        if (isset($data['spouse2_dob'])) {
            $data['spouse2_dob'] = Carbon::createFromFormat('d.m.Y', $data['spouse2_dob']);
        }
        if (isset($data['spouse2_dimissorial_requested'])) {
            $data['spouse2_dimissorial_requested'] = Carbon::createFromFormat(
                'd.m.Y',
                $data['spouse2_dimissorial_requested']
            );
        }
        if (isset($data['spouse2_dimissorial_received'])) {
            $data['spouse2_dimissorial_received'] = Carbon::createFromFormat(
                'd.m.Y',
                $data['spouse2_dimissorial_received']
            );
        }
        if (isset($data['permission_requested'])) {
            $data['permission_requested'] = Carbon::createFromFormat('d.m.Y', $data['permission_requested']);
        }
        if (isset($data['permission_received'])) {
            $data['permission_received'] = Carbon::createFromFormat('d.m.Y', $data['permission_received']);
        }
        return $data;
    }
}
