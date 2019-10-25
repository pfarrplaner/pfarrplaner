<?php

namespace App\Http\Controllers;

use App\Absence;
use App\City;
use App\Service;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class ICalController extends Controller
{
    protected function checkToken($token)
    {
        $users = User::all();
        $found = false;
        foreach ($users as $user) {
            $found = $found || ($user->getToken() == $token);
        }
        if (!$found) die('wrong token');
    }

    public function private($name, $token)
    {
        $this->checkToken($token);
        $services = Service::with(['day', 'location'])
            ->where(function ($query) use ($name) {
                $query->where('pastor', 'like', '%' . $name . '%')
                    ->orWhere('organist', 'like', '%' . $name . '%')
                    ->orWhere('sacristan', 'like', '%' . $name . '%');
            })->get();
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        header('Content-Type: text/calendar');
        $raw = View::make('ical.ical', ['services' => $services, 'action' => 'private', 'token' => $token, 'key' => $name]);

        $raw = str_replace("\r\n\r\n", "\r\n", str_replace('@@@@', "\r\n", str_replace("\n", "\r\n", str_replace("\r\n", '@@@@', $raw))));
        die ($raw);
    }

    public function byLocation($locationIds, $token)
    {
        $this->checkToken($token);
        $services = Service::with(['day', 'location'])
            ->whereIn('city_id', explode(',', $locationIds))
            ->get();
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        header('Content-Type: text/calendar');
        $raw = View::make('ical.ical', ['services' => $services, 'action' => 'gemeinden', 'token' => $token, 'key' => $locationIds]);

        $raw = str_replace("\r\n\r\n", "\r\n", str_replace('@@@@', "\r\n", str_replace("\n", "\r\n", str_replace("\r\n", '@@@@', $raw))));
        die ($raw);

    }



    /*
     * Export absences as ical
     *
     */
    public function absences(User $user, $token)
    {
        $this->checkToken($token);

        $users = $user->getViewableAbsenceUsers();
        $userId = $user->id;
        $absences = Absence::whereIn('user_id', $users->pluck('id'))
        ->orWhere(function ($query2)  use ($userId) {
            $query2->whereHas('replacements', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });
        })->get();

        $raw = View::make('ical.absences', compact('user', 'absences', 'token'));
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        header('Content-Type: text/calendar');
        $raw = str_replace("\r\n\r\n", "\r\n", str_replace('@@@@', "\r\n", str_replace("\n", "\r\n", str_replace("\r\n", '@@@@', $raw))));
        die($raw);
    }
}
