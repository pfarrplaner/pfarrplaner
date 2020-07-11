<?php

namespace App\Http\Controllers;

use App\CalendarLinks\AbstractCalendarLink;
use App\City;
use App\CalendarLinks\CalendarLinks;
use App\Location;
use App\Misc\VersionInfo;
use App\Service;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
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

    public function showChangePassword() {
        return view('auth.changepassword');
    }

    public function changePassword(Request $request){
        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with("error","Your current password does not match with the password you provided. Please try again.");
        }
        if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
            //Current password and new password are same
            return redirect()->back()->with("error","New Password cannot be same as your current password. Please choose a different password.");
        }
        $validatedData = $request->validate([
            'current-password' => 'required',
            'new-password' => 'required|string|min:6|confirmed',
        ]);
        //Change Password
        $user = Auth::user();
        $user->password = bcrypt($request->get('new-password'));
        $user->save();
        return redirect()->back()->with("success","Password changed successfully !");
    }

    public function connect() {
        /*
        $user = Auth::user();
        $token = $user->getToken();
        $cities = Auth::user()->visibleCities;
        $name = explode(' ', Auth::user()->name);
        $name = end($name);
        return view('ical.connect', ['user' => $user, 'token' => $token, 'cities' => $cities, 'name' => $name]);
        */
    }

    public function whatsnew() {
        $messages = VersionInfo::getMessages()->sortByDesc('date');
        Auth::user()->setSetting('new_features', \Carbon\Carbon::now());
        return view('whatsnew', compact('messages'));
    }

    public function counters($counter) {
        $data = [];
        switch ($counter) {
            case 'users':
                $count = count(User::where('password', '!=', '')->get());
                break;
            case 'services':
                $count = count(Service::whereHas('day', function($query) { $query->where('date', '>=', Carbon::now()); })->get());
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
