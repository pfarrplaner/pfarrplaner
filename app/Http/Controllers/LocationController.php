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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'city_id' => 'required|integer',
            'default_time' => 'date_format:H:i',
        ]);
        $location = new Location([
            'name' => $request->get('name'),
            'city_id' => $request->get('city_id'),
            'default_time' => $request->get('default_time'),
            'cc_default_location' => $request->get('cc_default_location') ?: '',
            'alternate_location_id' => $request->get('alternate_location_id') ?: null,
            'general_location_name' => $request->get('general_location_name') ?: '',
        ]);
        $location->save();
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
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'city_id' => 'required|integer',
            'default_time' => 'date_format:H:i',
        ]);

        $location = Location::find($id);
        $location->name = $request->get('name');
        $location->city_id = $request->get('city_id');
        $location->default_time = $request->get('default_time');
        $location->cc_default_location = $request->get('cc_default_location') ?: '';
        $location->alternate_location_id = $request->get('alternate_location_id') ?: null;
        $location->general_location_name = $request->get('general_location_name') ?: '';
        $location->save();

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
}
