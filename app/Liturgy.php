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

use App\Liturgy\Bible\BibleText;
use App\Liturgy\Bible\ReferenceParser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Storage;

/**
 * Class Liturgy
 * @package App
 */
class Liturgy
{

    /** @var Liturgy|null Instance */
    protected static $instance = null;

    protected static $bibleReferences = [
        'litTextsWeeklyPsalm',
        'litTextsWeeklyQuote',
        'litTextsEntryPsalm',
        'litTextsOldTestament',
        'litTextsEpistel',
        'litTextsEvangelium',
        'litTextsPreacher',
        'litTextsHaleluja',
        'litTextsPerikope1',
        'litTextsPerikope2',
        'litTextsPerikope3',
        'litTextsPerikope4',
        'litTextsPerikope5',
        'litTextsPerikope6',
        'litTextsSpecialty',
        'currentPerikope',
    ];

    /**
     * @return Liturgy|null
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function getCompleteLiturgyInfoArray():array {
        if (!Cache::has('liturgicalDays')) {
            if (!Storage::exists('liturgy.json')) {
                return [];
            }
            $tmpData = json_decode(Storage::get('liturgy.json'), true);
            $overrides = config('liturgy')['overrides'];
            foreach ($tmpData['content']['days'] as $key => $val) {
                $dateComponents = explode('-', $val['dateSql']);
                if (isset($overrides[$dateComponents[1]][$dateComponents[2]])) {
                    $val = array_replace_recursive($val, $overrides[$dateComponents[1]][$dateComponents[2]]);
                }
                if (!isset($data[$val['date']])) {
                    $data[$val['date']] = $val;
                }
            }
            Cache::put('liturgicalDays', $data, 86400);
        } else {
            $data = Cache::get('liturgicalDays');
        }
        return $data ?: [];
    }

    /**
     * Get all the liturgical info for a given day
     * @param string|Carbon $day
     * @param bool $fallback
     * @return array
     */
    public static function getDayInfo($date, $fallback = false): array
    {
        // fallback for obsolete code that still uses Day objects
        if (is_a($date, Day::class)) $date = $date->date;

        if (is_string($date)) $date = Carbon::parse($date);
        $data = self::getCompleteLiturgyInfoArray();

        $result = null;
        if (isset($data[$date->format('d.m.Y')])) {
            $result = $data[$date->format('d.m.Y')];
        } elseif ($fallback) {
            $date = $date;
            while (!isset($data[$date->format('d.m.Y')])) {
                $date = $date->subDays(1);
            }
            $result = isset($data[$date->format('d.m.Y')]) ? $data[$date->format('d.m.Y')] : [];
        }
        if (!is_null($result)) {
            $result['currentPerikope'] = $result['litTextsPerikope' . $result['perikope']];
            $result['currentPerikopeLink'] = $result['litTextsPerikope' . $result['perikope'] . 'Link'];
            return $result;
        }
        return [];
    }


}
