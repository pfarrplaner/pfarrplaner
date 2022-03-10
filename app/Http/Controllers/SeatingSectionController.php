<?php

namespace App\Http\Controllers;

use App\Location;
use App\Seating\RowBasedSeatingModel;
use App\Seating\SeatingModels;
use App\SeatingSection;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
    public function create(Request $request, Location $location)
    {
        $seatingSection = (new SeatingSection([
                                                  'title' => '',
                                                  'location' => $location->id,
                                                  'seatingModel' => RowBasedSeatingModel::class,
                                                  'priorität' => 1,
                                                  'color' => '',
                                              ]))->load('location');
        return Inertia::render('Admin/Location/SeatingSectionEditor', compact('seatingSection'));
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
        return redirect()->route('location.edit', ['location' => $seatingSection->location, 'tab' => 'seating']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\SeatingSection $seatingSection
     * @return \Inertia\Response
     */
    public function edit(SeatingSection $seatingSection)
    {
        return Inertia::render('Admin/Location/SeatingSectionEditor', compact('seatingSection'));
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
        return redirect()->route('location.edit', ['location' => $seatingSection->location, 'tab' => 'seating']);
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
        return redirect()->route('location.edit', ['location' => $location, 'tab' => 'seating']);
    }

    protected function validateRequest(Request $request)
    {
        $data = $request->validate(
            [
                'location_id' => 'required|int|exists:locations,id',
                'title' => 'required',
                'priority' => 'nullable|int',
                'color' => 'nullable|string',
            ]
        );
        if (isset($data['seating_model'])) $data['seating_model'] = get_class(SeatingModels::byTitle($data['seating_model']));
        if ($data['color'] == 'rgb(0,0,0)') $data['color'] = '';
        return $data;
    }
}
