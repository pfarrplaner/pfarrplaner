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

namespace App;

use App\HomeScreens\AbstractHomeScreen;
use App\Mail\User\AccountData;
use App\Providers\AuthServiceProvider;
use App\Services\PasswordService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\HasApiTokens;
use Shetabit\Visitor\Traits\Visitable;
use Shetabit\Visitor\Traits\Visitor;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;
use App\Facades\Settings;
use Venturecraft\Revisionable\Revision;

/**
 * Class User
 * @package App
 */
class User extends Authenticatable
{
    use Notifiable, HasRoles, Visitable, Visitor, HasApiTokens;

    /**
     *
     */
    public const NAME_FORMAT_DEFAULT = 1;
    /**
     *
     */
    public const NAME_FORMAT_INITIAL_AND_LAST = 2;
    /**
     *
     */
    public const NAME_FORMAT_FIRST_AND_LAST = 3;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'title',
        'email',
        'password',
        'notifications',
        'office',
        'address',
        'phone',
        'preference_cities',
        'canEditOfferings',
        'canEditCC',
        'new_features',
        'manage_absences',
        'api_token',
        'own_website',
        'own_podcast_title',
        'own_podcast_url',
        'own_podcast_spotify',
        'own_podcast_itunes',
        'show_vacations_with_services',
        'needs_replacement',
        'image',
        'must_change_password',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'new_features',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'isOfficialUser',
        'isAdmin',
        'isLocalAdmin',
        'sortName',
    ];

    /**
     * @var string
     */
    protected $orderBy = 'name';
    /**
     * @var string
     */
    protected $orderDirection = 'ASC';

    /** @var string[] cached user settings  */
    protected $settings = [];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('name', 'asc');
        });
    }

// ACCESSORS
    /**
     * @return array
     */
    public function getAllPermissionsAttribute()
    {
        $permissions = [];
        foreach (Permission::all() as $permission) {
            if (Auth::user()->can($permission->name)) {
                $permissions[] = $permission->name;
            }
        }
        return $permissions;
    }

    /**
     * @return bool
     */
    public function getIsAdminAttribute()
    {
        return $this->hasRole('Administrator*in') || $this->hasRole('Super-Administrator*in');
    }

    /**
     * @return bool
     */
    public function getIsLocalAdminAttribute()
    {
        return (!$this->isAdmin) && (count($this->adminCities) >0);
    }

    /**
     * @return string
     */
    public function getPlanNameAttribute()
    {
        return $this->lastName(true);
    }

    /**
     * @return City[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getWritableCitiesAttribute()
    {
        if ((!Auth::guest()) && Auth::user()->hasRole(AuthServiceProvider::SUPER)) {
            return City::all();
        }
        $writableCities = $this->writableCities()->get();
        $replacements = $this->currentReplacements();
        if (null !== $replacements) {
            foreach ($replacements as $replacement) {
                foreach ($replacement->absence->user->writableCities as $city) $writableCities->push($city);
            }
        }
        return $writableCities->unique();
    }


    public function currentReplacements()
    {
        return Replacement::with('absence')
            ->whereHas('users', function($query) {
                $query->where('user_id', $this->id);
            })
            ->where('from', '<=', Carbon::now())
            ->where('to', '>=', Carbon::now())
            ->get();
    }

    /**
     * @return BelongsToMany
     */
    public function writableCities()
    {
        return $this->belongsToMany(City::class)->withPivot('permission')->wherePivotIn('permission', ['w', 'a']);
    }

    /**
     * @return City[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAdminCitiesAttribute()
    {
        if ($this->hasRole(AuthServiceProvider::SUPER)) {
            return City::all();
        }
        return $this->adminCities()->get();
    }

    /**
     * @return BelongsToMany
     */
    public function adminCities()
    {
        return $this->belongsToMany(City::class)->withPivot('permission')->wherePivotIn('permission', ['a']);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }
// END ACCESSORS

// MUTATORS
    /**
     * Automatically hash password
     * @param $value
     */
    public function setPasswordAttribute($value) {
        if ($value != '') $this->attributes['password'] = Hash::make($value);
    }
// END MUTATORS

