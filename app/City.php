<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name'];

    public function locations() {
        return $this->hasMany(Location::class);
    }

    public function services() {
        return $this->hasManyThrough(Service::class, Location::class);
    }
}
