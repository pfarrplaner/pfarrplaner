<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['name', 'city_id', 'default_time', 'cc_default_location', 'alternate_location_id', 'general_location_name', 'at_text'];

    public function city() {
        return $this->belongsTo(City::class);
    }

    public function services() {
        return $this->hasMany(Service::class);
    }

    public function alternateLocation() {
        return $this->belongsTo(Location::class, 'alternate_location_id');
    }


    public function atText() {
        return $this->at_text ?: '('.$this->name.')';
    }
}
