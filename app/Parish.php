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

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Parish
 * @package App
 */
class Parish extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'code',
        'city_id',
        'address',
        'zip',
        'city',
        'phone',
        'email',
        'congregation_name',
        'congregation_url',
    ];

    /**
     * @return BelongsTo
     */
    public function owningCity()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * @return HasMany
     */
    public function streetRanges()
    {
        return $this->hasMany(StreetRange::class);
    }

    /**
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * @param $csv
     * @return int
     * @throws Exception
     */
    public function importStreetsFromCSV($csv)
    {
        /** @var StreetRange $streetRange */
        foreach ($this->streetRanges as $streetRange) {
            $streetRange->delete();
        }

        $ctr = 0;

        $records = [];
        foreach (explode("\r\n", $csv) as $line) {
            $records[] = str_getcsv($line, ';');
        }
        unset ($records[0]);
        array_walk(
            $records,
            function (&$record) use (&$ctr) {
                if ($record[4] == $this->code) {
                    $record[5] = explode(' bis ', $record[5]);
                    $record[6] = explode(' bis ', $record[6]);
                    $streetRange = new StreetRange(
                        [
                            'parish_id' => $this->id,
                            'name' => $record[0],
                            'odd_start' => $record[5][0],
                            'odd_end' => $record[5][1],
                            'even_start' => $record[6][0],
                            'even_end' => $record[6][1],
                        ]
                    );
                    $streetRange->save();
                    $ctr++;
                }
            }
        );
        return $ctr;
    }

    /**
     * @param Builder $query
     * @param string $street
     * @param int $number
     * @return mixed
     */
    public function scopeByAddress(Builder $query, $street, $number)
    {
        //if (!is_int($city)) $city = $city->id;

        return $this->whereHas(
            'streetRanges',
            function ($query2) use ($street, $number) {
                $query2->where('name', $street);
                if (($number % 2) == 0) {
                    $query2->where('even_start', '<=', $number)
                        ->where('even_end', '>=', $number);
                } else {
                    $query2->where('odd_start', '<=', $number)
                        ->where('odd_end', '>=', $number);
                }
            }
        );
    }
}
