<?php

namespace App\Http\Controllers;

use App\City;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
        $cities = City::all();
        $roles = Role::all()->sortBy('name');
        return view('users.create', compact('cities', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'email',
            'cities' => 'required',
        ]);

        $user = new User([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'cities' => $request->get('cities'),
            'title' => $request->get('title') ?: '',
            'first_name' => $request->get('first_name') ?: '',
            'last_name' => $request->get('last_name') ?: '',
            'notifications' => $request->get('notifications') ? 1 : 0,
            'office' => $request->get('office') ?: '',
            'address' => $request->get('address') ?: '',
            'phone' => $request->get('phone') ?: '',
            'preference_cities' => '', // TODO: empty for now
        ]);
        $user->save();
        $user->cities()->sync($request->get('cities'));

        // assign roles
        $roles = $request->get('roles');
        if (is_array($roles) && count($roles)) {
            if ((($key = array_search('Superadministrator*in', $roles)) !== false)
                && (!$user->hasRole('Superadmininistrator*in')))
                unset($roles[$key]);
            if ((($key = array_search('Administrator*in', $roles)) !== false)
                && (!$user->hasRole('Superadmininistrator*in'))
                && (!$user->hasRole('Administrator*in')))
                unset($roles[$key]);
        }
        $user->syncRoles($request->get('roles') ?: []);


        if ($request->get('homescreen')) $user->setSetting('homeScreen', $request->get('homescreen'));

        return redirect()->route('users.index')->with('success', 'Der neue Benutzer wurde angelegt.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $user = User::find($id);
        $cities = City::all();
        $roles = Role::all()->sortBy('name');
        $homescreen = $user->getSetting('homeScreen', 'route:calendar');
        return view('users.edit', compact('user', 'cities', 'homescreen', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        $user = User::find($id);
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        if ($password = $request->get('password') != '') $user->password = Hash::make($password);
        $user->title = $request->get('title') ?: '';
        $user->first_name = $request->get('first_name') ?: '';
        $user->last_name = $request->get('last_name') ?: '';
        $user->notifications = $request->get('notifications') ? 1 : 0;
        $user->office = $request->get('office') ?: '';
        $user->address = $request->get('address') ?: '';
        $user->phone = $request->get('phone') ?: '';
        $user->preference_cities = join(',', $request->get('preference_cities') ?: []);
        $user->save();

        $user->cities()->sync($request->get('cities'));

        // assign roles
        $roles = $request->get('roles');
        if (is_array($roles) && count($roles)) {
            if ((($key = array_search('Superadministrator*in', $roles)) !== false)
                && (!$user->hasRole('Superadmininistrator*in')))
                unset($roles[$key]);
            if ((($key = array_search('Administrator*in', $roles)) !== false)
                && (!$user->hasRole('Superadmininistrator*in'))
                && (!$user->hasRole('Administrator*in')))
                unset($roles[$key]);
        }
        $user->syncRoles($request->get('roles') ?: []);

        if ($request->get('homescreen')) $user->setSetting('homeScreen', $request->get('homescreen'));

        return redirect()->route('users.index')->with('success', 'Die Änderungen wurden gespeichert.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Der Benutzer wurde gelöscht.');
    }

    public function preferences($id)
    {
        $user = User::find($id);
        $preferenceCities = City::whereIn('id', $user->preference_cities ?: $user->cities);
        $allowedCities = City::whereIn('id', $user->cities);
        foreach ($allowedCities as $city) {
            if ($preferenceCities->contains($city)) $allowedCities->forget($city);
        }
        return view('users.preferences', ['user' => $user, 'allowedCities' => $allowedCities, 'preferenceCities' => $preferenceCities]);
    }

    public function savePreferences(Request $request, $id)
    {

    }
}
