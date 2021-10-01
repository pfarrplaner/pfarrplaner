<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'city_id', 'fake_user'];

    public function city() {
        return $this->belongsTo(City::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

}
