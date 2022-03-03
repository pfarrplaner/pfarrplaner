<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/pfarrplaner/pfarrplaner
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

use App\CalendarConnection;
use App\City;
use App\Facades\Settings;
use App\HomeScreen\Tabs\HomeScreenTabFactory;
use App\Http\Requests\UserRequest;
use App\Location;
use App\Mail\User\AccountData;
use App\Ministry;
use App\Parish;
use App\Rules\CreatedInLocalAdminDomainRule;
use App\Service;
use App\Services\PasswordService;
use App\Subscription;
use App\Traits\HandlesAttachedImageTrait;
use App\Traits\HandlesAttachmentsTrait;
use App\UI\Modules\Modules;
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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{

    use HandlesAttachmentsTrait;
    use HandlesAttachedImageTrait;

    protected $model = User::class;


    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $userQuery = User::with(['homeCities', 'cities', 'writableCities', 'adminCities', 'roles', 'roles.permissions'])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->orderBy('email');

        if (!Auth::user()->isAdmin) {
            if (Auth::user()->adminCities->count()) {
                // Local admin: can see anyone from his/her admin'd cities and people without homeCities
                $userQuery->where(function($q) {
                    $q->whereHas('homeCities', function ($q2) {
                        $q2->whereIn('cities.id', Auth::user()->homeCities->pluck('id'));
                    });
                })->orWhere(function ($q) {
                   $q->whereDoesntHave('homeCities');
                });
            } elseif (Auth::user()->can('benutzer-bearbeiten')) {
                // Clerk: Can only see users
                $userQuery->where('password', '!=', '');
            } elseif (Auth::user()->can('benutzerliste-lokal-sehen')) {
                // Local clerk: Can only see users from own cities
                $cityIds = Auth::user()->writableCities->pluck('id');
                $userQuery->whereHas('cities', function ($q) use ($cityIds) {
                    $q->whereIn('cities.id', $cityIds);
                });
            } else {
                abort(403);
            }
        }

        $canCreate = Auth::user()->can('create', User::class);

        return Inertia::render('Admin/User/Index', ['users' => $userQuery->get(), 'canCreate' => $canCreate]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $user = (new User())->load([
                                       'homeCities',
                                       'parishes',
                                       'roles',
                                       'cities',
                                       'writableCities',
                                       'adminCities',
                                       'vacationAdmins',
                                       'vacationApprovers',
                                   ]);

        $cities = City::orderBy('name')->get();

        $adminCities = [];
        foreach ($cities as $city) {
            if ($city->administeredBy(Auth::user())) {
                $adminCities[] = $city;
            }
        }

        return Inertia::render(
            'Admin/User/UserEditor',
            [
                'user' => $user,
                'cities' => $cities,
                'adminCities' => $adminCities,
                'roles' => Role::all(),
                'parishes' => Parish::all(),
                'users' => User::all(),
                'activeTab' => 'home',
                'subscriptions' => [],
                'settings' => [],
                'availableTabs' => HomeScreenTabFactory::available(),
                'locations' => Location::inCities(Auth::user()->cities->pluck('id'))->get(),
                'ministries' => Ministry::all(),
                'modules' => Modules::tree(),
            ]
        );


        $cities = City::all();
        $parishes = Parish::whereIn('city_id', Auth::user()->cities->pluck('id'))->get();
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
     * @param UserRequest $request
     * @return Response
     */
    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $user = User::create($data);
        $this->updateUserDataFromRequest($request, $user);

        return redirect()->route('users.index')->with('success', 'Der neue Benutzer wurde angelegt.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return \Inertia\Response
     */
    public function edit(User $user, Request $request)
    {
        $user->load([
                        'homeCities',
                        'parishes',
                        'roles',
                        'cities',
                        'writableCities',
                        'adminCities',
                        'vacationAdmins',
                        'vacationApprovers',
                    ]);
        $cities = City::orderBy('name')->get();
        $adminCities = [];
        $adminCityIds = [];
        foreach ($cities as $city) {
            if ($city->administeredBy(Auth::user())) {
                $adminCities[] = $city;
                $adminCityIds[] = $city->id;
            }
        }
        $roles = Role::all()->sortBy('name');
        $parishes = Parish::whereIn('city_id', Auth::user()->cities->pluck('id'))->get();
        $homescreen = $user->getSetting('homeScreen', 'route:calendar');
        $users = User::all();
        $subscriptions = Subscription::where('user_id', $user->id)
            ->whereIn('city_id', $adminCityIds)
            ->get();

        $homeScreenTabsConfig = $user->getSetting('homeScreenTabsConfig') ?? [];
        $settings = Settings::all($user);
        $availableTabs = HomeScreenTabFactory::available();
        $locations = Location::inCities(Auth::user()->cities->pluck('id'))->get();
        $ministries = Ministry::all();
        $modules = Modules::tree();


        $activeTab = $request->get('tab', 'home');

        return Inertia::render(
            'Admin/User/UserEditor',
            compact(
                'user',
                'cities',
                'adminCities',
                'homescreen',
                'roles',
                'parishes',
                'users',
                'activeTab',
                'subscriptions',
                'settings',
                'availableTabs',
                'locations',
                'ministries',
                'modules',
            )
        );
    }


    /**
     * @param Request $request
     * @return \Inertia\Response
     */
    public function profile(Request $request)
    {
        $user = Auth::user();
        $allCities = $user->visibleCities;
        $cities = $user->cities;

        $subscriptions = [];
        foreach ($cities as $city) {
            $subscriptions[$city->id] = $user->getSubscriptionType($city);
        }

        $sortedCities = $user->getSortedCities();

        // homeScreenTabs
        $homeScreenTabsConfig = $user->getSetting('homeScreenTabsConfig') ?? [];
        $settings = Settings::all($user);
        $availableTabs = HomeScreenTabFactory::available();

        $calendarConnections = CalendarConnection::where('user_id', $user->id)->get();
        $locations = Location::inCities(Auth::user()->cities->pluck('id'))->get();
        $ministries = Ministry::all();

        $tab = $request->get('tab', '');

        return Inertia::render(
            'Profile/ProfileEditor',
            compact(
                'user',
                'cities',
                'tab',
                'homeScreenTabsConfig',
                'availableTabs',
                'calendarConnections',
                'subscriptions',
                'locations',
                'ministries',
                'settings',
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
        $data = $this->validateRequest($request, $user);
        $user->update($data);

        // change password?
        if ($request->has('new_password')) {
            $passwordData = $request->validate([
                                                   'current_password' => 'required|hash:' . Auth::user()->password,
                                                   'new_password' => 'required|string|min:6|confirmed|not_current_password|notIn:testtest',
                                                   'new_password_confirmation' => 'required',
                                               ]);
            $user->update(['password' => $passwordData['new_password']]);
        }

        // set subscriptions
        $user->setSubscriptionsFromArray($request->get('subscriptions') ?: []);

        // settings
        if ($request->has('settings')) {
            foreach ($request->get('settings', []) as $key => $setting) {
                $user->setSetting($key, $setting);
            }
        }

        if ($request->has('homeScreenTabsConfig')) {
            $user->setSetting('homeScreenTabsConfig', $request->get('homeScreenTabsConfig'));
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
    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();
        $user->update($data);
        $this->updateUserDataFromRequest($request, $user);

        return redirect()->route('users.index')->with('success', 'Die Änderungen wurden gespeichert.');
    }

    /**
     * Update all user record relations from the request data
     * @param UserRequest $request
     * @param User $user
     */
    protected function updateUserDataFromRequest(UserRequest $request, User $user)
    {
        $user->homeCities()->sync($request->getRelationIdsForSync('home_cities', 'cities'));
        $user->parishes()->sync($request->getRelationIdsForSync('parishes'));
        $user->syncRelatedUsers(
            'vacationAdmins',
            'vacation_admin',
            $request->getRelationIdsForSync('vacation_admins', 'users')
        );
        $user->syncRelatedUsers(
            'vacationApprovers',
            'vacation_approver',
            $request->getRelationIdsForSync('vacation_approvers', 'users')
        );
        $user->roles()->sync($request->getRoles());

        foreach ($request->get('settings', []) as $key => $setting) {
            $user->setSetting($key, $setting);
        }
        $user->setSubscriptionsFromArray($request->get('subscriptions') ?: []);
        $user->updateCityPermissions($request->get('permissions') ?: []);

        if ($request->get('createAccount', false)) {
            $user->resetAccount();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return Response
     */
    public function destroy(User $user)
    {
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
        // save switch in session!
        return redirect()->route('home');
    }

    /**
     * @return RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

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
            'email' => 'nullable|string|email|max:255|unique:users,email' . ($user ? ',' . $user->id : ''),
            'password' => 'nullable|string',
            'office' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|phone_number',
            'preference_cities' => 'nullable|string',
            'manage_absences' => 'nullable|checkbox',
            'homeCities' => 'nullable',
            'homeCities.*' => 'int|exists:cities,id',
            'own_website' => 'nullable|string',
            'own_podcast_title' => 'nullable|string',
            'own_podcast_url' => 'nullable|string|url',
            'own_podcast_spotify' => 'nullable|checkbox',
            'own_podcast_itunes' => 'nullable|checkbox',
            'show_vacations_with_services' => 'nullable|checkbox',
            'needs_replacement' => 'nullable|checkbox',
        ];

        // special treatment if the submitter is a local admin
        if (Auth::user()->isLocalAdmin) {
            // on create:
            if ($request->route()->getName() == 'user.store') {
                // check if at least one permission is set
                $rules['cityPermission'] = [new CreatedInLocalAdminDomainRule()];

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
        $data['password'] = $data['password'] ?? '';
        if ((($user === null) || ($user->api_token == '')) && ($data['password'] != '')) {
            $data['api_token'] = Str::random(60);
        }

        return $data;
    }

    public function add(Request $request)
    {
        $data = $this->validateRequest($request);
        $user = User::create($data);
        return response()->json($user);
    }

    public function findDuplicates()
    {
        $allUsers = User::all();
        $possibleDuplicates = [];
        $withoutDuplicates = [];

        foreach ($allUsers as $user) {
            $usersWithSameName = User::with('homeCities')
                ->where('name', $user->name)
                ->where('id', '!=', $user->id)
                ->get();
            if (count($usersWithSameName) > 0) {
                $alreadyListed = false;
                foreach ($usersWithSameName as $thisUser) {
                    if (isset($possibleDuplicates[$thisUser->name])) {
                        $alreadyListed = true;
                        if ($thisUser->isOfficialUser && (!$possibleDuplicates[$thisUser->name]->isOfficialUser)) {
                            $thisUser->duplicates = $possibleDuplicates[$thisUser->name]->duplicates->reject(
                                function ($item) use ($thisUser) {
                                    return $item->id == $thisUser->id;
                                }
                            );
                            $possibleDuplicates[$thisUser->name]->duplicates = collect();
                            $thisUser->duplicates->push($possibleDuplicates[$thisUser->name]);
                            $possibleDuplicates[$thisUser->name] = $thisUser;
                        }
                    }
                }
                if (!$alreadyListed) {
                    $user->load('homeCities');
                    $usersWithSameName = $usersWithSameName->map(function ($item) {
                        $item->duplicates = collect();
                        $item->fullNameText = $item->fullName(true);
                        return $item;
                    });
                    $user->duplicates = $usersWithSameName;
                    $user->fullNameText = $user->fullName(true);
                    $possibleDuplicates[$user->name] = $user;
                }
            } else {
                $user->duplicates = collect();
                $user->fullNameText = $user->fullName(true);
                $withoutDuplicates[] = $user;
            }
        }
        $possibleDuplicates = array_values($possibleDuplicates);

        return Inertia::render('Admin/User/DuplicatesWizard', compact('possibleDuplicates', 'withoutDuplicates'));
    }

    public function fixDuplicates(Request $request)
    {
        foreach ($request->all() as $target => $duplicates) {
            $target = User::find($target);
            if ($target) {
                foreach ($duplicates as $duplicate) {
                    $duplicate = User::find($duplicate);
                    if ($duplicate) {
                        $duplicate->mergeInto($target);
                        $duplicate->delete();
                    }
                }
            }
        }
        return redirect()->route('users.duplicates');
    }

    public function resetPassword(Request $request, User $user)
    {
        if (!$request->user()->can('update', $user)) abort(403);
        $user->resetAccount();
        return redirect()->route('users.index')->with('success', 'Das Benutzerpasswort für '.$user->name.' wurde zurückgesetzt. Eine E-Mail mit neuen Zugangsdaten wurde an '.$user->email.' versandt.');
    }
}
