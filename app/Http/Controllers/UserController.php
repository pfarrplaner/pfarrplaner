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
        $users = User::where('password', '!=', '')->orderBy('email')->get();
        $otherPeople = User::where('password', '')->orWhereNull('password')->orderBy('name')->get();
        return view('users.index', compact('users', 'otherPeople'));
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
            'manage_absences' => $request->get('manage_absences') ? 1 : 0,
        ]);
        $user->save();
        $this->updateCityPermissions($user, $request->get('cityPermission') ?: []);

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

        // set subscriptions
        $user->setSubscriptionsFromArray($request->get('subscribe') ?: []);


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

    public function profile(Request $request)
    {
        $user = Auth::user();
        $allCities = $user->visibleCities;
        $cities = $user->visibleCities;
        $sortedCities = $user->getSortedCities();
        $unusedCities = $allCities->whereNotIn('id', $sortedCities->pluck('id'));
        $calendarView = $user->getSetting('calendar_view', 'calendar.month');
        return view('users.profile', compact('user', 'cities', 'sortedCities', 'unusedCities', 'calendarView'));
    }

    public function profileSave(Request $request)
    {
        $user = Auth::user();
        $user->email = $request->get('email');
        $user->office = $request->get('office') ?: '';
        $user->address = $request->get('address') ?: '';
        $user->phone = $request->get('phone') ?: '';
        $user->preference_cities = join(',', $request->get('preference_cities') ?: []);
        $user->save();

        // set subscriptions
        $user->setSubscriptionsFromArray($request->get('subscribe') ?: []);

        // set preferences
        $user->setSetting('sorted_cities', $request->get('citySort'));
        $user->setSetting('calendar_view', $request->get('calendar_view'));

        return redirect()->route('home')->with('success', 'Die Änderungen wurden gespeichert.');
    }

    protected function updateCityPermissions(User $user, $permissions)
    {
        $previousCities = $user->visibleCities->pluck('id');
        $addedCities = [];

        foreach ($permissions as $cityId => $permission) {
            if ($permission['permission'] == 'n') {
                unset($permissions[$cityId]);
            } else {
                if (!$previousCities->contains($cityId)) $addedCities[] = $cityId;
            }
        }
        $user->cities()->sync($permissions);

        // update the user's own sorting (remove cities which are not allowed)
        $cityIds = array_keys($permissions);
        $userPref = explode(',',$user->getSetting('sorted_cities', ''));
        if (!count($userPref)) {
            $userPref = $cityIds;
        } else {
            foreach ($userPref as $key => $city) {
                if (!in_array($city, $cityIds)) unset($userPref[$key]);
            }
            // make added cities visible without having to edit user preference in profile:
            $userPref = array_merge($userPref, $addedCities);
        }
        $user->setSetting('sorted_cities', join(',', $userPref));

        // update the user's subscriptions (remove cities which are not allowed)
        $subscriptions = $user->subscriptions;
        foreach ($subscriptions as $subscription) {
            if (!in_array($subscription->city_id, $cityIds)) $subscription->delete();
        }
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
        //if ($password = $request->get('password') != '') $user->password = Hash::make($password);
        if (($password = $request->get('password')) != '') {
            $user->password = Hash::make($password);
        }
        $user->title = $request->get('title') ?: '';
        $user->first_name = $request->get('first_name') ?: '';
        $user->last_name = $request->get('last_name') ?: '';
        $user->notifications = $request->get('notifications') ? 1 : 0;
        $user->office = $request->get('office') ?: '';
        $user->address = $request->get('address') ?: '';
        $user->phone = $request->get('phone') ?: '';
        $user->preference_cities = join(',', $request->get('preference_cities') ?: []);
        $user->manage_absences = $request->get('manage_absences') ? 1 : 0;
        $user->save();

        $this->updateCityPermissions($user, $request->get('cityPermission') ?: []);

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

        // set subscriptions
        $user->setSubscriptionsFromArray($request->get('subscribe') ?: []);

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
        return view('users.preferences', compact('user', 'allowedCities', 'preferenceCities'));
    }

    public function savePreferences(Request $request, $id)
    {
    }
}
