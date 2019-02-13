<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

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
        'notifications'
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
        return md5($this->name . $this->password.env('TOKEN_SALT'));
    }

    public function lastName() {
        $name = explode(' ', $this->name);
        return end($name);
    }
}
