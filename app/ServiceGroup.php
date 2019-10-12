<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceGroup extends Model
{
    protected $fillable = ['name'];

    public function services() {
        return $this->belongsToMany(Service::class);
    }
}
