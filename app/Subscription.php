<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Subscription extends Model
{
    public const SUBSCRIBE_ALL = 2;
    public const SUBSCRIBE_OWN = 1;
    public const SUBSCRIBE_NONE = 0;

    protected $fillable = [
        'user_id',
        'city_id',
        'subscription_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public static function send(Service $service, $mailClass, $data = [])
    {
        $subscribers = User::subscribedTo($service)->get();
        foreach ($subscribers as $subscriber) {
            if (env('THIS_IS_MY_DEV_HOST')) $subscriber->email = 'chris@toph.de';
            Mail::to($subscriber)->send(new $mailClass($subscriber, $service, $data));
        }
    }
}
