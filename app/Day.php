<?php

namespace App;

use Carbon\Carbon;
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

    /**
     * @param string $date Date (Y-m-d)
     * @return bool|\App\Day False if not found, Day if found
     */
    public static function existsForDate($date) {
        $day = Day::where('date', $date)->first();
        if (null === $day) return false;
        return $day;
    }

    /**
     * Accept a d.m.Y-formatted string as date attribute
     * @param string $date
     */
    public function setDateAttribute($date) {
        $this->attributes['date'] = Carbon::createFromFormat('d.m.Y', $date);
    }

}
