<?php

namespace App\Http\Controllers;

use App\City;
use App\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
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
        $locations = Location::whereIn('city_id', Auth::user()->writableCities->pluck('id'))->get();
        return view('locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        Location::create($this->validateRequest());
        return redirect()->route('locations.index')->with('success', 'Die Kirche wurde gespeichert');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cities = Auth::user()->writableCities;
        $location = Location::find($id);
        $alternateLocations = Location::whereIn('city_id', $cities->pluck('id'))->where('id', '!=', $location->id)->get();
        return view('locations.edit', compact('cities', 'location', 'alternateLocations'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Location $location
     * @return \Illuminate\Http\Response
     */
    public function update(Location $location)
    {
        $location->update($this->validateRequest());
        return redirect()->route('locations.index')->with('success', 'Die Kirche wurde geändert.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $location = Location::find($id);
        $location->delete();
        return redirect()->route('locations.index')->with('success', 'Die Kirche wurde gelöscht.');
    }

    /**
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateRequest(): array
    {
        return request()->validate([
            'name' => 'required|max:255',
            'city_id' => 'required|integer',
            'default_time' => 'nullable|date_format:H:i',
            'cc_default_location' => 'nullable',
            'alternate_location_id' => 'nullable',
            'general_location_name' => 'nullable',
            'at_text' => 'nullable',
        ]);
    }
}
