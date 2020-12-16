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

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Day
 * @package App
 */
class Day extends Model
{

    /**
     *
     */
    public const DAY_TYPE_DEFAULT = 0;
    /**
     *
     */
    public const DAY_TYPE_LIMITED = 1;

    /**
     * @var string[]
     */
    protected $fillable = ['date', 'name', 'description', 'day_type'];
    /**
     * @var string[]
     */
    protected $dates = ['date'];
    /**
     * @var string[]
     */
    protected $appends = ['liturgy'];

    /**
     * @param string $date Date (Y-m-d)
     * @return bool|Day False if not found, Day if found
     */
    public static function existsForDate($date)
    {
        $day = Day::where('date', $date)->first();
        if (null === $day) {
            return false;
        }
        return $day;
    }

    /**
     * @return HasMany
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * @return BelongsToMany
     */
    public function cities()
    {
        return $this->belongsToMany(City::class);
    }

    /**
     * Accept a d.m.Y-formatted string as date attribute
     * @param string $date
     */
    public function setDateAttribute($date)
    {
        if (is_a($date, Carbon::class)) {
            $this->attributes['date'] = $date;
        } else {
            $this->attributes['date'] = Carbon::createFromFormat('d.m.Y', $date);
        }
    }

    /**
     * Add liturgy attribute
     * @return array
     */
    public function getLiturgyAttribute(): array {
        return Liturgy::getDayInfo($this);
    }

    public function scopeInMonth($query, Carbon $date) {
        $date->setTime(0,0,0)->setDay(1);
        return $query->where('date', '>=', $date)
            ->where('date', '<=', $date->copy()->addMonth(1)->subSecond(1));
    }

}
