<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['name', 'city_id', 'default_time', 'cc_default_location'];

    public function city() {
        return $this->belongsTo(City::class);
    }

    public function services() {
        return $this->hasMany(Service::class);
    }

}