// SCOPES
    /**
     * Query for users subscribed to a specific service
     *
     * This requires one of two condition sets to be true:
     * 1) User is subscribed to all services for this city
     * 2) User is subscribed to own services for this city AND is a participant in this service
     *
     * @param Builder $query
     * @param Service $service
     * @param bool $isNew True, if this is a newly created service
     * @return mixed
     */
    public function scopeSubscribedTo(Builder $query, Service $service, $isNew = false)
    {
        $query->whereHas(
            'subscriptions',
            function ($query) use ($service) {
                $query->where('city_id', $service->city_id);
                $query->where('subscription_type', Subscription::SUBSCRIBE_ALL);
            }
        )->orWhere(
            function ($query) use ($service) {
                $query->whereIn('id', $service->participants->pluck('id'));
                $query->whereHas(
                    'subscriptions',
                    function ($query) use ($service) {
                        $query->where('city_id', $service->city_id);
                        $query->where('subscription_type', Subscription::SUBSCRIBE_OWN);
                    }
                );
            }
        );

        // some users only get notified when time or day_id change
        if ($isNew || isset($service->changes['time']) || isset($service->changes['day_id'])) {
            $query->orWhereHas('subscriptions',
                function ($query) use ($service) {
                    $query->where('city_id', $service->city_id);
                    $query->where('subscription_type', Subscription::SUBSCRIBE_TIME_CHANGES);
                }
            );
        }

        return $query;
    }
// END SCOPES

