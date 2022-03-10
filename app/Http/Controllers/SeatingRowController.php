<?php

namespace App\Http\Controllers;

use App\Location;
use App\SeatingRow;
use App\SeatingSection;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SeatingRowController extends Controller
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
        $seatingRow = (new SeatingRow([
                                          'title' => '',
                                          'seats' => '',
                                          'split' => '',
                                          'seating_section_id' => $location->seatingSections->first()->id
                                      ]))->load('seatingSection');
        return Inertia::render('Admin/Location/SeatingRowEditor', compact('seatingRow', 'location'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $seatingRow = SeatingRow::create($this->validateRequest($request));
        return redirect()->route(
            'location.edit',
            ['location' => $seatingRow->seatingSection->location, 'tab' => 'seating']
        );
    }

    /**
     * Display the specified resource.
     *
     * @param \App\SeatingRow $seatingRow
     * @return \Illuminate\Http\Response
     */
    public function show(SeatingRow $seatingRow)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\SeatingRow $seatingRow
     * @return \Inertia\Response
     */
    public function edit(SeatingRow $seatingRow)
    {
        $seatingRow->load('seatingSection');
        $location = $seatingRow->seatingSection->location;
        return Inertia::render('Admin/Location/SeatingRowEditor', compact('seatingRow', 'location'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\SeatingRow $seatingRow
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SeatingRow $seatingRow)
    {
        $seatingRow->update($this->validateRequest($request));
        return redirect()->route(
            'location.edit',
            ['location' => $seatingRow->seatingSection->location, 'tab' => 'seating']
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\SeatingRow $seatingRow
     * @return \Illuminate\Http\Response
     */
    public function destroy(SeatingRow $seatingRow)
    {
        $seatingSection = $seatingRow->seatingSection;
        $seatingRow->delete();
        return redirect()->route('location.edit', ['location' => $seatingSection->location, 'tab' => 'seating']);
    }

    /**
     * Validate and add sensible defaults
     * @param Request $request
     * @return array
     */
    protected function validateRequest(Request $request)
    {
        $data = $request->validate(
            [
                'seating_section_id' => 'required|int|exists:seating_sections,id',
                'title' => 'required|regex:/[0-9]+/i',
                'divides_into' => 'nullable|int',
                'seats' => 'nullable|int',
                'spacing' => 'nullable|int',
                'split' => 'nullable|string|regex:/^((\d+)(,\s*\d+)+)$/i',
                'color' => 'nullable|string',
            ]
        );
        if (is_numeric($data['title'])) {
            $data['title'] = str_pad($data['title'], 2, 0, STR_PAD_LEFT);
        }
        $data['divides_into'] = $data['divides_into'] ?? 1;
        $data['seats'] = $data['seats'] ?? 1;
        $data['spacing'] = $data['spacing'] ?? 0;
        $data['split'] = str_replace(' ', '', $data['split']);
        return $data;
    }
}
