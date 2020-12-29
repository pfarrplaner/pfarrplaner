<?php

namespace App\Http\Controllers;

use App\Booking;
use App\Location;
use App\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class CheckInController extends Controller
{

    public function create(Location $location)
    {
        $service = Service::where('location_id', $location->id)
            ->select('services.*')
            ->join('days', 'days.id', 'services.day_id')
            ->whereHas(
                'day',
                function ($query) {
                    $query->where('date', '>=', Carbon::now());
                }
            )
            ->where('needs_reservations', '!=', 1)
            ->orderBy('days.date')
            ->orderBy('time')
            ->first();

        return view('checkin.create', compact('location', 'service'));
    }

    public function store(Request $request, Service $service)
    {
        $data = $request->validate(
            [
                'name' => 'string|required',
                'first_name' => 'string|required',
                'address' => 'string|required',
                'zip' => 'zip|required',
                'city' => 'string|required',
                'phone' => 'string|required',
                'number' => 'int|required',
            ]
        );
        $data['service_id'] = $service->id;
        $data['contact'] = $data['address']."\n".$data['zip'].' '.$data['city']."\n".$data['phone'];
        $data['code'] = Booking::createCode();
        $booking = Booking::create($data);
        return view('checkin.store', compact('service', 'booking'));
    }

    public function qr(Location $location)
    {
        $pdf = PDF::loadView('checkin.qr', compact('location'), [], ['format' => 'A4']);
        return $pdf->stream('QR Check-In '.$location->name.'.pdf');
    }

}
