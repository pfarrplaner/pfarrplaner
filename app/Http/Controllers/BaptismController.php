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
use App\Baptism;
use App\Http\Requests\StoreBaptismRequest;
use App\Liturgy\PronounSets\PronounSets;
use App\Service;
use App\Traits\HandlesAttachmentsTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;

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
            $service = Service::findOrFail($serviceId);
        }
        $baptism = Baptism::create(
            [
                'candidate_name' => '',
                'candidate_address' => '',
                'candidate_zip' => '',
                'candidate_city' => '',
                'candidate_email' => '',
                'candidate_phone' => '',
                'first_contact_with' => '',
                'registered' => false,
                'signed' => false,
                'docs_ready' => false,
                'docs_where' => '',
                'service_id' => $serviceId
            ]
        );
        return redirect()->route('baptisms.edit', $baptism->id);
    }

    /**
     * @param int $baptismFlag
     * @return mixed
     */
    protected function getBaptismalServices($baptismFlag = 1)
    {
        return Service::setEagerLoads([])
            ->with(['day', 'baptisms', 'participants'])
            ->where('baptism', '=', $baptismFlag)
            ->whereDoesntHave('funerals')
            ->inCities(Auth::user()->cities)
            ->startingFrom(Carbon::now())
            ->ordered()
            ->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Baptism $baptism
     * @return Response
     */
    public function edit(Baptism $baptism)
    {
        $baptismalServicesQuery = Service::setEagerLoads([])
            ->with(['day', 'baptisms', 'participants'])
            ->where('baptism', '=', 1)
            ->inCities(Auth::user()->cities)
            ->startingFrom(Carbon::now())
            ->ordered();
        if ($baptism->service_id) {
            $baptismalServicesQuery->orWhere('services.id', (int)$baptism->service_id);
        }
        $baptismalServices = $baptismalServicesQuery->get();

        $cities = Auth::user()->writableCities;
        $pronounSets = PronounSets::toArray();

        $otherServices = $this->getBaptismalServices(0);

        $services = [];
        foreach (
            [
                'Taufgottesdienste' => $baptismalServices,
                'Andere Gottesdienste' => $otherServices
            ] as $category => $theseServices
        ) {
            foreach ($theseServices as $service) {
                $services[] = [
                    'id' => $service->id,
                    'name' => $service->dateTime->setTimeZone('Europe/Berlin')->format(
                            'd.m.Y, H:i'
                        ) . ' Uhr (' . $service->locationText() . '), '
                        . ($service->titleText() == 'GD' ? '' : $service->titleText(false) . ', ')
                        . $service->participantsText('P')
                        . (count($service->baptisms) ? ' [bisherige Taufen: ' . count($service->baptisms) . ']' : ''),
                    'category' => $category,
                ];
            }
        }

        return Inertia::render('Rites/BaptismEditor', compact('baptism', 'services', 'cities', 'pronounSets'));
        //return view('baptisms.edit', compact('baptism', 'baptismalServices', 'otherServices', 'cities'));
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
        $data = $request->validated();
        $baptism->update($data);

        if ($baptism->service_id) {
            return redirect(route('services.edit', ['service' => $baptism->service_id, 'tab' => 'rites']));
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
    public
    function destroy(
        Baptism $baptism
    ) {
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
    public
    function appointmentIcal(
        Baptism $baptism
    ) {
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
    public
    function done(
        Baptism $baptism
    ) {
        $baptism->done = true;
        $baptism->save();
        return json_encode(true);
    }


    /**
     * @param Request $request
     * @param Baptism $baptism
     * @return \Illuminate\Http\JsonResponse
     */
    public function attach(Request $request, Baptism $baptism)
    {
        $this->handleAttachments($request, $baptism);
        $baptism->refresh();
        return response()->json($baptism->attachments);
    }

    /**
     * @param Request $request
     * @param Baptism $baptism
     * @param Attachment $attachment
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public
    function detach(
        Request $request,
        Baptism $baptism,
        Attachment $attachment
    ) {
        $file = $attachment->file;
        $baptism->attachments()->where('id', $attachment->id)->delete();
        Storage::delete($file);
        $attachment->delete();
        $baptism->refresh();
        return response()->json($baptism->attachments);
    }
}
