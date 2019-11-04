<?php

namespace App\Http\Controllers;

use App\City;
use App\Parish;
use App\StreetRange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParishController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parishes = Parish::with('owningCity')->whereIn('city_id', Auth::user()->writableCities)->get();
        return view('parishes.index', compact('parishes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cities = Auth::user()->cities;
        return view('parishes.create', compact('cities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'owningCity' => 'required|int',
            'name' => 'required',
            'code' => 'required',
        ]);

        $owningCity = City::findOrFail($request->get('owningCity'));
        $parish = new Parish([
           'city_id' => $owningCity->id,
           'name' => $request->get('name'),
           'code' => $request->get('code'),
           'address' => $request->get('address', ''),
           'zip' => $request->get('zip', ''),
           'city' => $request->get('city', ''),
           'phone' => $request->get('phone', ''),
           'email' => $request->get('email', ''),
        ]);
        $parish->save();
        return redirect()->route('parishes.index')->with('success', 'Das Pfarramt wurde angelegt.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Parish $parish
     * @return \Illuminate\Http\Response
     */
    public function show(Parish $parish)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Parish $parish
     * @return \Illuminate\Http\Response
     */
    public function edit(Parish $parish)
    {
        $cities = Auth::user()->cities;
        return view('parishes.edit', compact('parish', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Parish $parish
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Parish $parish)
    {
        $request->validate([
            'owningCity' => 'required|int',
            'name' => 'required',
            'code' => 'required',
        ]);

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

        $success = $ctr ? $ctr.' Straßendatensätze wurden importiert.' : '';

        return redirect()->route('parishes.index')->with('success', 'Das Pfarramt wurde geändert. '.$success);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Parish $parish
     * @return \Illuminate\Http\Response
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
