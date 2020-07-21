<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
 * @version git: $Id$
 *
 * Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
 *
 * Pfarrplaner is based on the Laravel framework (https://laravel.com).
 * This file may contain code created by Laravel's scaffolding functions.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App\Http\Controllers;

use App\City;
use App\Location;
use App\Misc\VersionInfo;
use App\Service;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Route /
     */
    public function root() {
        if (Auth::user()) {
            return redirect()->route('home');
        }
        return redirect()->route('login');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        $homeScreen = Auth::user()->getSetting('homeScreen', 'route:calendar');
        if (strpos($homeScreen, ':') !== false) {
            list($action, $data) = explode(':', $homeScreen);
            switch ($action) {
                case 'redirect':
                    return redirect($data);
                    break;
                case 'route':
                    return redirect(route($data));
                    break;
                case 'view':
                    return view($data);
                    break;
                case 'homescreen':
                    $homeScreenObj = Auth::user()->getHomeScreen();
                    if (null !== $homeScreenObj) {
                        return $homeScreenObj->render();
                    }
            }
        } else {
            return view($homeScreen);
        }
    }

    /**
     * @return Application|Factory|View
     */
    public function showChangePassword()
    {
        return view('auth.changepassword');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function changePassword(Request $request)
    {
        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with(
                "error",
                "Your current password does not match with the password you provided. Please try again."
            );
        }
        if (strcmp($request->get('current-password'), $request->get('new-password')) == 0) {
            //Current password and new password are same
            return redirect()->back()->with(
                "error",
                "New Password cannot be same as your current password. Please choose a different password."
            );
        }
        $validatedData = $request->validate(
            [
                'current-password' => 'required',
                'new-password' => 'required|string|min:6|confirmed',
            ]
        );
        //Change Password
        $user = Auth::user();
        $user->password = bcrypt($request->get('new-password'));
        $user->save();
        return redirect()->back()->with("success", "Password changed successfully !");
    }

    public function connect()
    {
        /*
        $user = Auth::user();
        $token = $user->getToken();
        $cities = Auth::user()->visibleCities;
        $name = explode(' ', Auth::user()->name);
        $name = end($name);
        return view('ical.connect', ['user' => $user, 'token' => $token, 'cities' => $cities, 'name' => $name]);
        */
    }

    /**
     * @return Application|Factory|View
     */
    public function whatsnew()
    {
        $messages = VersionInfo::getMessages()->sortByDesc('date');
        Auth::user()->setSetting('new_features', Carbon::now());
        return view('whatsnew', compact('messages'));
    }

    /**
     * @param $counter
     * @return JsonResponse
     */
    public function counters($counter)
    {
        $data = [];
        switch ($counter) {
            case 'users':
                $count = count(User::where('password', '!=', '')->get());
                break;
            case 'services':
                $count = count(
                    Service::whereHas(
                        'day',
                        function ($query) {
                            $query->where('date', '>=', Carbon::now());
                        }
                    )->get()
                );
                break;
            case 'locations':
                $count = count(Location::all());
                break;
            case 'cities':
                $count = count(City::all());
                break;
            case 'online':
                $data['users'] = visitor()->onlineVisitors(User::class);
                $count = count($data['users']);
                break;
        }
        return response()->json(compact('count', 'data'));
    }

}
