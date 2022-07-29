<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DiaryEntry extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'service_id', 'date', 'title', 'category'];
    protected $dates = ['date'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return mixed|null
     */
    public function getDateAttribute()
    {
        if ($this->attributes['date']) {
            return $this->attributes['date'];
        }
        if ($this->service_id) {
            return $this->service->date;
        }
        return null;
    }

    /**
     * Create a new DiaryEntry from an existing service
     *
     * @param string $category Target category
     * @param Service $service Service record
     * @return DiaryEntry New DiaryEntry record
     */
    public static function createFromService(Service $service,  $category)
    {
        return self::create([
                                'date' => $service->date->copy()->setTimeZone('Europe/Berlin'),
                                'title' => $service->titleText(false),
                                'user_id' => Auth::user()->id,
                                'service_id' => $service->id,
                                'category' => $category,
                            ]);
    }

}
