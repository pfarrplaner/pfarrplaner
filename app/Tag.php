<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['code', 'name'];

    public function services() {
        return $this->belongsToMany(Service::class)->withTimestamps();
    }
}
