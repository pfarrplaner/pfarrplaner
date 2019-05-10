<?php

namespace App\Http\Controllers;

use App\Funeral;
use App\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PDF;

class FuneralController extends Controller
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
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Service $service)
    {
        return view('funerals.create', compact('service'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'buried_name' => 'required',
            'service' => 'required|int',
        ]);

        $serviceId = $request->get('service');
        $funeral = new Funeral([
            'service_id' => $serviceId,
            'buried_name' => $request->get('buried_name') ?: '',
            'buried_address' => $request->get('buried_address') ?: '',
            'buried_zip' => $request->get('buried_zip') ?: '',
            'buried_city' => $request->get('buried_city') ?: '',
            'text' => $request->get('text') ?: '',
            'type' => $request->get('type') ?: '',
            'relative_name' => $request->get('relative_name') ?: '',
            'relative_address' => $request->get('relative_address') ?: '',
            'relative_zip' => $request->get('relative_zip') ?: '',
            'relative_city' => $request->get('relative_city') ?: '',
            'wake_location' => $request->get('wake_location') ?: '',
        ]);
        if ($request->get('announcement')) $funeral->announcement = Carbon::createFromFormat('d.m.Y', $request->get('announcement'));
        if ($request->get('wake')) $funeral->announcement = Carbon::createFromFormat('d.m.Y', $request->get('wake'));
        $funeral->save();
        return redirect(route('services.edit', ['service' => $serviceId, 'tab' => 'rites']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Funeral  $funeral
     * @return \Illuminate\Http\Response
     */
    public function show(Funeral $funeral)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Funeral  $funeral
     * @return \Illuminate\Http\Response
     */
    public function edit(Funeral $funeral)
    {
        $service = Service::find($funeral->service_id);
        return view('funerals.edit', compact('service', 'funeral'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Funeral  $funeral
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Funeral $funeral)
    {
        $request->validate([
            'buried_name' => 'required',
            'service' => 'required|int',
        ]);
        $serviceId = $request->get('service');
        $funeral->buried_name = $request->get('buried_name') ?: '';
        $funeral->buried_address = $request->get('buried_address') ?: '';
        $funeral->buried_zip = $request->get('buried_zip') ?: '';
        $funeral->buried_city = $request->get('buried_city') ?: '';
        $funeral->text = $request->get('text') ?: '';
        $funeral->type = $request->get('type') ?: '';
        $funeral->relative_name = $request->get('relative_name') ?: '';
        $funeral->relative_address = $request->get('relative_address') ?: '';
        $funeral->relative_zip = $request->get('relative_zip') ?: '';
        $funeral->relative_city = $request->get('relative_city') ?: '';
        $funeral->wake_location = $request->get('wake_location') ?: '';
        if ($request->get('announcement') !='')  $funeral->announcement = Carbon::createFromFormat('d.m.Y', $request->get('announcement'));
        if ($request->get('wake') != '') $funeral->wake = Carbon::createFromFormat('d.m.Y', $request->get('wake'));
        $funeral->save();

        return redirect(route('services.edit', ['service' => $serviceId, 'tab' => 'rites']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Funeral  $funeral
     * @return \Illuminate\Http\Response
     */
    public function destroy(Funeral $funeral)
    {
        $serviceId = $funeral->service_id;
        $funeral->delete();
        return redirect(route('services.edit', ['service' => $serviceId, 'tab' => 'rites']));
    }

    /**
     * Create a pdf form with funeral data
     * @param \App\Funeral $funeral
     * @return \Illuminate\Http\Response
     */
    public function pdfForm(Funeral $funeral) {
        $funeral->load('service');
        $funeral->service->load('day', 'location', 'city');
        $filename = $funeral->service->day->date->format('Ymd').' '.$funeral->buried_name.' KRA.pdf';

        $pdf = PDF::loadView('funerals.pdf.form', compact('funeral'), [], ['format' => 'A5', 'useActiveForms' => true]);


        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $filename .'"');
        header('Content-Type: application/pdf');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        return $pdf->download($filename);
    }
}
