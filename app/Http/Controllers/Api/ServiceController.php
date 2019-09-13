<?php
/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 17.08.2019
 * Time: 11:09
 */

namespace App\Http\Controllers\Api;


use App\City;
use App\Day;
use App\Http\Controllers\Controller;
use App\Service;

class ServiceController extends Controller
{

    public function byDayAndCity(Day $day, City $city) {
        return Service::select('id')
            ->where('city_id', $city->id)
            ->where('day_id', '=', $day->id)
            ->orderBy('time')
            ->get()
            ->pluck('id');
    }

    public function show(Service $service) {
        $service->load(['location', 'city', 'participants', 'weddings', 'funerals', 'baptisms']);
        return response()->json($service);
    }

}