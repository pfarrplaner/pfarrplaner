<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Sanctum\PersonalAccessToken;

class TokenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $tokens = Auth::user()->tokens;
        $token = null;
        return Inertia::render('tokens', compact('tokens', 'token'));
    }

    public function create(Request $request)
    {
        $request->validate(['title' => 'required|string']);
        $token = Auth::user()->createToken($request->get('title'));
        $tokens = Auth::user()->tokens;
        return Inertia::render('tokens', compact('tokens', 'token'));
    }

    public function destroy(Request $request, PersonalAccessToken $token)
    {
        Auth::user()->tokens()->where('id', $token->id)->delete();
        return redirect()->route('tokens.index');
    }
}
