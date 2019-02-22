<?php

namespace App\Http\Controllers;

use App\City;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->isAdmin) return redirect()->back()->with('error', 'Leider haben Sie dafür keine Berechtigung.');
        $users = User::all();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->isAdmin) return redirect()->back()->with('error', 'Leider haben Sie dafür keine Berechtigung.');
        $cities = City::all();
        return view('users.create', ['cities' => $cities]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin) return redirect()->back()->with('error', 'Leider haben Sie dafür keine Berechtigung.');
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'cities' => 'required',
        ]);

        $user = new User([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'cities' => $request->get('cities'),
            'isAdmin' => $request->get('isAdmin') ? 1 : 0,
            'canEditChurch' => $request->get('canEditChurch') ? 1 : 0,
            'canEditGeneral' => $request->get('canEditGeneral') ? 1 : 0,
            'canEditFields' => join(',', $request->get('canEditField') ?: []),
            'notifications' => $request->get('notifications') ? 1 : 0,
            'office' => $request->get('office') ?: '',
            'address' => $request->get('address') ?: '',
            'phone' => $request->get('phone') ?: '',
        ]);
        $user->save();
        $user->cities()->sync($request->get('cities'));

        return redirect()->route('users.index')->with('success', 'Der neue Benutzer wurde angelegt.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->isAdmin) return redirect()->back()->with('error', 'Leider haben Sie dafür keine Berechtigung.');
        $user = User::find($id);
        $cities = City::all();
        return view('users.edit', ['user' => $user, 'cities' => $cities]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->isAdmin) return redirect()->back()->with('error', 'Leider haben Sie dafür keine Berechtigung.');
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
            'cities' => 'required',
        ]);

        $user = User::find($id);
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        if ($password = $request->get('password') != '') $user->password = Hash::make($password);
        $user->cities()->sync($request->get('cities'));
        $user->isAdmin = $request->get('isAdmin') ? 1 : 0;
        $user->canEditGeneral = $request->get('canEditGeneral') ? 1 : 0;
        $user->canEditChurch = $request->get('canEditChurch') ? 1 : 0;
        $user->canEditFields = join(',', $request->get('canEditField') ?: []);
        $user->notifications = $request->get('notifications') ? 1 : 0;
        $user->office = $request->get('office') ?: '';
        $user->address = $request->get('address') ?: '';
        $user->phone = $request->get('phone') ?: '';
        $user->preference_cities = join(',', $request->get('preference_cities') ?: []);
        $user->save();

        return redirect()->route('users.index')->with('success', 'Die Änderungen wurden gespeichert.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->isAdmin) return redirect()->back()->with('error', 'Leider haben Sie dafür keine Berechtigung.');
        $user = User::find($id);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Der Benutzer wurde gelöscht.');
    }

    public function preferences($id) {
        $user = User::find($id);
        $preferenceCities = City::whereIn('id', $user->preference_cities ?: $user->cities);
        $allowedCities = City::whereIn('id', $user->cities);
        foreach ($allowedCities as $city) {
            if ($preferenceCities->contains($city)) $allowedCities->forget($city);
        }
        return view('users.preferences', ['user' => $user, 'allowedCities' => $allowedCities, 'preferenceCities' => $preferenceCities]);
    }

    public function savePreferences(Request $request, $id) {

    }
}