// SETTERS
    /**
     * @param $name
     * @return int|mixed|string
     */
    public static function createIfNotExists($name)
    {
        if ((!is_numeric($name)) || (User::find($name) === false)) {
            $title = $firstName = $lastName = '';
            if (false === strpos($name, '_')) {
                // split participant name into its parts
                $tmp = explode(' ', $name);
                if (count($tmp) == 3) {
                    $title = array_shift($tmp);
                }
                if (count($tmp) == 2) {
                    $firstName = array_shift($tmp);
                }
                $lastName = array_shift($tmp);
            } elseif ((substr($name, 0, 1) == '"') && (substr($name, -1, 1) == '"')) {
                // allow submitting participant names in double quotes ("participant name"), which will prevent splitting
                $name = substr($name, 1, -1);
            } else {
                // allow submitting participant name with an underscore, which will prevent splitting
                $name = str_replace('_', ' ', $name);
            }
            $user = new User(
                [
                    'name' => $name,
                    'office' => '',
                    'phone' => '',
                    'address' => '',
                    'preference_cities' => '',
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'title' => $title,
                ]
            );
            $user->save();
            $name = $user->id;
        }
        return $name;
    }

    /**
     * @return BelongsToMany
     */
    public function visibleCities()
    {
        return $this->cities();
    }

    /**
     * Cities to which the user has at least read access
     * @return BelongsToMany
     */
    public function cities()
    {
        return $this->belongsToMany(City::class)
            ->withPivot('permission');
    }

    /**
     * @param $city
     * @return string
     */
    public function permissionForCity($city)
    {
        if (is_numeric($city)) {
            $city = City::find($city);
        }
        /** @var City $city */
        return $this->cities()->where('cities.id', $city->id)->first()->pivot->permission ?? 'n';
    }

    /**
     * @return BelongsToMany
     */
    public function writableCitiesWithoutAdmin()
    {
        return $this->belongsToMany(City::class)->withPivot('permission')->wherePivotIn('permission', ['w']);
    }

    /**
     * @return BelongsToMany
     */
    public function homeCities()
    {
        return $this->belongsToMany(City::class, 'user_home');
    }

    /**
     * @return BelongsToMany
     */
    public function services()
    {
        return $this->belongsToMany(Service::class)
            ->withTimestamps()
            ->withPivot('category');
    }

    /**
     * @return HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return BelongsToMany
     */
    public function parishes()
    {
        return $this->belongsToMany(Parish::class)->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function approvers()
    {
        return $this->belongsToMany(User::class, 'user_approver', 'user_id', 'approver_id');
    }

    /**
     * @return BelongsToMany
     */
    public function relatedUsers() {
        return $this->belongsToMany(User::class, 'user_user', 'user_id', 'related_user_id')->withPivot('relation');
    }

    /**
     * @return BelongsToMany
     */
    public function vacationAdmins() {
        return $this->belongsToMany(User::class, 'user_user', 'user_id', 'related_user_id')->wherePivot('relation', '=', 'vacation_admin');
    }

    /**
     * @return BelongsToMany
     */
    public function vacationApprovers() {
        return $this->belongsToMany(User::class, 'user_user', 'user_id', 'related_user_id')->wherePivot('relation', 'vacation_approver');
    }

    /**
     * @return HasMany
     */
    public function calendarConnections() {
        return $this->hasMany(CalendarConnection::class);
    }

    /**
     * @param $area
     * @return bool
     */
    public function canEdit($area)
    {
        if (in_array($area, ['general', 'church'])) {
            $property = ucfirst($area);
            return $this->isAdmin || $this->$property;
        } else {
            return $this->canEditField($area);
        }
    }

    /**
     * @param $field
     * @return bool
     */
    public function canEditField($field)
    {
        return $this->isAdmin || in_array($field, $this->getEditableFields());
    }

    /**
     * @return false|string[]
     */
    public function getEditableFields()
    {
        return explode(',', $this->canEditFields);
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->api_token;
    }

    /**
     * Get the user's name in one of the predefined formats
     * @param int $format
     * @param bool $withTitle
     * @return string
     */
    public function formattedName($format = self::NAME_FORMAT_DEFAULT, $withTitle = true)
    {
        switch ($format) {
            case self::NAME_FORMAT_INITIAL_AND_LAST:
                return $this->initialedName($withTitle);
                break;
            case self::NAME_FORMAT_FIRST_AND_LAST:
                return $this->fullName($withTitle);
                break;
        }
        return $this->lastName($withTitle);
    }

    /**
     * @param bool $withTitle
     * @return string
     */
    public function initialedName($withTitle = false)
    {
        return ($withTitle ? ($this->title ? $this->title . ' ' : '') : '')
            . ($this->first_name ? substr($this->first_name, 0, 1) . '. ' : '')
            . $this->last_name;
    }

    /**
     * @param bool $withTitle
     * @return string
     */
    public function fullName($withTitle = false)
    {
        return ($withTitle ? ($this->title ? $this->title . ' ' : '') : '') . $this->name;
    }

    /**
     * @param bool $withTitle
     * @return string
     */
    public function lastName($withTitle = false)
    {
        if ($this->last_name) {
            return ($withTitle ? ($this->title ? $this->title . ' ' : '') : '') . $this->last_name;
        }
        $name = explode(' ', $this->name);
        return ($withTitle ? ($this->title ? $this->title . ' ' : '') : '') . end($name);
    }

    /**
     * @return HasMany
     */
    public function userSettings()
    {
        return $this->hasMany(UserSetting::class);
    }

    /**
     * @param $key
     * @throws Exception
     */
    public function forgetSetting($key)
    {
        if ($this->hasSetting($key)) {
            $setting = $this->getSetting($key, null, true);
            $setting->delete();
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasSetting($key)
    {
        return Settings::has($this, $key);
    }

    /**
     * @param $key
     * @param null $default
     * @param bool $returnObject
     * @param bool $unserialize
     * @return UserSetting|mixed
     */
    public function getSetting($key, $default = null, $returnObject = false, $unserialize = true)
    {
        return Settings::get($this, $key, $default, $returnObject, $unserialize);
    }

    /**
     * Get an instance of the HomeScreen for this user
     * @return AbstractHomeScreen|null
     */
    public function getHomeScreen()
    {
        $homeScreen = $this->getSetting('homeScreen', 'route:calendar');
        if (substr($homeScreen, 0, 11) == 'homescreen:') {
            $homeScreenClass = 'App\\HomeScreens\\' . ucfirst(substr($homeScreen, 11)) . 'HomeScreen';
            if (class_exists($homeScreenClass)) {
                return new $homeScreenClass();
            }
        }
        return null;
    }

    /**
     * Get the users subscription for a city
     * @param $city
     * @return Subscription
     */
    public function getSubscriptionType($city)
    {
        $subscription = $this->getSubscription($city)->subscription_type;
        return $subscription;
    }

    /**
     * Get a single subscription for a specific city
     *
     * If no subscription is present yet, it will be created with type SUBSCRIBE_NONE
     *
     * @param int|City $city
     * @return bool
     */
    public function getSubscription($city)
    {
        $cityId = is_int($city) ? $city : $city->id;
        $subscription = $this->subscriptions()->where('city_id', $cityId)->first();
        if (null === $subscription) {
            $subscription = new Subscription(
                [
                    'user_id' => $this->id,
                    'city_id' => $cityId,
                    'subscription_type' => Subscription::SUBSCRIBE_OWN,
                ]
            );
            $subscription->save();
        }
        return $subscription;
    }

    /**
     * @return HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * @param $subscriptions
     */
    public function setSubscriptionsFromArray($subscriptions)
    {
        foreach ($subscriptions as $city => $type) {
            $this->setSubscription($city, $type);
        }
    }

    /**
     * Set the user's subscription for a city
     * @param City|int $city City
     * @param int $type Subscription type
     * @return Subscription
     */
    public function setSubscription($city, int $type)
    {
        $city = is_int($city) ? City::find($city) : $city;
        $subscription = $this->getSubscription($city);
        if (null === $subscription) {
            $subscription = new Subscription(
                [
                    'user_id' => $this->id,
                    'city_id' => $city->id,
                ]
            );
        }
        $subscription->subscription_type = $type;
        $subscription->save();
        return $subscription;
    }

    /**
     * @return mixed
     */
    public function getSortedCities()
    {
        if ($this->hasSetting('sorted_cities')) {
            $ids = explode(',', $this->getSetting('sorted_cities'));
            return City::whereIn('id', $ids)->get()->sortBy(
                function ($model) use ($ids) {
                    return array_search($model->getKey(), $ids);
                }
            );
        } else {
            // default if user preference not set:
            $cities = $this->visibleCities;
            $this->setSetting('sorted_cities', join(',', $cities->pluck('id')->toArray()));
            return $cities;
        }
    }

    /**
     * @param $key
     * @param $value
     */
    public function setSetting($key, $value)
    {
        return Settings::set($this, $key, $value);
    }

    /**
     * @param $date
     * @param bool $returnAbsence
     * @return bool|Builder|Model|object|null
     * @throws Exception
     */
    public function isAbsent($date, $returnAbsence = false)
    {
        if (!$this->manage_absences) {
            return ($returnAbsence ? null : false);
        }
        if (!is_a($date, Carbon::class)) {
            $date = new Carbon($date);
        }
        $absences = Absence::with('user')
            ->where('user_id', $this->id)
            ->where('from', '<=', $date)
            ->where('to', '>=', $date)
            ->first();
        return (null !== $absences) ? ($returnAbsence ? $absences : null) : ($returnAbsence ? null : false);
    }

    /**
     * @param $date
     * @param bool $returnAbsence
     * @return bool|null
     * @throws Exception
     */
    public function isReplacement($date, $returnAbsence = false)
    {
        if (!$this->manage_absences) {
            return ($returnAbsence ? null : false);
        }
        if (!is_a($date, Carbon::class)) {
            $date = new Carbon($date);
        }
        $absences = Absence::where('replacement', $this->id)
            ->where('from', '<=', $date)
            ->where('to', '>=', $date)
            ->first();
        return (null !== $absences) ? ($returnAbsence ? $absences : null) : ($returnAbsence ? null : false);
    }

    /**
     * @param $date
     * @param bool $returnServices
     * @return bool|null
     */
    public function isBusy($date, $returnServices = false)
    {
        $services = Service::whereHas(
            'day',
            function ($query) use ($date) {
                $query->where('date', $date);
            }
        )->whereHas(
            'participants',
            function ($query) {
                $query->where('user_id', $this->id);
            }
        )->get();
        return (count($services) ? ($returnServices ? $services : null) : ($services ? null : false));
    }

    /**
     * Find all users for which this user may see the absences
     *
     * Permission logic:
     * (A) Pastors may see all fellow pastors in their district (cities with view rights) and all staff in their home cities
     * (B) Staff may see all fellow staff in their home city, if "fremden-urlaub-bearbeiten" permission is set
     * (C) All others only see themselves
     * (D) Users without the manage_absences flag see nothing at all
     */
    public function getViewableAbsenceUsers()
    {
        if (!$this->manage_absences) {
            if (!$this->isAdmin) {
                return new Collection();
            } else {
                if (Cache::has('viewAbleAbsenceUsers__'.$this->id)) {
                    return Cache::get('viewAbleAbsenceUsers__'.$this->id);
                } else {
                    $users = User::where('manage_absences', 1)->orderBy('last_name')->orderBy('first_name')->get();
                    Cache::put('viewAbleAbsenceUsers__'.$this->id, $users);
                    return $users;
                }
            }
        }
        $ids = [];
        $ids[] = $this->id;

        $userQuery = User::where('manage_absences', 1)
            ->where('id', $this->id);

        if ($this->hasRole('Pfarrer*in') || $this->hasPermissionTo('fremden-urlaub-bearbeiten')) {
            $newIds = User::whereHas(
                'homeCities',
                function ($query) {
                    $query->whereIn('cities.id', $this->homeCities->pluck('id'));
                }
            )->where('manage_absences', 1)
                ->get()->pluck('id')->toArray();
            $ids = array_merge($ids, $newIds);
        }

        if ($this->hasRole('Pfarrer*in')) {
            $newIds = User::where(
                function ($query2) {
                    $query2->whereHas(
                        'roles',
                        function ($query) {
                            $query->where('name', 'Pfarrer*in');
                        }
                    );
                    $query2->whereHas(
                        'homeCities',
                        function ($query) {
                            $query->whereIn('cities.id', $this->cities->pluck('id'));
                        }
                    );
                }
            )->get()->pluck('id')->toArray();
            $ids = array_merge($ids, $newIds);
        }

        $ids = array_unique($ids);

        $userQuery = User::whereIn('id', $ids);
        $userQuery->orderBy('last_name');
        $userQuery->orderBy('first_name');

        $users = $userQuery->get();

        return $users;
    }

    /**
     * Update this users permissions from an array
     * Format [ city_id => ['permission' => level]]
     * @param $permissions
     */
    public function updateCityPermissions($permissions)
    {
        // check user rights, remove entries for cities without admin rights
        foreach ($permissions as $cityId => $permission) {
            $city = City::find($cityId);
            if (!$city->administeredBy($this)) {
                unset($permission);
            }
        }

        // add missing cities
        foreach ($this->visibleCities as $city) {
            if (!isset($permissions[$city->id])) {
                $permissions[$city->id] = ['permission' => $city->pivot->permission];
            }
        }

        foreach ($permissions as $cityId => $permission) {
            if ($permission['permission'] == 'n') {
                unset($permissions[$cityId]);
            }
        }
        $result = $this->cities()->sync($permissions);

        // update the user's own sorting (remove cities which are not allowed)
        $thisPref = explode(',', $this->getSetting('sorted_cities', ''));
        if (!count($thisPref)) {
            $thisPref = $result['attached'];
        } else {
            foreach ($thisPref as $key => $city) {
                if (in_array($city, $result['detached'])) {
                    unset($thisPref[$key]);
                }
            }
            // make added cities visible without having to edit user preference in profile:
            $thisPref = array_merge($thisPref, $result['attached']);
        }
        $this->setSetting('sorted_cities', join(',', $thisPref));

        // update the user's subscriptions (remove cities which are not allowed)
        $subscriptions = $this->subscriptions;
        foreach ($subscriptions as $subscription) {
            if (in_array($subscription->city_id, $result['detached'])) {
                $subscription->delete();
            }
        }
    }

    /**
     * Check if another user has admin rights for this user
     * This is the case, if the other user administers one of this users homeCities
     * @param $user User User to be checked for admin rights
     * @return bool True if other user has admin rights for this one
     */
    public function administeredBy($user)
    {
        if ($user->hasRole('Super-Administrator*in')) {
            return true;
        }
        foreach ($this->homeCities as $city) {
            if ($city->administeredBy($user)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function absencesToBeApproved()
    {
        $approvableUsers = $this->approvableUsers();
        return Absence::whereIn('user_id', $approvableUsers->pluck('id'))
            ->where('status', 'pending')->get();
    }

    /**
     * @return mixed
     */
    public function approvableUsers()
    {
        $id = $this->id;
        return User::whereHas(
            'approvers',
            function ($query) use ($id) {
                $query->where('approver_id', $id);
            }
        )->get();
    }

    /**
     * Check if the user has at least one of an array of permissions
     * @param string[] $permissions
     * @param mixed $data
     * @return bool
     */
    public function canAny($permissions, $data = null)
    {
        $can = false;
        foreach ($permissions as $permission) {
            $can = $can || $this->can($permission, $data);
        }
        return $can;
    }


    /**
     * Ensure certain user settings are present, if not, set a sensible default
     */
    public function ensureDefaultSettings()
    {
        $needReload = false;
        $settings = Settings::all($this);
        $defaults = config('user-settings.defaults');
        foreach ($defaults as $key => $defaultSetting) {
            if (!isset($settings[$key])) {
                $settings[$key] = $defaultSetting;
                Settings::set($this, $key, $defaultSetting);
            } else {
                if (is_array($defaultSetting)) {
                    if (is_array($settings[$key])) {
                        $settings[$key] = array_replace_recursive($defaultSetting, $settings[$key]);
                    } else {
                        $settings[$key] = $defaultSetting;
                    }
                    Settings::set($this, $key, $settings[$key]);
                }
            }
        }
    }

    public function getIsOfficialUserAttribute() {
        return !empty($this->password);
    }

    public function mergeInto(User $user)
    {
        if ((!$user->isOfficialUser) && $this->isOfficialUser) {
            $user->password = $this->password;
            $user->cities()->sync($this->cities->pluck('id'));
            $user->writableCities()->sync($this->writableCities->pluck('id'));
            $user->adminCities()->sync($this->adminCities->pluck('id'));
            $user->roles()->sync($this->roles->pluck('id'));
            $user->permissions()->sync($this->permissions->pluck('id'));
            $user->update([
                'manage_absences' => $this->manage_absences,
                'preference_cities' => $this->preference_cities,
                          ]);

        }
        Absence::where('user_id', $this->id)->update(['user_id' => $user->id]);
        Approval::where('user_id', $this->id)->update(['user_id' => $user->id]);
        CalendarConnection::where('user_id', $this->id)->update(['user_id' => $user->id]);
        Comment::where('user_id', $this->id)->update(['user_id' => $user->id]);
        Participant::where('user_id', $this->id)->update(['user_id' => $user->id]);
        Revision::where('user_id', $this->id)->update(['user_id' => $user->id]);
        Subscription::where('user_id', $this->id)->update(['user_id' => $user->id]);
        UserSetting::where('user_id', $this->id)->update(['user_id' => $user->id]);

        foreach ($this->parishes as $parish) {
            /** @var Parish $parish */
            $parish->users()->detach($this->id);
            $parish->users()->attach($user->id);
        }

        foreach ($this->teams as $team) {
            /** @var Team $team */
            $team->users()->detach($this->id);
            $team->users()->attach($user->id);
        }

        $srcUser = $this;
        foreach (Replacement::whereHas('users', function ($query) use ($srcUser) {
            $query->where('user_id', $srcUser);
        }) as $replacement) {
            $replacement->update(['user_id' => $user->id]);
        }
    }

    /**
     * Sync related users list for a specific kind of relation
     * @param $relation Kind of relation
     * @param $relationString Relation pivot string
     * @param $userIds User ids
     */
    public function syncRelatedUsers($relation, $relationString, $userIds)
    {
        $this->$relation()->sync([]);
        foreach ($userIds as $userId) {
            $this->$relation()->attach([$userId => ['relation' => $relationString]]);
        }
    }


    public function getSortNameAttribute()
    {
        return ($this->last_name && $this->first_name) ? $this->last_name.', '.$this->first_name : $this->name;
    }

    public function getImageField()
    {
        return 'image';
    }

    /**
     * Reset password and send AccountData mail to user
     */
    public function resetAccount()
    {
        $password = PasswordService::randomPassword();
        $this->update(['password' => Hash::make($password), 'must_change_password' => 1]);
        Mail::to($this->email)->send(new AccountData($this, Auth::user(), $password));
    }
}
