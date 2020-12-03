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
use App\HomeScreen\Tabs\HomeScreenTabFactory;
use App\Parish;
use App\Rules\CreatedInLocalAdminDomain;
use App\Service;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if (Auth::user()->can('benutzer-bearbeiten')) {
            $users = User::where('password', '!=', '')->orderBy('email')->get();
        } elseif (Auth::user()->can('benutzerliste-lokal-sehen')) {
            $cityIds = Auth::user()->writableCities->pluck('id');
            $users = User::where('password', '!=', '')
                ->select('users.*')
                ->whereHas(
                    'cities',
                    function ($query) use ($cityIds) {
                        $query->whereIn('cities.id', $cityIds);
                    }
                )
                ->orderBy('email')->get();
        } else {
            return redirect()->route('home');
        }
        $otherPeople = User::where('password', '')->orWhereNull('password')->orderBy('name')->get();
        return view('users.index', compact('users', 'otherPeople'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $cities = City::all();
        $parishes = Parish::whereIn('city_id', Auth::user()->cities)->get();
        $users = User::all();
        $roles = $this->getRoles();
        return view('users.create', compact('cities', 'roles', 'parishes', 'users'));
    }

    /**
     * @return Collection|Role[]
     */
    protected function getRoles()
    {
        if (Auth::user()->hasRole('Super-Administrator*in')) {
            $roles = Role::all();
        } else {
            $roles = Role::where('name', '!=', 'Super-Administrator*in')->get();
        }
        return $roles->sortBy('name');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $user = User::create($this->validateRequest($request));

        if ($request->hasFile('image')) {
            $user->update(['image' => $request->file('image')->store('user-images', 'public')]);
        }

        // api_token

        $user->save();


        $user->updateCityPermissions($request->get('cityPermission') ?: []);
        $user->parishes()->sync($request->get('parishes') ?: []);

        $user->homeCities()->sync($request->get('homeCities') ?: []);

        if (count($user->homeCities) == 0) {
            if (count(Auth::user()->adminCities)) {
                $user->homeCities()->attach(Auth::user()->adminCities->first()->id);
            }
        }


        // assign roles
        $roles = $request->get('roles');
        if (is_array($roles) && count($roles)) {
            $key = array_search('Super-Administrator*in', $roles);
            if (($key !== false)
                && (!$user->hasRole('Superadmininistrator*in'))) {
                unset($roles[$key]);
            }
            $key = array_search('Administrator*in', $roles);
            if (($key !== false)
                && (!$user->hasRole('Superadmininistrator*in'))
                && (!$user->hasRole('Administrator*in'))) {
                unset($roles[$key]);
            }
        }
        $user->syncRoles($request->get('roles') ?: []);
        $user->approvers()->sync($request->get('approvers') ?: []);

        // set subscriptions
        $user->setSubscriptionsFromArray($request->get('subscribe') ?: []);


        if ($request->get('homescreen')) {
            $user->setSetting('homeScreen', $request->get('homescreen'));
        }

        return redirect()->route('users.index')->with('success', 'Der neue Benutzer wurde angelegt.');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id, Request $request)
    {
        $user = User::find($id);
        $cities = City::all();
        $roles = Role::all()->sortBy('name');
        $parishes = Parish::whereIn('city_id', Auth::user()->cities)->get();
        $homescreen = $user->getSetting('homeScreen', 'route:calendar');
        $users = User::all();
        return view('users.edit', compact('user', 'cities', 'homescreen', 'roles', 'parishes', 'users'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function profile(Request $request)
    {
        $user = Auth::user();
        $allCities = $user->visibleCities;
        $cities = $user->visibleCities;
        $sortedCities = $user->getSortedCities();
        $unusedCities = $allCities->whereNotIn('id', $sortedCities->pluck('id'));
        $calendarView = $user->getSetting('calendar_view', 'calendar.month');
        $homeScreen = $user->getHomeScreen();

        // homeScreenTabs
        $homeScreenTabsConfig = $user->getSetting('homeScreenTabsConfig') ?? [];
        $activeTabs = explode(',', $user->getSetting('homeScreenTabs', '')) ?? [];
        if ($activeTabs == [0 => '']) $activeTabs = [];
        $homeScreenTabsInactive = HomeScreenTabFactory::all($homeScreenTabsConfig);
        $homeScreenTabsActive = [];
        foreach ($activeTabs as $tab) {
            $homeScreenTabsActive[$tab] = $homeScreenTabsInactive[$tab];
            unset($homeScreenTabsInactive[$tab]);
        }

        $tab = $request->get('tab', '');
        return view(
            'users.profile',
            compact(
                'user',
                'cities',
                'sortedCities',
                'unusedCities',
                'calendarView',
                'homeScreen',
                'tab',
                'homeScreenTabsActive',
                'homeScreenTabsInactive',
                'activeTabs',
                'homeScreenTabsConfig'
            )
        );
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function profileSave(Request $request)
    {
        $user = Auth::user();
        $user->email = $request->get('email');
        $user->office = $request->get('office') ?: '';
        $user->address = $request->get('address') ?: '';
        $user->phone = $request->get('phone') ?: '';
        $user->preference_cities = join(',', $request->get('preference_cities') ?: []);

        // api_token
        if ($request->get('api_token') != '') {
            $user->api_token = $request->get('api_token');
        }

        $user->save();

        // set subscriptions
        $user->setSubscriptionsFromArray($request->get('subscribe') ?: []);

        // homescreen configuration
        $homeScreen = $user->getHomeScreen();
        if (null !== $homeScreen) {
            $homeScreen->setConfiguration($request);
        }
        if ($request->has('homeScreenTabs')) {
            $user->setSetting('homeScreenTabs', $request->get('homeScreenTabs'));
        }

        // settings
        if ($request->has('settings')) {
            foreach ($request->get('settings') as $settingKey => $setting) {
                $user->setSetting($settingKey, $setting);
            }
        }



        return redirect()->route('home')->with('success', 'Die Änderungen wurden gespeichert.');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, User $user)
    {
        if ($user->administeredBy(Auth::user())) {
            $user->update($this->validateRequest($request, $user));
            if ($request->hasFile('image') || ($request->get('removeAttachment') == 1)) {
                if ($user->image != '') {
                    Storage::delete($user->image);
                }
                $user->update(['image' => '']);
            }
            if ($request->hasFile('image')) {
                $user->update(['image' => $request->file('image')->store('user-images', 'public')]);
            }

            $user->homeCities()->sync($request->get('homeCities') ?: []);
            $user->parishes()->sync($request->get('parishes') ?: []);
            $user->approvers()->sync($request->get('approvers') ?: []);
            // assign roles
            $roles = $request->get('roles');
            if (is_array($roles) && count($roles)) {
                $key = array_search('Super-Administrator*in', $roles);
                if (($key !== false)
                    && (!$user->hasRole('Superadmininistrator*in'))) {
                    unset($roles[$key]);
                }
                $key = array_search('Administrator*in', $roles);
                if (($key !== false)
                    && (!$user->hasRole('Superadmininistrator*in'))
                    && (!$user->hasRole('Administrator*in'))) {
                    unset($roles[$key]);
                }
            }
            $user->syncRoles($request->get('roles') ?: []);

            // set subscriptions
            $user->setSubscriptionsFromArray($request->get('subscribe') ?: []);
        } else {
            $password = $request->get('password');
            if ($password != '') {
                $user->update(['password' => $password]);
                if (count($user->homeCities) == 0) {
                    if (count(Auth::user()->adminCities)) {
                        $user->homeCities()->attach(Auth::user()->adminCities->first()->id);
                    }
                }
            }
        }

        $user->updateCityPermissions($request->get('cityPermission') ?: []);

        if ($request->get('homescreen')) {
            $user->setSetting('homeScreen', $request->get('homescreen'));
        }

        return redirect()->route('users.index')->with('success', 'Die Änderungen wurden gespeichert.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Der Benutzer wurde gelöscht.');
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function preferences($id)
    {
        $user = User::find($id);
        $preferenceCities = City::whereIn('id', $user->preference_cities ?: $user->cities);
        $allowedCities = City::whereIn('id', $user->cities);
        foreach ($allowedCities as $city) {
            if ($preferenceCities->contains($city)) {
                $allowedCities->forget($city);
            }
        }
        return view('users.preferences', compact('user', 'allowedCities', 'preferenceCities'));
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function savePreferences(Request $request, $id)
    {
    }

    /**
     * @param User $user
     * @return Application|Factory|View
     */
    public function join(User $user)
    {
        $users = User::where('id', '!=', $user->id)->orderBy('name')->get();
        return view('users.join', compact('user', 'users'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function doJoin(Request $request)
    {
        $request->validate(
            [
                'user1' => 'required|integer',
                'user2' => 'required|integer',
            ]
        );

        $user1Id = $request->get('user1');
        $user2Id = $request->get('user2');

        $user1 = User::findOrFail($user1Id);
        $user2 = User::findOrFail($user2Id);

        $this->authorize('delete', $user1);
        $this->authorize('update', $user2);

        if (null === $user1 || null === $user2) {
            return redirect()->route('home')->with('error', 'Ein Fehler ist aufgetreten.');
        }

        // replace all the pivot records

        DB::statement(
            'UPDATE service_user SET user_id=:user2Id WHERE user_id=:user1Id;',
            compact('user1Id', 'user2Id')
        );

        // delete old user
        $user1->delete();

        return redirect()->route('users.index')->with('success', 'Die Benutzer wurden zusammengeführt.');
    }


    /**
     * @param User $user
     * @return Application|Factory|View
     */
    public function services(User $user)
    {
        $services = Service::with('location', 'day', 'participants')
            ->select('services.*')
            ->join('days', 'days.id', '=', 'services.day_id')
            ->whereHas(
                'participants',
                function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }
            )->orderBy('days.date', 'ASC')
            ->orderBy('time')
            ->get();
        return view('users.services', compact('user', 'services'));
    }

    /**
     * @param User $user
     * @return RedirectResponse
     */
    public function switch(User $user)
    {
        if (!Auth::user()->isAdmin) {
            abort(403);
        }
        Auth::logout();
        Auth::login($user);
        return redirect()->route('home');
    }

    /**
     * @return RedirectResponse
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }


    /**
     * Check if the user is a local admin and has set city permissions
     * @param Request $request
     */
    protected function validateCityPermissions(Request $request)
    {
        if (Auth::user()->isLocalAdmin) {
            $permissions = $request->get('cityPermission');
            dd(Auth::user()->adminCities->pluck('id'), $permissions);
        }
    }


    /**
     * Validate submitted data
     * @param Request $request
     * @param User|null $user
     * @return array
     */
    protected function validateRequest(Request $request, $user = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'title' => 'nullable|string',
            'email' => 'nullable|email',
            'password' => 'nullable|string',
            'notifications' => 'nullable|string',
            'office' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|phone_number',
            'preference_cities' => 'nullable|string',
            'manage_absences' => 'nullable|string',
            'homeCities' => 'nullable',
            'homeCities.*' => 'int|exists:cities,id',
        ];

        // special treatment if the submitter is a local admin
        if (Auth::user()->isLocalAdmin) {
            // on create:
            if ($request->route()->getName() == 'users.store') {
                // check if at least one permission is set
                $rules['cityPermission'] = [new CreatedInLocalAdminDomain()];

                // force setting a password
                $rules['email'] = 'required|email';
                $rules['password'] = 'required|string';
            }
        }

        // if a password is set, an email is required
        if ($request->get('password', '') != '') {
            $rules['email'] = 'required|email';
        }

        $data = $request->validate($rules);

        // api token
        if (($user === null) || ($user->api_token == '')) {
            $data['api_token'] = $data['password'] == '' ? '' : Str::random(60);
        }

        return $data;
    }
}
