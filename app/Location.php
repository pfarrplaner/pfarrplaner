<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['name', 'city_id', 'default_time'];

    public function city() {
        return $this->belongsTo(City::class);
    }

    public function services() {
        return $this->hasMany(Service::class);
    }

}
