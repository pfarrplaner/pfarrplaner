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
use App\Parish;
use App\StreetRange;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/**
 * Class ParishController
 * @package App\Http\Controllers
 */
class ParishController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $parishes = Parish::with('owningCity')->whereIn('city_id', Auth::user()->adminCities->pluck('id'))->get();
        return Inertia::render('Admin/Parish/Index', compact('parishes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $this->authorize('create', Parish::class);
        $cities = Auth::user()->writableCities;
        $parish = new Parish();
        return Inertia::render('Admin/Parish/ParishEditor', compact('parish','cities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        Parish::create($this->validateRequest($request));
        return redirect()->route('parishes.index')->with('success', 'Das Pfarramt wurde angelegt.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Parish $parish
     * @return \Inertia\Response
     */
    public function edit(Parish $parish)
    {
        $cities = Auth::user()->writableCities;
        return Inertia::render('Admin/Parish/ParishEditor', compact('parish','cities'));
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
        $parish->update($this->validateRequest($request));

        // import street ranges from csv
        $csv = $request->get('csv', '');
        if ($csv) {
            $ctr = $parish->importStreetsFromCSV($csv);
        }

        $success = isset($ctr) ? $ctr . ' Straßendatensätze wurden importiert.' : '';

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

    /**
     * Validate submitted data
     * @param Request $request
     * @return array
     */
    protected function validateRequest(Request $request) {
        return $request->validate(
            [
                'owningCity' => 'required|int|exists:cities,id',
                'name' => 'required|string',
                'code' => 'required|string',
                'congregation_name' => 'nullable|string',
                'congregation_url' => 'nullable|string',
                'address' => 'nullable|string',
                'zip' => 'nullable|zip',
                'city' => 'nullable|string',
                'phone' => 'nullable|phone_number',
                'email' => 'nullable|email',
            ]
        );

    }
}
