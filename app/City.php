<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name',
        'public_events_calendar_url',
        'default_offering_goal',
        'default_offering_description',
        'default_funeral_offering_goal',
        'default_funeral_offering_description',
        'default_wedding_offering_goal',
        'default_wedding_offering_description',
        'op_domain',
        'op_customer_key',
        'op_customer_token',
        'podcast_title',
        'podcast_logo',
        'sermon_default_image',
        'homepage',
        'podcast_owner_name',
        'podcast_owner_email',
        'google_auth_code',
        'google_access_token',
        'google_refresh_token',
        'youtube_channel_url',
        'konfiapp_apikey',
    ];

    protected $orderBy = 'name';
    protected $orderDirection = 'ASC';

    public function locations() {
        return $this->hasMany(Location::class);
    }

    public function services() {
        return $this->hasManyThrough(Service::class, Location::class);
    }

    /**
     * Check if this city is administered by a particular use
     * @param User $user User
     * @return bool True if user has admin rights here
     */
    public function administeredBy(User $user) {
        if ($user->hasRole('Super-Administrator*in')) return true;
        if (($city = $user->cities->where('id', $this->id)->first()) && ($city->pivot->permission == 'a')) return true;
        return false;
    }
}
