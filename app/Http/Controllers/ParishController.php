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
use App\Parish;
use App\StreetRange;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class ParishController
 * @package App\Http\Controllers
 */
class ParishController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $parishes = Parish::with('owningCity')->whereIn('city_id', Auth::user()->writableCities)->get();
        return view('parishes.index', compact('parishes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $cities = Auth::user()->cities;
        return view('parishes.create', compact('cities'));
    }

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
                'owningCity' => 'required|int',
                'name' => 'required',
                'code' => 'required',
            ]
        );

        $owningCity = City::findOrFail($request->get('owningCity'));
        $parish = new Parish(
            [
                'city_id' => $owningCity->id,
                'name' => $request->get('name'),
                'code' => $request->get('code'),
                'address' => $request->get('address', ''),
                'zip' => $request->get('zip', ''),
                'city' => $request->get('city', ''),
                'phone' => $request->get('phone', ''),
                'email' => $request->get('email', ''),
            ]
        );
        $parish->save();
        return redirect()->route('parishes.index')->with('success', 'Das Pfarramt wurde angelegt.');
    }

    /**
     * Display the specified resource.
     *
     * @param Parish $parish
     * @return Response
     */
    public function show(Parish $parish)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Parish $parish
     * @return Response
     */
    public function edit(Parish $parish)
    {
        $cities = Auth::user()->cities;
        return view('parishes.edit', compact('parish', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Parish $parish
     * @return Response
     */
    public function update(Request $request, Parish $parish)
    {
        $request->validate(
            [
                'owningCity' => 'required|int',
                'name' => 'required',
                'code' => 'required',
            ]
        );

        $parish->name = $request->get('name');
        $parish->code = $request->get('code');
        $parish->city_id = $request->get('owningCity');
        $parish->address = $request->get('address', '');
        $parish->zip = $request->get('zip', '');
        $parish->city = $request->get('city', '');
        $parish->phone = $request->get('phone', '');
        $parish->email = $request->get('email', '');
        $parish->save();

        // import street ranges from csv
        $csv = $request->get('csv', '');
        if ($csv) {
            $ctr = $parish->importStreetsFromCSV($csv);
        }

        $success = $ctr ? $ctr . ' Straßendatensätze wurden importiert.' : '';

        return redirect()->route('parishes.index')->with('success', 'Das Pfarramt wurde geändert. ' . $success);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Parish $parish
     * @return Response
     */
    public function destroy(Parish $parish)
    {
        /** @var StreetRange $streetRange */
        foreach ($parish->streetRanges as $streetRange) {
            $streetRange->delete();
        }
        $parish->delete();
        return redirect()->route('parishes.index')->with('success', 'Das Pfarramt wurde gelöscht.');
    }
}
