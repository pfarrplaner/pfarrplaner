<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Day extends Model
{

    public const DAY_TYPE_DEFAULT = 0;
    public const DAY_TYPE_LIMITED = 1;

    protected $fillable = ['date', 'name', 'description', 'day_type'];
    protected $dates = ['date'];

    public function services() {
        return $this->hasMany(Service::class);
    }

    public function cities() {
        return $this->belongsToMany(City::class);
    }

}
