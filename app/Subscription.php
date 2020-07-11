<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
 * @version git: $Id$
 *
 * Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
 *
 * Pfarrplaner is based on the Laravel framework (https://laravel.com).
 * This file may contain code created by Laravel's scaffolding functions.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
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

    public static function send(Service $service, $mailClass, $data = [], $additionalSubscribers = null)
    {
        $subscribers = User::subscribedTo($service)->get();

        // add additional subscribers
        if (!is_null($additionalSubscribers)) {
            foreach ($additionalSubscribers as $subscriber) {
                if (!$subscribers->contains($subscriber)) $subscribers->push($subscriber);
            }
        }

        // queue messages
        foreach ($subscribers as $subscriber) {
            if (!env('THIS_IS_MY_DEV_HOST')) {
                if ($subscriber->email && filter_var($subscriber->email, FILTER_VALIDATE_EMAIL)) {
                    Log::debug('Sending ServiceUpdated to '.$subscriber->email);
                    Mail::to($subscriber)->queue(new $mailClass($subscriber, $service, $data));
                } else {
                    Log::debug('User '.$subscriber->name.' has no valid email to send ServiceUpdated.');
                }
            } else {
                Log::debug('Sending ServiceUpdated to dev@toph.de instead of '.$subscriber->email);
                $subscriber->email = 'dev@toph.de';
                Mail::to($subscriber)->queue(new $mailClass($subscriber, $service, $data));
            }
        }
    }
}
