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
    ];

    protected $orderBy = 'name';
    protected $orderDirection = 'ASC';

    public function locations() {
        return $this->hasMany(Location::class);
    }

    public function services() {
        return $this->hasManyThrough(Service::class, Location::class);
    }
}
