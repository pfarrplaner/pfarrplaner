<?php
/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 24.04.2019
 * Time: 14:32
 */

namespace App\HomeScreens;


use App\Baptism;
use App\Service;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PastorHomeScreen extends AbstractHomeScreen
{
    public function render()
    {
        /** @var User $user */
        $user = Auth::user();
        $name = $user->lastName();

        $start = Carbon::now()->setTime(0,0,0);
        $end = Carbon::now()->addMonth(2);

        $services = Service::with(['baptisms', 'weddings', 'funerals', 'location', 'day'])
            ->select(['services.*', 'days.date'])
            ->join('days', 'days.id', '=', 'day_id')
            ->where(function ($query) use ($name) {
                $query->where('pastor', 'like', '%' . $name . '%')
                    ->orWhere('organist', 'like', '%' . $name . '%')
                    ->orWhere('sacristan', 'like', '%' . $name . '%');
            })->whereHas('day', function ($query) use ($start, $end) {
                $query->where('date', '>=', $start)
                    ->where('date', '<=', $end);
            })
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->get();

        $end = Carbon::now()->addYear(1);

        $baptisms = Service::with(['baptisms', 'location', 'day'])
            ->select(['services.*', 'days.date'])
            ->join('days', 'days.id', '=', 'day_id')
            ->whereHas('baptisms')
            ->whereHas('day', function ($query) use ($start, $end) {
                $query->where('date', '>=', $start)
                    ->where('date', '<=', $end);
            })
            ->where(function ($query) use ($name) {
                $query->where('pastor', 'like', '%' . $name . '%')
                    ->orWhere('organist', 'like', '%' . $name . '%')
                    ->orWhere('sacristan', 'like', '%' . $name . '%');
            })
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->get();
        $baptisms->load('day');

        $funerals = Service::with(['funerals', 'location', 'day'])
            ->select(['services.*', 'days.date'])
            ->join('days', 'days.id', '=', 'day_id')
            ->whereHas('funerals')
            ->where(function ($query) use ($name) {
                $query->where('pastor', 'like', '%' . $name . '%')
                    ->orWhere('organist', 'like', '%' . $name . '%')
                    ->orWhere('sacristan', 'like', '%' . $name . '%');
            })
            ->whereHas('day', function ($query) use ($start, $end) {
                $query->where('date', '>=', $start);
            })
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->get();
        $funerals->load('day');

        $weddings = Service::with(['weddings', 'location', 'day'])
            ->select(['services.*', 'days.date'])
            ->join('days', 'days.id', '=', 'day_id')
            ->whereHas('weddings')
            ->where(function ($query) use ($name) {
                $query->where('pastor', 'like', '%' . $name . '%')
                    ->orWhere('organist', 'like', '%' . $name . '%')
                    ->orWhere('sacristan', 'like', '%' . $name . '%');
            })
            ->whereHas('day', function ($query) use ($start, $end) {
                $query->where('date', '>=', $start)
                    ->where('date', '<=', $end);
            })
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->get();
        $weddings->load('day');

        return view('homescreen.pastor', compact('user', 'services', 'funerals', 'baptisms', 'weddings'));
    }

}