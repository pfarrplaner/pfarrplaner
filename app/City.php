<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/pfarrplaner/pfarrplaner
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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Class City
 * @package App
 */
class City extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'public_events_calendar_url',
        'default_offering_goal',
        'default_offering_description',
        'default_funeral_offering_goal',
        'default_funeral_offering_description',
        'default_wedding_offering_goal',
        'default_wedding_offering_description',
        'op_domain',
        'op_customer_key',
        'op_customer_token',
        'podcast_title',
        'podcast_logo',
        'sermon_default_image',
        'homepage',
        'podcast_owner_name',
        'podcast_owner_email',
        'google_auth_code',
        'google_access_token',
        'google_refresh_token',
        'youtube_channel_url',
        'konfiapp_apikey',
        'youtube_active_stream_id',
        'youtube_passive_stream_id',
        'youtube_auto_startstop',
        'youtube_cutoff_days',
        'default_offering_url',
        'youtube_self_declared_for_children',
        'communiapp_url',
        'communiapp_token',
        'communiapp_default_group_id',
        'communiapp_use_outlook',
        'communiapp_use_op',
    ];

    /**
     * @var string
     */
    protected $orderBy = 'name';
    /**
     * @var string
     */
    protected $orderDirection = 'ASC';

    /**
     * @return HasMany
     */
    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    /**
     * @return HasManyThrough
     */
    public function services()
    {
        return $this->hasManyThrough(Service::class, Location::class);
    }

    /**
     * Check if this city is administered by a particular use
     * @param User $user User
     * @return bool True if user has admin rights here
     */
    public function administeredBy(User $user)
    {
        if ($user->hasRole('Super-Administrator*in')) {
            return true;
        }
        $city = $user->cities->where('id', $this->id)->first();
        if ($city && ($city->pivot->permission == 'a')) {
            return true;
        }
        return false;
    }

    /**
     * Check if this city has any locations where seating is defined
     * @return bool True if such locations exist
     */
    public function hasRegistrableLocations() {
        return Location::where('city_id', $this->id)->whereHas('seatingSections')->count() > 0;
    }
}
