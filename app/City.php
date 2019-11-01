<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name', 'public_events_calendar_url'];

    protected $orderBy = 'name';
    protected $orderDirection = 'ASC';

    public function locations() {
        return $this->hasMany(Location::class);
    }

    public function services() {
        return $this->hasManyThrough(Service::class, Location::class);
    }
}
