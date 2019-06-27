<?php

namespace App;

use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

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
        'new_features'
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

    public function cities()
    {
        return $this->belongsToMany(City::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class)->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
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
        return md5($this->name . $this->password . env('TOKEN_SALT'));
    }

    public function lastName($withTitle = false)
    {
        if ($this->last_name) {
            return ($withTitle ? ($this->title ? $this->title.' ' : ''): '').$this->last_name;
        }
        $name = explode(' ', $this->name);
        return ($withTitle ? ($this->title ? $this->title.' ' : ''): '').end($name);
    }

    public function fullName($withTitle = false)
    {
        return ($withTitle ? ($this->title ? $this->title.' ' : ''): '').$this->name;
    }

    public function userSettings()
    {
        return $this->hasMany(UserSetting::class);
    }

    public function getSetting($key, $default = null, $returnObject = false)
    {
        $setting = UserSetting::where('key', $key)->where('user_id', $this->id)->first();
        if ((!$setting) && (!is_null($default))) {
            $setting = new UserSetting([
                'user_id' => $this->id,
                'key' => $key,
                'value' => $default,
            ]);
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
            $setting = new UserSetting([
                'user_id' => $this->id,
                'key' => $key,
                'value' => $value,
            ]);
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
        $subscription = $this->subscriptions()->where('city_id', is_int($city)? $city : $city->id)->first();
        if (null === $subscription) {
            $subscription = new Subscription([
                'user_id' => $this->id,
                'city_id' => $city->id,
                'subscription_type' => Subscription::SUBSCRIBE_OWN,
            ]);
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
            $subscription = new Subscription([
                'user_id' => $this->id,
                'city_id' => $city->id,
            ]);
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
        return $query->whereHas('subscriptions', function ($query) use ($service) {
            $query->where('city_id', $service->city_id);
            $query->where('subscription_type', Subscription::SUBSCRIBE_ALL);
        })->orWhere(function ($query) use ($service) {
            $query->whereIn('id', $service->participants->pluck('id'));
            $query->whereHas('subscriptions', function ($query) use ($service) {
                $query->where('city_id', $service->city_id);
                $query->where('subscription_type', Subscription::SUBSCRIBE_OWN);
            });
        });
    }

    public function setSubscriptionsFromArray($subscriptions)
    {
        foreach ($subscriptions as $city => $type) {
            $this->setSubscription($city, $type);
        }
    }
}