<?php

namespace App\Http\Controllers;

use App\Facades\Settings;
use App\User;
use App\UserSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function set(Request $request, User $user, $key)
    {
        if ($user->id != \Auth::user()->id) {
            abort(403);
        }
        $data = $request->validate(['value' => 'nullable']);
        $userSetting = Settings::set($user, $key, $data['value']);

        if ($request->header('x-inertia')) {
            return redirect()->back();
        } else{
            return response()->json($userSetting->only(['key', 'value']), 200);
        }
    }
}
