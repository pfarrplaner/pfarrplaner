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

use App\Location;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Class LocationController
 * @package App\Http\Controllers
 */
class LocationController extends Controller
{
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
        $locations = Location::whereIn('city_id', Auth::user()->writableCities->pluck('id'))->get();
        return view('locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $cities = Auth::user()->writableCities;
        $alternateLocations = Location::whereIn('city_id', $cities->pluck('id'))->get();
        return view('locations.create', compact('cities', 'alternateLocations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        Location::create($this->validateRequest());
        return redirect()->route('locations.index')->with('success', 'Die Kirche wurde gespeichert');
    }

    /**
     * @return array
     * @throws ValidationException
     */
    protected function validateRequest(): array
    {
        return request()->validate(
            [
                'name' => 'required|max:255',
                'city_id' => 'required|integer',
                'default_time' => 'nullable|date_format:H:i',
                'cc_default_location' => 'nullable',
                'alternate_location_id' => 'nullable',
                'general_location_name' => 'nullable',
                'at_text' => 'nullable',
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $cities = Auth::user()->writableCities;
        $location = Location::find($id);
        $alternateLocations = Location::whereIn('city_id', $cities->pluck('id'))->where('id', '!=', $location->id)->get(
        );
        return view('locations.edit', compact('cities', 'location', 'alternateLocations'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Location $location
     * @return Response
     */
    public function update(Location $location)
    {
        $location->update($this->validateRequest());
        return redirect()->route('locations.index')->with('success', 'Die Kirche wurde geändert.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $location = Location::find($id);
        $location->delete();
        return redirect()->route('locations.index')->with('success', 'Die Kirche wurde gelöscht.');
    }
}
