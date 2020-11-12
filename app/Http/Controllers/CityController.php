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
use App\Integrations\KonfiApp\KonfiAppIntegration;
use App\Service;
use App\Traits\HandlesAttachmentsTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class CityController
 * @package App\Http\Controllers
 */
class CityController extends Controller
{

    use HandlesAttachmentsTrait;

    public function __construct()
    {
        $this->middleware('auth')->except('qr');
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $cities = Auth::user()->cities;
        return view('cities.index', compact('cities'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('cities.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $city = City::create($this->validateRequest());
        $this->handleIndividualAttachment($request, $city, 'podcast_logo');
        $this->handleIndividualAttachment($request, $city, 'sermon_default_image');
        return redirect()->route('cities.index')->with('success', 'Die neue Kirchengemeinde wurde gespeichert.');
    }

    /**
     * Validate a city request
     * @return mixed
     */
    protected function validateRequest()
    {
        return request()->validate(
            [
                'name' => 'required|max:255',
                'public_events_calendar_url' => 'nullable',
                'default_offering_goal' => 'nullable',
                'default_offering_description' => 'nullable',
                'default_funeral_offering_goal' => 'nullable',
                'default_funeral_offering_description' => 'nullable',
                'default_wedding_offering_goal' => 'nullable',
                'default_wedding_offering_description' => 'nullable',
                'op_domain' => 'nullable',
                'op_customer_key' => 'nullable',
                'op_customer_token' => 'nullable',
                'podcast_title' => 'nullable|string',
                'homepage' => 'nullable|string',
                'podcast_owner_name' => 'nullable|string',
                'podcast_owner_email' => 'nullable|email',
                'youtube_channel_url' => 'nullable|string',
                'konfiapp_apikey' => 'nullable|string',
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $city = City::find($id);

        return view('cities.edit', compact('city'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param City $city
     * @param Request $request
     * @return Response
     */
    public function update(Request $request, City $city)
    {
        $city->update($this->validateRequest());
        $this->handleIndividualAttachment($request, $city, 'podcast_logo');
        $this->handleIndividualAttachment($request, $city, 'sermon_default_image');
        return redirect('/cities')->with('success', 'Die Kirchengemeinde wurde geändert.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param City $city
     * @return Response
     */
    public function destroy(City $city)
    {
        $city->delete();
        return redirect('/cities')->with('success', 'Die Kirchengemeinde wurde gelöscht.');
    }


    /**
     * Show QR codes
     * @param Request $request
     * @param string $city
     */
    public function qr(Request $request, $city) {
        $city = City::where('name', 'like', '%' . str_replace('-', ' ', $city) . '%')->first();
        $services = Service::where('city_id', $city->id)
        ->whereHas('day', function($query) {
            $query->where('date', Carbon::now()->setTime(0,0,0));
        })->whereNotNull('konfiapp_event_qr')->get();
        $types = KonfiAppIntegration::get($city)->listEventTypes();

        return view('cities.qr', compact('services', 'city', 'types'));
    }

}
