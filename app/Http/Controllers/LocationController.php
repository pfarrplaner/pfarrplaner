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
        $locations = Location::all();
        return view('locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cities = City::all();
        return view('locations.create', compact('cities'));
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
            'default_time' => $request->get('default_time')
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
        $cities = City::all();
        $location = Location::find($id);
        if (!(Auth::user()->isAdmin || (Auth::user()->canEditChurch && Auth::user()->cities->contains($location->city))))
            return redirect()->back()->with('error', 'Sie haben keine Berechtigung für die gewählte Aktion');
        return view('locations.edit', ['cities' => $cities, 'location' => $location]);
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
