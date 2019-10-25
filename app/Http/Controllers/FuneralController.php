<?php

namespace App\Http\Controllers;

use App\City;
use App\Day;
use App\Funeral;
use App\Location;
use App\Mail\ServiceCreated;
use App\Service;
use App\Subscription;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
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
        $wizard = Session::get('wizard', 0);
        return view('funerals.create', compact('service', 'wizard'));
    }


    public function wizardStep1(Request $request) {
        $cities = Auth::user()->writableCities;
        return view('funerals.wizard.step1', compact('cities'));
    }

    public function wizardStep2(Request $request) {
        $request->validate([
            'date' => 'required|date|date_format:d.m.Y',
            'city' => 'required|integer',
        ]);

        $cityId = $request->get('city');
        $date = Carbon::createFromFormat('d.m.Y', $request->get('date'))->setTime(0,0,0);

        $city = City::find($cityId);

        // check if day exists
        $day = Day::where('date', $date)->first();
        if ($day) {
            // check if it visible for this city
            if ($day->day_type == Day::DAY_TYPE_LIMITED) {
                if (!$day->cities->contains($city)) $day->cities()->attach($city);
            }
        } else {
            // create day
            $day = new Day([
               'date' => $date,
                'name' => '',
                'description' => '',
                'day_type' => Day::DAY_TYPE_LIMITED,
            ]);
            $day->save();
            $day->cities()->sync([$cityId]);
        }

        $locations = Location::where('city_id', $cityId)->get();

        return view('funerals.wizard.step2', compact('day', 'city', 'locations'));

    }

    public function wizardStep3(Request $request)
    {
        $request->validate([
            'day' => 'required|integer',
            'city' => 'required|integer',
        ]);

        $city = City::find($request->get('city'));
        $day = Day::find($request->get('day'));

        if ($specialLocation = ($request->get('special_location') ?: '')) {
            $locationId = 0;
            $time = $request->get('time') ?: '';
            $ccLocation = $request->get('cc_location') ?: '';
        } else {
            $locationId = $request->get('location_id') ?: 0;
            if ($locationId) {
                $location = Location::find($locationId);
                $time = $request->get('time') ?: $location->default_time;
                $ccLocation = $request->get('cc_location') ?: ($request->get('cc') ? $location->cc_default_location : '');
            } else {
                $time = $request->get('time') ?: '';
                $ccLocation = $request->get('cc_location') ?: '';
            }
        }

        // create the service
        $service = new Service([
            'day_id' => $day->id,
            'location_id' => $locationId,
            'time' => $time,
            'special_location' => $specialLocation,
            'city_id' => $city->id,
            'others' => '',
            'description' => '',
            'need_predicant' => 0,
            'baptism' => 0,
            'eucharist' => 0,
            'offerings_counter1' => '',
            'offerings_counter2' => '',
            'offering_goal' => '',
            'offering_description' => '',
            'offering_type' => '',
            'cc' => 0,
            'cc_location' => '',
            'cc_lesson' => '',
            'cc_staff' => '',
        ]);
        $service->save();
        if (Auth::user()->hasRole('Pfarrer*in')) {
            $service->pastors()->sync([Auth::user()->id => ['category' => 'P']]);
        }
        Session::flash('wizard', 1);

        return redirect(route('funeral.add', compact('service')));

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
            'relative_contact_data' => $request->get('relative_contact_data') ?: '',
            'wake_location' => $request->get('wake_location') ?: '',
        ]);
        if ($request->get('announcement')) $funeral->announcement = Carbon::createFromFormat('d.m.Y', $request->get('announcement'));
        if ($request->get('wake')) $funeral->announcement = Carbon::createFromFormat('d.m.Y', $request->get('wake'));
        $funeral->save();

        // delayed notification after wizard completion:
        if ($request->get('wizard') == 1) {
            Subscription::send(Service::find($serviceId), ServiceCreated::class);
            Session::remove('wizard');
        }

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
        $funeral->relative_contact_data = $request->get('relative_contact_data') ?: '';
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


    public function done(Funeral $funeral) {
        $funeral->done = true;
        $funeral->save();
        return json_encode(true);
    }

}
