<?php

namespace App\Http\Controllers;

use App\City;
use App\Service;
use Illuminate\Http\Request;

class PodcastController extends Controller
{
    public function podcast(Request $request, $cityName)
    {
        $city = City::where('name', $cityName)->first();
        if (null === $city) abort(404);

        $services = Service::where('city_id', $city->id)
            ->select('services.*')
            ->join('days', 'days.id', 'services.day_id')
            ->where('recording_url', '!=', '')
            ->orderBy('days.date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        return response()->view('podcasts.podcast', compact('city', 'services'))
            ->header('Content-Type', 'text/xml');
    }
}
