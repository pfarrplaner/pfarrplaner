<?php

namespace App;

use AustinHeap\Database\Encryption\Traits\HasEncryptedAttributes;
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
        'email',
        'password',
        'isAdmin',
        'canEditGeneral',
        'canEditChurch',
        'canEditFields',
        'notifications',
        'office',
        'address',
        'phone',
        'preference_cities',
        'canEditOfferings',
        'canEditCC',
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

    public function lastName()
    {
        $name = explode(' ', $this->name);
        return end($name);
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
}
