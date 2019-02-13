<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    protected $fillable = ['date', 'name', 'description'];
    protected $dates = ['date'];

    public function services() {
        return $this->hasMany(Service::class);
    }


}
