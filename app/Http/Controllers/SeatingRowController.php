<?php

namespace App\Http\Controllers;

use App\SeatingRow;
use App\SeatingSection;
use Illuminate\Http\Request;

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
    public function create(Request $request)
    {
        $seatingSection = SeatingSection::findOrFail($request->get('seatingSection', 0));
        return view('seatingrows.create', compact('seatingSection'));
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
            'locations.edit',
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
     * @return \Illuminate\Http\Response
     */
    public function edit(SeatingRow $seatingRow)
    {
        return view('seatingrows.edit', compact('seatingRow'));
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
            'locations.edit',
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
        return redirect()->route('locations.edit', ['location' => $seatingSection->location, 'tab' => 'seating']);
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
                'title' => 'required',
                'divides_into' => 'nullable|int',
                'seats' => 'nullable|int',
                'spacing' => 'nullable|int',
                'split' => 'nullable|string|regex:/^((\d+)(,\s*\d+)+)$/i',
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
