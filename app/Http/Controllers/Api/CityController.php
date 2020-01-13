<?php

namespace App\Http\Controllers\Api;

use App\City;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CityController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        return  Auth::user()->cities;
    }


    /**
     * Get JSON record for a city
     *
     * @param  City $city
     * @return \Illuminate\Http\Response
     */
    public function show(City $city) {
        return response()->json(compact($city));
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
        City::create($this->validateRequest($request));
        return redirect()->route('cities.index')->with('success', 'Die neue Kirchengemeinde wurde gespeichert.');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  City $city
     * @return \Illuminate\Http\Response
     */
    public function edit(City $city)
    {
        return view('cities.edit', compact('city'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  City $city
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, City $city)
    {
        $city->update($this->validateRequest($request));
        return redirect()->route('cities.index')->with('success', 'Die neue Kirchengemeinde wurde gespeichert.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  City $city
     * @return \Illuminate\Http\Response
     */
    public function destroy(City $city)
    {
        $city->delete();
        return redirect('/cities')->with('success', 'Die Kirchengemeinde wurde gelöscht.');
    }

    /**
     * Validate a city request
     * @param Request $request
     * @return mixed
     */
    protected function validateRequest(Request $request)
    {
        $data = $request->validate([
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
        ]);
        return $data;
    }


}
