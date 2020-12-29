<?php

namespace App\Http\Controllers;

use App\Facades\Settings;
use App\User;
use App\UserSetting;
use Illuminate\Http\Request;

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
        $data = $request->validate(['value' => 'required']);
        $userSetting = Settings::set($user, $key, $data['value']);

        return response()->json($userSetting->only(['key', 'value']), 200);
    }
}
