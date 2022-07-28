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

use App\City;
use App\Integrations\KonfiApp\KonfiAppIntegration;
use App\Integrations\Youtube\YoutubeIntegration;
use App\Service;
use App\Traits\HandlesAttachedImageTrait;
use App\Traits\HandlesAttachmentsTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/**
 * Class CityController
 * @package App\Http\Controllers
 */
class CityController extends Controller
{

    use HandlesAttachmentsTrait;
    use HandlesAttachedImageTrait;

    protected $model = City::class;

    public function __construct()
    {
        $this->middleware('auth')->except('qr');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        if (Auth::user()->is_admin) {
            $cities = City::orderBy('name')->get();
        } else {
            $cities = Auth::user()->cities->sortBy('name');
        }
        foreach ($cities as $cityKey => $city) {
            $cities[$cityKey]['canEdit'] = Auth::user()->can('update', $city);
            $cities[$cityKey]['canDelete'] = Auth::user()->can('delete', $city);
        }

        return Inertia::render('Admin/City/CityIndex', ['cities' => array_values($cities->all())]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $city = City::create([]);
        return redirect()->route('city.edit', $city);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
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
                'youtube_active_stream_id' => 'nullable|string',
                'youtube_passive_stream_id' => 'nullable|string',
                'youtube_auto_startstop' => 'nullable|int',
                'youtube_cutoff_days' => 'nullable|int',
                'default_offering_url' => 'nullable|string',
                'youtube_self_declared_for_children' => 'nullable|int',
                'communiapp_url' => 'nullable|string',
                'communiapp_token' => 'nullable|string',
                'communiapp_default_group_id' => 'nullable|int',
                'communiapp_use_outlook' => 'nullable|checkbox',
                'communiapp_use_op' => 'nullable|checkbox',
                'konfiapp_default_type' => 'nullable|string',
                'logo' => 'nullable|string',
                'official_name' => 'nullable|string',
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param City $city
     * @return Response
     */
    public function edit(City $city)
    {
        if ($city->google_access_token) {
            $streams = YoutubeIntegration::get($city)->getAllStreams();
        } else {
            $streams = [];
        }

        return Inertia::render('Admin/City/CityEditor', compact('city', 'streams'));
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
        return redirect()->route('cities.index')->with('success', 'Die Kirchengemeinde wurde geändert.');
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
        return route('cities.index')->with('success', 'Die Kirchengemeinde wurde gelöscht.');
    }


    /**
     * Show QR codes
     * @param Request $request
     * @param string $city
     * @return \Inertia\Response
     */
    public function qr(Request $request, $city) {
        $city = City::where('name', 'like', '%' . str_replace('-', ' ', $city) . '%')->first();
        $services = Service::where('city_id', $city->id)->whereDate('date', Carbon::now()->setTime(0,0,0))
            ->whereNotNull('konfiapp_event_qr')->get();
        $types = KonfiAppIntegration::get($city)->listEventTypes();
        return Inertia::render('Public/City/QR', compact('services', 'city', 'types'));
    }

}
