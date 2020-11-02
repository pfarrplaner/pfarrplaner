<?php

namespace App\Http\Controllers;

use App\Location;
use App\Seating\SeatingModels;
use App\SeatingSection;
use Illuminate\Http\Request;

class SeatingSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $location = Location::findOrFail($request->get('location', null));
        return view('seatingsections.create', compact('location'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);
        $seatingSection = SeatingSection::create($data);
        return redirect()->route('locations.edit', ['location' => $seatingSection->location, 'tab' => 'seating']);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\SeatingSection $seatingSection
     * @return \Illuminate\Http\Response
     */
    public function show(SeatingSection $seatingSection)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\SeatingSection $seatingSection
     * @return \Illuminate\Http\Response
     */
    public function edit(SeatingSection $seatingSection)
    {
        return view('seatingsections.edit', compact('seatingSection'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\SeatingSection $seatingSection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SeatingSection $seatingSection)
    {
        $seatingSection->update($this->validateRequest($request));
        return redirect()->route('locations.edit', ['location' => $seatingSection->location, 'tab' => 'seating']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\SeatingSection $seatingSection
     * @return \Illuminate\Http\Response
     */
    public function destroy(SeatingSection $seatingSection)
    {
        $location = $seatingSection->location;
        $seatingSection->delete();
        return redirect()->route('locations.edit', ['location' => $location, 'tab' => 'seating']);
    }

    protected function validateRequest(Request $request)
    {
        $data = $request->validate(
            [
                'location_id' => 'required|int|exists:locations,id',
                'title' => 'required',
                'seating_model' => 'required',
                'priority' => 'nullable|int',
            ]
        );
        $data['seating_model'] = get_class(SeatingModels::byTitle($data['seating_model']));
        return $data;
    }
}
