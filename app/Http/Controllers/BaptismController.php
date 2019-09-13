<?php

namespace App\Http\Controllers;

use App\Baptism;
use App\City;
use App\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

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

    protected function getBaptismalServices($baptismFlag = 1) {
        return Service::where('baptism', '=', $baptismFlag)
            ->select('services.*')
            ->join('days', 'services.day_id', '=', 'days.id')
            ->whereIn('city_id', Auth::user()->cities->pluck('id'))
            ->whereHas('day', function ($query) {
                $query->where('date', '>=', Carbon::now());
            })
            ->orderBy('days.date', 'ASC')
            ->orderBy('time')
            ->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param int $serviceId Service Id
     * @return \Illuminate\Http\Response
     */
    public function create($serviceId = null)
    {
        $service = null;
        if ($serviceId) $service = Service::find($serviceId);

        $baptismalServices = $this->getBaptismalServices();
        $otherServices = $this->getBaptismalServices(0);

        $cities = Auth::user()->writableCities;

        return view('baptisms.create', compact('service', 'baptismalServices', 'otherServices', 'cities'));
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
            'city_id' => 'required|integer',
        ]);

        $baptism = new Baptism([
            'city_id' => $request->get('city_id'),
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
        $serviceId = $request->get('service') ?: '';
        if ($serviceId) {
            $baptism->service_id = $serviceId;
        }
        if ($request->get('first_contact_on')) $baptism->first_contact_on = Carbon::createFromFormat('d.m.Y', $request->get('first_contact_on'));
        if ($request->get('appointment')) $baptism->appointment = Carbon::createFromFormat('d.m.Y H:i', $request->get('appointment'));

        if ($request->hasFile('registration_document')) {
            $baptism->registration_document = $request->file('registration_document')->store('baptism', 'public');
        }

        $baptism->save();
        if ($serviceId) {
            return redirect(route('services.edit', ['service' => $serviceId, 'tab' => 'rites']));
        } else {
            return redirect(route('home'));
        }
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

        $baptismalServicesQuery = Service::where('baptism', '=', 1)
            ->select('services.*')
            ->join('days', 'services.day_id', '=', 'days.id')
            ->whereIn('city_id', Auth::user()->cities->pluck('id'))
            ->whereHas('day', function ($query) {
                $query->where('date', '>=', Carbon::now());
            })
            ->orderBy('days.date', 'ASC')
            ->orderBy('time');
        if ($baptism->service_id) {
            $baptismalServicesQuery->orWhere('services.id', (int)$baptism->service_id);
        }
        $baptismalServices = $baptismalServicesQuery->get();

        $cities = City::all();

        $otherServices = $this->getBaptismalServices(0);
        return view('baptisms.edit', compact('baptism', 'baptismalServices', 'otherServices', 'cities'));
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
            'city_id' => 'required|integer',
        ]);

        $serviceId = $request->get('service');
        if ($serviceId) {
            $baptism->service_id = $serviceId;
        } else {
            $baptism->service_id = null;
        }
        $baptism->city_id = $request->get('city_id');
        $baptism->candidate_name = $request->get('candidate_name') ?: '';
        $baptism->candidate_address= $request->get('candidate_address') ?: '';
        $baptism->candidate_zip = $request->get('candidate_zip') ?: '';
        $baptism->candidate_city = $request->get('candidate_city') ?: '';
        $baptism->candidate_phone = $request->get('candidate_phone') ?: '';
        $baptism->candidate_email = $request->get('candidate_email') ?: '';
        $baptism->first_contact_with = $request->get('first_contact_with') ?: '';
        if ($request->get('first_contact_on')) $baptism->first_contact_on = Carbon::createFromFormat('d.m.Y', $request->get('first_contact_on'));
        if ($request->get('appointment')) $baptism->appointment = Carbon::createFromFormat('d.m.Y H:i', $request->get('appointment'));
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
        if ($serviceId) {
            return redirect(route('services.edit', ['service' => $serviceId, 'tab' => 'rites']));
        } else {
            return redirect(route('home'));
        }
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
        if ($serviceId) {
            return redirect(route('services.edit', ['service' => $serviceId, 'tab' => 'rites']));
        } else {
            return redirect(route('home'));
        }
    }

    public function appointmentIcal(Baptism $baptism) {
        $service = Service::find($baptism->service_id);
        $raw = View::make('baptisms.appointment.ical', compact('baptism', 'service'));
        $raw = str_replace("\r\n\r\n", "\r\n", str_replace('@@@@', "\r\n", str_replace("\n", "\r\n", str_replace("\r\n", '@@@@', $raw))));
        return response($raw, 200)
            ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->header('Expires', '0')
            ->header('Content-Type', 'text/calendar')
            ->header('Content-Disposition', 'inline; filename=Taufgespraech-'.$baptism->id.'.ics');
    }
}
