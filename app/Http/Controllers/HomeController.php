<?php

namespace App\Http\Controllers;

use App\City;
use App\Misc\VersionInfo;
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
                    $homeScreenClass = 'App\\HomeScreens\\'.ucfirst($data).'HomeScreen';
                    if (class_exists($homeScreenClass)) {
                        $homeScreenObj = new $homeScreenClass();
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
            return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
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

    public function connectWithOutlook() {
        $user = Auth::user();
        $token = $user->getToken();
        $cities = City::all();
        $name = explode(' ', Auth::user()->name);
        $name = end($name);
        return view('connectwithoutlook', ['user' => $user, 'token' => $token, 'cities' => $cities, 'name' => $name]);
    }

    public function whatsnew() {
        $messages = VersionInfo::getMessages()->sortByDesc('date');
        return view('whatsnew', compact('messages'));
    }
}
