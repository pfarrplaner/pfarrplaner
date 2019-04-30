<?php

namespace App\Http\Controllers;

use App\Service;
use App\Wedding;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WeddingController extends Controller
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
     * @param int $serviceId Service Id
     * @return \Illuminate\Http\Response
     */
    public function create($serviceId)
    {
        $service = Service::find($serviceId);
        return view('weddings.create', compact('service'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'spouse1_name' => 'required',
            'spouse2_name' => 'required',
            'service' => 'required|integer',
        ]);

        $serviceId = $request->get('service');

        $wedding = new Wedding([
            'service_id' => $serviceId,
            'spouse1_name' => $request->get('spouse1_name') ?: '',
            'spouse1_birth_name' => $request->get('spouse1_birth_name') ?: '',
            'spouse1_phone' => $request->get('spouse1_phone') ?: '',
            'spouse1_email' => $request->get('spouse1_email') ?: '',
            'spouse2_name' => $request->get('spouse2_name') ?: '',
            'spouse2_birth_name' => $request->get('spouse2_birth_name') ?: '',
            'spouse2_phone' => $request->get('spouse2_phone') ?: '',
            'spouse2_email' => $request->get('spouse2_email') ?: '',
            'text' => $request->get('text') ?: '',
            'registered' => $request->get('registered') ? 1 : 0,
            'registration_document' => $request->get('candidate_email') ?: '',
            'signed' => $request->get('signed') ? 1 : 0,
            'docs_ready' => $request->get('docs_ready') ? 1 : 0,
            'docs_where' => $request->get('docs_where') ?: '',
        ]);
        if ($request->get('appointment')) $wedding->appointment = Carbon::createFromFormat('d.m.Y', $request->get('appointment'));

        if ($request->hasFile('registration_document')) {
            $wedding->registration_document = $request->file('registration_document')->store('wedding', 'public');
        }

        $wedding->save();
        return redirect(route('services.edit', $serviceId).'#rites');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Wedding  $wedding
     * @return \Illuminate\Http\Response
     */
    public function show(Wedding $wedding)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Wedding  $wedding
     * @return \Illuminate\Http\Response
     */
    public function edit(Wedding $wedding)
    {
        return view('weddings.edit', compact('wedding'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Wedding  $wedding
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Wedding $wedding)
    {
        $request->validate([
            'spouse1_name' => 'required',
            'spouse2_name' => 'required',
            'service' => 'required|integer',
        ]);

        $serviceId = $request->get('service');
        $wedding->spouse1_name = $request->get('spouse1_name') ?: '';
        $wedding->spouse1_birth_name = $request->get('spouse1_birth_name') ?: '';
        $wedding->spouse1_phone = $request->get('spouse1_phone') ?: '';
        $wedding->spouse1_email = $request->get('spouse1_email') ?: '';
        $wedding->spouse2_name = $request->get('spouse2_name') ?: '';
        $wedding->spouse2_birth_name = $request->get('spouse2_birth_name') ?: '';
        $wedding->spouse2_phone = $request->get('spouse2_phone') ?: '';
        $wedding->spouse2_email = $request->get('spouse2_email') ?: '';
        if ($request->get('appointment')) $wedding->appointment =  Carbon::createFromFormat('d.m.Y', $request->get('appointment'));
        $wedding->registered= $request->get('registered') ? 1 : 0;
        $wedding->signed= $request->get('signed') ? 1 : 0;
        $wedding->docs_ready= $request->get('docs_ready') ? 1 : 0;
        $wedding->docs_where = $request->get('docs_where') ?: '';

        if ($request->hasFile('registration_document') || ($request->get('removeAttachment') == 1)) {
            if ($wedding->registration_document != '') {
                Storage::delete($wedding->registration_document);
            }
            $wedding->registration_document = '';
        }
        if ($request->hasFile('registration_document')) {
            $wedding->registration_document = $request->file('registration_document')->store('baptism', 'public');
        }

        $wedding->save();
        return redirect(route('services.edit', $serviceId).'#rites');
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Wedding  $wedding
     * @return \Illuminate\Http\Response
     */
    public function destroy(Wedding $wedding)
    {
        $serviceId = $wedding->service_id;
        $wedding->delete();
        return redirect(route('services.edit', $serviceId).'#rites');
    }
}
