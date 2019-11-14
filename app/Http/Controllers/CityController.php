<?php

namespace App\Http\Controllers;

use App\City;
use Illuminate\Http\Request;

class CityController extends Controller
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
    public function index(Request $request) {
        $cities = City::all();
        return view('cities.index', compact('cities'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('cities.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
        ]);
        $city = new City([
            'name' => $request->get('name'),
            'public_events_calendar_url' => $request->get('public_events_calendar_url') ?: '',
            'default_offering_goal' => $request->get('default_offering_goal', ''),
            'default_offering_description' => $request->get('default_offering_description', ''),
            'default_funeral_offering_goal' => $request->get('default_funeral_offering_goal', ''),
            'default_funeral_offering_description' => $request->get('default_funeral_offering_description', ''),
            'default_wedding_offering_goal' => $request->get('default_wedding_offering_goal', ''),
            'default_wedding_offering_description' => $request->get('default_wedding_offering_description', '')
        ]);
        $city->save();
        return redirect()->route('cities.index')->with('success', 'Die neue Kirchengemeinde wurde gespeichert.');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $city = City::find($id);

        return view('cities.edit', compact('city'));    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);
        $city = City::find($id);
        $city->name = $request->get('name');
        $city->public_events_calendar_url = $request->get('public_events_calendar_url') ?: '';
        $city->default_offering_goal = $request->get('default_offering_goal', '');
        $city->default_offering_description = $request->get('default_offering_description', '');
        $city->default_funeral_offering_goal = $request->get('default_funeral_offering_goal', '');
        $city->default_funeral_offering_description = $request->get('default_funeral_offering_description', '');
        $city->default_wedding_offering_goal = $request->get('default_wedding_offering_goal', '');
        $city->default_wedding_offering_description = $request->get('default_wedding_offering_description', '');
        $city->save();

        return redirect('/cities')->with('success', 'Die Kirchengemeinde wurde geändert.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $city = City::find($id);
        $city->delete();

        return redirect('/cities')->with('success', 'Die Kirchengemeinde wurde gelöscht.');
    }


}
