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
use App\Liturgy;
use App\Service;
use App\User;
use Carbon\Carbon;

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
        $service->load(['location', 'city', 'participants', 'weddings', 'funerals', 'baptisms', 'day', 'tags', 'serviceGroups']);
        $service->liturgy = Liturgy::getDayInfo($service->day);
        if (isset($liturgy['title']) && ($service->day->name == '')) $service->day->name = $liturgy['title'];
            return response()->json($service);
    }


    public function byUser(User $user) {
        $services = Service::select('services.*')
            ->with('location', 'city', 'participants', 'funerals', 'baptisms', 'weddings', 'day')
            ->join('days', 'days.id', 'services.day_id')
        ->whereHas('participants', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereHas('day', function($query) {
                $query->where('date', '>=', Carbon::now());
            })->orderBy('days.date')->get();


        foreach ($services as $service) {
            $service->liturgy = Liturgy::getDayInfo($service->day);
        }
        return response()->json(compact('services'))->header('Access-Control-Allow-Origin', '*');
    }

}