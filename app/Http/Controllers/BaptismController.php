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

use App\Baptism;
use App\Http\Requests\StoreBaptismRequest;
use App\Service;
use App\Traits\HandlesAttachmentsTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

/**
 * Class BaptismController
 * @package App\Http\Controllers
 */
class BaptismController extends Controller
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
     * @param int $serviceId Service Id
     * @return Response
     */
    public function create($serviceId = null)
    {
        $service = null;
        if ($serviceId) {
            $service = Service::find($serviceId);
        }

        $baptismalServices = $this->getBaptismalServices();
        $otherServices = $this->getBaptismalServices(0);

        $cities = Auth::user()->writableCities;

        return view('baptisms.create', compact('service', 'baptismalServices', 'otherServices', 'cities'));
    }

    /**
     * @param int $baptismFlag
     * @return mixed
     */
    protected function getBaptismalServices($baptismFlag = 1)
    {
        return Service::where('baptism', '=', $baptismFlag)
            ->select('services.*')
            ->join('days', 'services.day_id', '=', 'days.id')
            ->whereIn('city_id', Auth::user()->cities->pluck('id'))
            ->whereHas(
                'day',
                function ($query) {
                    $query->where('date', '>=', Carbon::now());
                }
            )
            ->orderBy('days.date', 'ASC')
            ->orderBy('time')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreBaptismRequest $request
     * @return Response
     */
    public function store(StoreBaptismRequest $request)
    {
        $baptism = new Baptism(
            [
                'city_id' => $request->get('city_id'),
                'candidate_name' => $request->get('candidate_name') ?: '',
                'candidate_address' => $request->get('candidate_address') ?: '',
                'candidate_zip' => $request->get('candidate_zip') ?: '',
                'candidate_city' => $request->get('candidate_city') ?: '',
                'candidate_phone' => $request->get('candidate_phone') ?: '',
                'candidate_email' => $request->get('candidate_email') ?: '',
                'first_contact_with' => $request->get('first_contact_with') ?: '',
                'registered' => $request->get('registered') ? 1 : 0,
                'registration_document' => $request->get('candidate_email') ?: '',
                'signed' => $request->get('signed') ? 1 : 0,
                'docs_ready' => $request->get('docs_ready') ? 1 : 0,
                'docs_where' => $request->get('docs_where') ?: '',
            ]
        );
        $serviceId = $request->get('service') ?: '';
        if ($serviceId) {
            $baptism->service_id = $serviceId;
        }
        if ($request->get('first_contact_on')) {
            $baptism->first_contact_on = Carbon::createFromFormat('d.m.Y', $request->get('first_contact_on'));
        }
        if ($request->get('appointment')) {
            $baptism->appointment = Carbon::createFromFormat('d.m.Y H:i', $request->get('appointment'));
        }

        if ($request->hasFile('registration_document')) {
            $baptism->registration_document = $request->file('registration_document')->store('baptism', 'public');
        }

        $baptism->save();
        $this->handleAttachments($request, $baptism);


        if ($serviceId) {
            return redirect(route('services.edit', ['service' => $serviceId, 'tab' => 'rites']));
        } else {
            return redirect(route('home'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Baptism $baptism
     * @return Response
     */
    public function show(Baptism $baptism)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Baptism $baptism
     * @return Response
     */
    public function edit(Baptism $baptism)
    {
        $baptismalServicesQuery = Service::where('baptism', '=', 1)
            ->select('services.*')
            ->join('days', 'services.day_id', '=', 'days.id')
            ->whereIn('city_id', Auth::user()->cities->pluck('id'))
            ->whereHas(
                'day',
                function ($query) {
                    $query->where('date', '>=', Carbon::now());
                }
            )
            ->orderBy('days.date', 'ASC')
            ->orderBy('time');
        if ($baptism->service_id) {
            $baptismalServicesQuery->orWhere('services.id', (int)$baptism->service_id);
        }
        $baptismalServices = $baptismalServicesQuery->get();

        $cities = Auth::user()->writableCities;

        $otherServices = $this->getBaptismalServices(0);
        return view('baptisms.edit', compact('baptism', 'baptismalServices', 'otherServices', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreBaptismRequest $request
     * @param Baptism $baptism
     * @return Response
     */
    public function update(StoreBaptismRequest $request, Baptism $baptism)
    {
        $serviceId = $request->get('service');
        if ($serviceId) {
            $baptism->service_id = $serviceId;
        } else {
            $baptism->service_id = null;
        }
        $baptism->city_id = $request->get('city_id');
        $baptism->candidate_name = $request->get('candidate_name') ?: '';
        $baptism->candidate_address = $request->get('candidate_address') ?: '';
        $baptism->candidate_zip = $request->get('candidate_zip') ?: '';
        $baptism->candidate_city = $request->get('candidate_city') ?: '';
        $baptism->candidate_phone = $request->get('candidate_phone') ?: '';
        $baptism->candidate_email = $request->get('candidate_email') ?: '';
        $baptism->first_contact_with = $request->get('first_contact_with') ?: '';
        if ($request->get('first_contact_on')) {
            $baptism->first_contact_on = Carbon::createFromFormat('d.m.Y', $request->get('first_contact_on'));
        }
        if ($request->get('appointment')) {
            $baptism->appointment = Carbon::createFromFormat('d.m.Y H:i', $request->get('appointment'));
        }
        $baptism->registered = $request->get('registered') ? 1 : 0;
        $baptism->signed = $request->get('signed') ? 1 : 0;
        $baptism->docs_ready = $request->get('docs_ready') ? 1 : 0;
        $baptism->docs_where = $request->get('docs_where') ?: '';

        if ($request->hasFile('registration_document') || ($request->get('removeAttachment') == 1)) {
            if ($baptism->registration_document != '') {
                Storage::delete($baptism->registration_document);
            }
            $baptism->registration_document = '';
        }
        if ($request->hasFile('registration_document')) {
            $baptism->registration_document = $request->file('registration_document')->store('baptism', 'public');
        }

        $baptism->save();
        $this->handleAttachments($request, $baptism);


        if ($serviceId) {
            return redirect(route('services.edit', ['service' => $serviceId, 'tab' => 'rites']));
        } else {
            return redirect(route('home'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Baptism $baptism
     * @return Response
     */
    public function destroy(Baptism $baptism)
    {
        $serviceId = $baptism->service_id;
        $baptism->delete();
        if ($serviceId) {
            return redirect(route('services.edit', ['service' => $serviceId, 'tab' => 'rites']));
        } else {
            return redirect(route('home'));
        }
    }

    /**
     * @param Baptism $baptism
     * @return Application|ResponseFactory|Response
     */
    public function appointmentIcal(Baptism $baptism)
    {
        $service = Service::find($baptism->service_id);
        $raw = View::make('baptisms.appointment.ical', compact('baptism', 'service'));
        $raw = str_replace(
            "\r\n\r\n",
            "\r\n",
            str_replace('@@@@', "\r\n", str_replace("\n", "\r\n", str_replace("\r\n", '@@@@', $raw)))
        );
        return response($raw, 200)
            ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->header('Expires', '0')
            ->header('Content-Type', 'text/calendar')
            ->header('Content-Disposition', 'inline; filename=Taufgespraech-' . $baptism->id . '.ics');
    }

    /**
     * @param Baptism $baptism
     * @return false|string
     */
    public function done(Baptism $baptism)
    {
        $baptism->done = true;
        $baptism->save();
        return json_encode(true);
    }
}
