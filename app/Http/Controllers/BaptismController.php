<?php

namespace App\Http\Controllers;

use App\Baptism;
use App\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BaptismController extends Controller
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param int $serviceId Service Id
     * @return \Illuminate\Http\Response
     */
    public function create($serviceId)
    {
        $service = Service::find($serviceId);
        return view('baptisms.create', compact('service'));
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
            'candidate_name' => 'required',
            'service' => 'required|integer',
        ]);

        $serviceId = $request->get('service');

        $baptism = new Baptism([
            'service_id' => $serviceId,
            'candidate_name' => $request->get('candidate_name') ?: '',
            'candidate_address' => $request->get('candidate_address') ?: '',
            'candidate_zip' => $request->get('candidate_zip') ?: '',
            'candidate_city' => $request->get('candidate_city') ?: '',
            'candidate_phone' => $request->get('candidate_phone') ?: '',
            'candidate_email' => $request->get('candidate_email') ?: '',
            'first_contact_with' => $request->get('first_contact_with') ?: '',
            'registered' => $request->get('registered') ? 1 : 0,
            'registration_document' => $request->get('candidate_email') ?: '',
            'signed' => $request->get('signed') ? 1 : 0,
            'docs_ready' => $request->get('docs_ready') ? 1 : 0,
            'docs_where' => $request->get('docs_where') ?: '',
        ]);
        if ($request->get('first_contact_on')) $baptism->first_contact_on = Carbon::createFromFormat('d.m.Y', $request->get('first_contact_on'));
        if ($request->get('appointment')) $baptism->appointment = Carbon::createFromFormat('d.m.Y', $request->get('appointment'));

        if ($request->hasFile('registration_document')) {
            $baptism->registration_document = $request->file('registration_document')->store('baptism', 'public');
        }

        $baptism->save();
        return redirect(route('services.edit', $serviceId).'#rites');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Baptism  $baptism
     * @return \Illuminate\Http\Response
     */
    public function show(Baptism $baptism)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Baptism  $baptism
     * @return \Illuminate\Http\Response
     */
    public function edit(Baptism $baptism)
    {
        return view('baptisms.edit', compact('baptism'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Baptism  $baptism
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Baptism $baptism)
    {
        $request->validate([
            'candidate_name' => 'required',
            'service' => 'required|integer',
        ]);

        $serviceId = $request->get('service');
        $baptism->candidate_name = $request->get('candidate_name') ?: '';
        $baptism->candidate_address= $request->get('candidate_address') ?: '';
        $baptism->candidate_zip = $request->get('candidate_zip') ?: '';
        $baptism->candidate_city = $request->get('candidate_city') ?: '';
        $baptism->candidate_phone = $request->get('candidate_phone') ?: '';
        $baptism->candidate_email = $request->get('candidate_email') ?: '';
        $baptism->first_contact_with = $request->get('first_contact_with') ?: '';
        if ($request->get('first_contact_on')) $baptism->first_contact_on = Carbon::createFromFormat('d.m.Y', $request->get('first_contact_on'));
        if ($request->get('appointment')) $baptism->appointment = Carbon::createFromFormat('d.m.Y', $request->get('appointment'));
        $baptism->registered= $request->get('registered') ? 1 : 0;
        $baptism->signed= $request->get('signed') ? 1 : 0;
        $baptism->docs_ready= $request->get('docs_ready') ? 1 : 0;
        $baptism->docs_where = $request->get('docs_where') ?: '';

        if ($request->hasFile('registration_document') || ($request->get('removeAttachment') == 1)) {
            if ($baptism->registration_document != '') {
                Storage::delete($baptism->registration_document);
            }
            $baptism->registration_document = '';
        }
        if ($request->hasFile('registration_document')) {
            $baptism->registration_document = $request->file('registration_document')->store('baptism', 'public');
        }

        $baptism->save();
        return redirect(route('services.edit', $serviceId).'#rites');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Baptism  $baptism
     * @return \Illuminate\Http\Response
     */
    public function destroy(Baptism $baptism)
    {
        $serviceId = $baptism->service_id;
        $baptism->delete();
        return redirect(route('services.edit', $serviceId).'#rites');
    }
}
