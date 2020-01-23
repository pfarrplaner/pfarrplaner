<?php

namespace App;

use App\Providers\AuthServiceProvider;
use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Shetabit\Visitor\Traits\Visitable;
use Shetabit\Visitor\Traits\Visitor;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles, Visitable, Visitor;

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
    ];

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

    protected $orderBy = 'name';
    protected $orderDirection = 'ASC';

    /**
     * Cities to which the user has at least read access
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cities()
    {
        return $this->belongsToMany(City::class)
            ->withPivot('permission');
    }

    public function visibleCities()
    {
        return $this->cities();
    }

    public function writableCities()
    {
        return $this->belongsToMany(City::class)->withPivot('permission')->wherePivotIn('permission', ['w', 'a']);
    }

    public function writableCitiesWithoutAdmin()
    {
        return $this->belongsToMany(City::class)->withPivot('permission')->wherePivotIn('permission', ['w']);
    }

    public function adminCities()
    {
        return $this->belongsToMany(City::class)->withPivot('permission')->wherePivotIn('permission', ['a']);
    }

    public function homeCities()
    {
        return $this->belongsToMany(City::class, 'user_home');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class)
            ->withTimestamps()
            ->withPivot('category');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function parishes()
    {
        return $this->belongsToMany(Parish::class)->withTimestamps();
    }

    public function approvers()
    {
        return $this->belongsToMany(User::class, 'user_approver', 'user_id', 'approver_id');
    }

    public function canEditField($field)
    {
        return $this->isAdmin || in_array($field, $this->getEditableFields());
    }

    public function canEdit($area)
    {
        if (in_array($area, ['general', 'church'])) {
            $property = ucfirst($area);
            return $this->isAdmin || $this->$property;
        } else {
            return $this->canEditField($area);
        }
    }

    public function getEditableFields()
    {
        return explode(',', $this->canEditFields);
    }

    public function getToken()
    {
        return $this->api_token;
    }

    public function lastName($withTitle = false)
    {
        if ($this->last_name) {
            return ($withTitle ? ($this->title ? $this->title . ' ' : '') : '') . $this->last_name;
        }
        $name = explode(' ', $this->name);
        return ($withTitle ? ($this->title ? $this->title . ' ' : '') : '') . end($name);
    }

    public function fullName($withTitle = false)
    {
        return ($withTitle ? ($this->title ? $this->title . ' ' : '') : '') . $this->name;
    }

    public function initialedName($withTitle = false)
    {
        return ($withTitle ? ($this->title ? $this->title . ' ' : '') : '')
            . ($this->first_name ? substr($this->first_name, 0, 1) . '. ' : '')
            . $this->last_name;
    }

    public function userSettings()
    {
        return $this->hasMany(UserSetting::class);
    }

    public function getSetting($key, $default = null, $returnObject = false)
    {
        $setting = UserSetting::where('key', $key)->where('user_id', $this->id)->first();
        if ((!$setting) && (!is_null($default))) {
            $setting = new UserSetting(
                [
                    'user_id' => $this->id,
                    'key' => $key,
                    'value' => $default,
                ]
            );
        }
        return ($returnObject ? $setting : $setting->value);
    }

    public function hasSetting($key)
    {
        return (UserSetting::where('key', $key)->where('user_id', $this->id)->get()->count() > 0);
    }

    public function setSetting($key, $value)
    {
        if ($this->hasSetting($key)) {
            $setting = $this->getSetting($key, null, true);
            $setting->value = $value;
        } else {
            $setting = new UserSetting(
                [
                    'user_id' => $this->id,
                    'key' => $key,
                    'value' => $value,
                ]
            );
        }
        $setting->save();
    }

    public function forgetSetting($key)
    {
        if ($this->hasSetting($key)) {
            $setting = $this->getSetting($key, null, true);
            $setting->delete();
        }
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

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
     * Get a single subscription for a specific city
     *
     * If no subscription is present yet, it will be created with type SUBSCRIBE_NONE
     *
     * @param int|City $city
     * @return bool
     */
    public function getSubscription($city)
    {
        $subscription = $this->subscriptions()->where('city_id', is_int($city) ? $city : $city->id)->first();
        if (null === $subscription) {
            $subscription = new Subscription(
                [
                    'user_id' => $this->id,
                    'city_id' => $city->id,
                    'subscription_type' => Subscription::SUBSCRIBE_OWN,
                ]
            );
            $subscription->save();
        }
        return $subscription;
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
     * Query for users subscribed to a specific service
     *
     * This requires one of two condition sets to be true:
     * 1) User is subscribed to all services for this city
     * 2) User is subscribed to own services for this city AND is a participant in this service
     *
     * @param Builder $query
     * @param Service $service
     * @return mixed
     */
    public function scopeSubscribedTo(Builder $query, Service $service)
    {
        return $query->whereHas(
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
    }

    public function setSubscriptionsFromArray($subscriptions)
    {
        foreach ($subscriptions as $city => $type) {
            $this->setSubscription($city, $type);
        }
    }


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

    public function getIsAdminAttribute()
    {
        return $this->hasRole('Administrator*in') || $this->hasRole('Super-Administrator*in');
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
                return User::where('manage_absences', 1)->orderBy('last_name')->orderBy('first_name')->get();
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

    public function getPlanNameAttribute()
    {
        return $this->lastName(true);
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

    public function getWritableCitiesAttribute()
    {
        if (Auth::user()->hasRole(AuthServiceProvider::SUPER)) {
            return City::all();
        }
        return $this->writableCities()->get();
    }

    public function getAdminCitiesAttribute()
    {
        if (Auth::user()->hasRole(AuthServiceProvider::SUPER)) {
            return City::all();
        }
        return $this->adminCities()->get();
    }

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

    public function absencesToBeApproved()
    {
        $approvableUsers = $this->approvableUsers();
        return Absence::whereIn('user_id', $approvableUsers->pluck('id'))
            ->where('status', 'pending')->get();
    }

}
