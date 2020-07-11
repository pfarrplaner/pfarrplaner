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

namespace App\Traits;


use App\Service;
use Illuminate\Support\Collection;

/**
 * Trait TracksChangesTrait
 * @package App\Traits
 */
trait TracksChangesTrait
{

    /** @var array $dataBeforeChanges Snapshot with original data */
    public $dataBeforeChanges = [];
    /**
     * @var array
     */
    public $changed = [];

    /**
     * @return mixed
     */
    public function createSnapshot()
    {
        $snapshot = $this->removeTimestamps(json_decode(json_encode($this), true));
        if (property_exists($this, 'appendsToTracking') && is_array($this->appendsToTracking)) {
            foreach ($this->appendsToTracking as $attribute) {
                if (!isset($snapshot[$attribute])) $snapshot[$attribute] = $this->$attribute;
            }
        }
        return $snapshot;
    }

    /**
     * Start tracking changed
     * @return void
     */
    public function trackChanges()
    {
        if (isset($this->forceTracking)) {
            $this->load($this->forceTracking);
        }
        $this->dataBeforeChanges = $this->createSnapshot();
    }

    /**
     * @param $array
     * @return mixed
     */
    protected function removeTimestamps($array) {
        foreach ($array as $key => $value) {
            if (is_array($value)) $array[$key] = $this->removeTimestamps($value);
            elseif ($key == 'updated_at') unset($array[$key]);
            elseif ($key == 'created_at') unset($array[$key]);
        }
        return $array;
    }


    /**
     * Return all changed attributes and relations
     * @return array
     */
    public function diff()
    {
        $this->refresh();

        $diff = [];
        $snapshot = $this->createSnapshot();
        $original = $this->dataBeforeChanges;

        // set previously unset keys
        foreach ($snapshot as $key => $value) {
            if (!isset($original[$key])) $original[$key] = '';
        }

        foreach ($original as $key => $value) {
            // reset unset keys
            if (!isset($snapshot[$key])) $snapshot[$key] = '';
            if (print_r($value, 1) != print_r($snapshot[$key], 1)) {
                $diff[$key] = [
                    'original' => $original[$key],
                    'changed' => $snapshot[$key],
                ];
            }
        }

        return $diff;
    }

    /**
     * @return bool
     */
    public function isChanged()
    {
        $snapshot = $this->createSnapshot();
        $original = $this->dataBeforeChanges;
        unset($original['updated_at']);
        unset($snapshot['updated_at']);
        return (json_encode($snapshot) != json_encode($original));
    }

    public function storeDiff() {
        $this->changed = $this->diff();
    }

}
