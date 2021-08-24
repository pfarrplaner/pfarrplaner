<?php

namespace App;

use App\Traits\HasAttachedImage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Sermon extends Model
{
    use HasAttachedImage;

    protected $imageField = 'image';

    protected $guarded = [];
    protected $appends = ['fullTitle'];

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get all preachers for this sermon
     * @return array Preachers (by service id)
     */
    public function getPreachersAttribute()
    {
        $preachers = [];
        foreach ($this->services as $service) {
            $preachers[$service->id] = $service->preachers;
        }
        return $preachers;
    }

    /**
     * Get accessible sermons, i.e. those preached in the same city, or by the currently logged-in user
     * @param City $city
     * @return Sermon[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getList(City $city) {
        $sermons = self::all()->filter(function ($value, $key) use ($city) {
            foreach ($value->services as $service) {
                $user = Auth::user();
                if ($service->city->id == $city->id) return true;
                if (collect($service->pastors->pluck('id'))->contains(Auth::user()->id)) return true;
            }
        });
        $list = [];
        foreach ($sermons as $sermon) {
            foreach ($sermon->services as $service) {
                $sermon->name = $service->oneLiner().' "'.$sermon->title
                        .($sermon->subtitle ? ': '.$sermon->subtitle : '')
                    .'" ('.$sermon->reference.')';
                $list[$service->dateTime()->format('YmdHi')] = $sermon;
            }

        }
        ksort($list);
        return $list;
    }

    public function getFullTitleAttribute()
    {
        return $this->title.($this->subtitle ? ': '.$this->subtitle : '');
    }

    public function latestService()
    {
        $dates = $this->services->pluck('dateTime');
        $dates = $dates->reject(function ($item) {
            return (new Carbon($item)) > Carbon::now();
        })->all();
        usort($dates, function($a, $b) {
            return ((new Carbon($a)) < (new Carbon($b))) ? 1 : -1;
        });
        return (string)$dates[0];
    }

    public function setTitleAttribute($title)
    {
        $this->attributes['title'] = $title;

        $title = strtr($title, ['Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss'  ]);

        $slug = $originalSlug = Str::slug($title);
        $index = 1;
        while (null !== Sermon::where('slug', $slug)->where('id', '!=', $this->id)->first()) {
            $slug = $originalSlug.'-'.$index;
            $index++;
        }
        $this->attributes['slug'] = $slug;
    }

}
