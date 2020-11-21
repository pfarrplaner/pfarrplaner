<?php
/*
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

namespace App\Seating;


use App\Booking;
use App\Location;
use App\SeatingSection;
use App\Service;
use Illuminate\Database\Eloquent\Collection;

class SeatFinder
{

    protected const ROW_TOO_SMALL = -1;
    protected const ROW_FULL = -2;

    /** @var Location */
    protected $location;

    /** @var $service */
    protected $service;

    protected $capacity = 0;

    protected $grid = [];
    protected $originalGrid = [];

    /**
     * SeatFinder constructor.
     * @param Service $service
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
        $this->location = $service->location;
        $this->grid = $this->originalGrid = $this->buildGrid();
        $this->loadExistingBookings();
    }

    /**
     * Initialize a grid array with all rows
     * The row title is used as the array key.
     * The array is sorted by row titles
     * @return array
     */
    protected function buildGrid()
    {
        $grid = [];
        foreach ($this->getAllRows() as $row) {
            $grid[$row->title] = ['row' => $row, 'booking' => null];
        }
        ksort($grid);
        return $grid;
    }

    protected function dumpGrid()
    {
        ksort($this->grid);
        echo '<table>';
        foreach ($this->grid as $key => $place) {
            echo "<tr><td>" . $key . "</td><td>[" . $place['row']->seats . "]</td><td>" .
                ((null !== $place['booking']) ? $place['booking']['name'] : '')
                . " ("
                . ((null !== $place['booking']) ? $place['booking']['number'] : '')
                . ")</td></tr>";
        }
        echo '</table><hr />';
    }

    /**
     * Check if a specified number of people can still be seated in the location
     * @param $number Number of people
     * @return bool
     */
    public function find($number)
    {
        if ($number > $this->remainingCapacity()) return false;

        $savedGrid = $this->grid;

        // insert new fake booking
        $myBooking = new Booking(['service_id' => $this->service->id, 'name' => '___FINDER___', 'number' => $number]);
        $success = $this->getSeating($myBooking) !== false;

        // unset fake booking
        if ($success) {
            $this->grid = $savedGrid;
        }
        return $success;
    }

    /**
     * Find a place for all existing bookings
     */
    protected function loadExistingBookings()
    {
        $bookings = $this->service->bookings->sortByDesc('number');
        foreach ($bookings as $booking) {
            $this->getSeating($booking);
        }
    }

    /**
     * Optimize seating
     *
     * This will move people as far to the front as possible
     *
     */
    protected function optimize()
    {
        do {
            $result = $this->optimizeOne();
        } while ($result);
    }

    /**
     * Do one step of optimization
     * @return bool True, if there was still something to be optimized
     */
    protected function optimizeOne()
    {
        ksort($this->grid);
        foreach ($this->grid as $key => $place) {
            if (null === $place['booking']) {
                // try what we gain by optimizing whole row
                $ptsWholeRow = $this->optimizeWholeRow($key, $place, false);
                $ptsSplitRow = $this->optimizeSplitRow($key, $place, false);

                if (max($ptsSplitRow, $ptsWholeRow) == 0) {
                    return false;
                }

                if (!(($ptsSplitRow == $ptsWholeRow) && ($ptsWholeRow == -1))) {
                    if ($ptsWholeRow > $ptsSplitRow) {
                        $this->optimizeWholeRow($key, $place);
                    } else {
                        $this->optimizeSplitRow($key, $place);
                    }
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Optimize a row using the whole row as one
     * @param $key Grid key
     * @param $place Grid element
     * @param bool $do Actually do this (false will only simulate optimization)
     * @return int Point gained by this move
     */
    protected function optimizeWholeRow($key, $place, $do = true)
    {
        $found = 0;
        $foundKey = null;

        foreach ($this->grid as $searchKey => $searchPlace) {
            if ((null !== $searchPlace['booking']) && ($searchKey > $key) && ($searchPlace['booking']->number > $found) && ($searchPlace['booking']->number <= $place['row']->seats)) {
                $found = $searchPlace['booking']->number;
                $foundKey = $searchKey;
            }
        }
        if (null !== $foundKey) {
            if ($do) {
                $this->grid[$key]['booking'] = $this->grid[$foundKey]['booking'];
                $this->grid[$foundKey]['booking'] = null;
            }
            return $found;
        }
        return -1;
    }

    /**
     * Optimize a row by splitting it into several places
     * @param $key Grid key
     * @param $place Grid element
     * @param bool $do Actually do this (false will only simulate optimization)
     * @return int Point gained by this move, -1 if not possible
     */
    protected function optimizeSplitRow($key, $place, $do = true)
    {
        $pts = 0;
        $moved = [];
        $splits = explode(',', $this->grid[$key]['row']->split ?: $this->grid[$key]['row']->seats);
        if (count($splits) == 1) {
            return -1;
        }

        foreach ($splits as $splitKey => $split) {
            $found = 0;
            $foundKey = null;
            foreach ($this->grid as $searchKey => $searchPlace) {
                if ((null !== $searchPlace['booking'])
                    && ($searchKey > $key)
                    && ($searchPlace['booking']->number > $found)
                    && ($searchPlace['booking']->number <= $split)
                    && (!in_array($searchKey, $moved))
                ) {
                    $found = $searchPlace['booking']->number;
                    $foundKey = $searchKey;
                }
            }
            if (null !== $foundKey) {
                $moved[$splitKey] = $foundKey;
                $pts += $this->grid[$foundKey]['booking']->number;
            }
        }

        if ($do && count($moved)) {
            $this->splitRow($key);
            foreach ($moved as $target => $src) {
                $this->grid[$key . chr(65 + $target)]['booking'] = $this->grid[$src]['booking'];
                $this->grid[$src]['booking'] = null;
            }
        }

        return $pts;
    }

    public function finalList()
    {
        $this->optimize();

        // split all empty rows:
        foreach ($this->grid as $key => $place) {
            if (null === $place['booking']) {
                $this->splitRow($key);
            }
        }

        // compile lists
        $final = [];
        $empty = [];
        foreach ($this->grid as $key => $place) {
            if (null !== $place['booking']) {
                $sortKey = $place['booking']->name . ($place['booking']->first_name ? ', ' . $place['booking']->first_name : '');
                while(isset($final[$sortKey])) $sortKey.='_';
                $final[$sortKey] = $place;
            } else {
                $empty[$place['row']->seatingSection->prioriy.$key] = $place;
            }
        }
        ksort($final);
        ksort($empty);
        return ['list' => $final, 'empty' => $empty];
    }

    /**
     * Try to place a booking on the grid with minimum loss of seats
     * @param Booking $booking Booking to place
     * @return false|mixed Place key, false if not placeable
     */
    protected function getSeating(Booking $booking)
    {
        $candidates = [];
        foreach ($this->grid as $key => $place) {
            $impact = $this->getImpact($key, $booking->number);
            if ($impact > 0) {
                $candidates[$impact][] = $key;
            }
        }
        if (!count($candidates)) {
            return false;
        }
        ksort($candidates);
        $targetImpact = array_key_first($candidates);
        $candidates = $candidates[$targetImpact];
        if (count($candidates)) {
            $target = $candidates[0];
            $this->capacity -= $targetImpact;
            $this->seat($target, $booking);
            return $target;
        }
        return false;
    }

    /**
     * Place a booking in a specific spot
     *
     * @param string $key Grid key
     * @param Booking $booking Booking
     */
    protected function seat($key, Booking $booking)
    {
        // first, test if we can split the row:
        $splits = explode(',', $this->grid[$key]['row']->split ?: $this->grid[$key]['row']->seats);

        // splitting doesn't work if there are no splits
        if (count($splits) == 1) {
            $this->grid[$key]['booking'] = $booking;
            return;
        }

        // splitting doesn't work if the number is too big
        if ($booking->number > max($splits)) {
            $this->grid[$key]['booking'] = $booking;
            return;
        }

        // seem's like splitting works:
        // 1. find the proper split
        $targetSplitSize = $this->grid[$key]['row']->seats;
        $targetSplit = null;
        foreach ($splits as $splitKey => $split) {
            if (($split >= $booking->number) && ($split <= $targetSplitSize)) {
                $targetSplitSize = $split;
                $targetSplit = $splitKey;
            }
        }

        // 2. split the row
        $this->splitRow($key);

        // 3. place booking
        $this->grid[$key . chr(65 + $targetSplit)]['booking'] = $booking;
    }

    /**
     * Split a row into its parts
     * @param string $key Grid key
     * @param array $grid Optional: use alternative grid array instead of $this->grid
     * @param bool $isRowGrid Optional: Grid only contains rows, no bookings
     * @return array Grid with split row
     */
    protected function splitRow($key, $grid = null, $isRowGrid = false)
    {
        // do not split an already split row (letter in $key)
        if (!is_numeric($key)) {
            return;
        }

        $tmpGrid = $grid ?? $this->grid;

        $targetRow = $isRowGrid ? $tmpGrid[$key] : $tmpGrid[$key]['row'];
        $splits = explode(',', $targetRow->split ?: $targetRow->seats);

        // only split splittable rows
        if (count($splits) == 1) return;

        // insert the new split rows
        foreach ($splits as $splitKey => $split) {
            $rowKey = $key . chr(65 + $splitKey);
            $row = clone($targetRow);
            $row->title = $rowKey;
            $row->seats = $split;
            $row->split = '';
            $tmpGrid[$rowKey] = $isRowGrid ? $row : ['row' => $row, 'booking' => null];
        }

        // delete old full row
        unset ($tmpGrid[$key]);

        // restore target grid from $tmpGrid
        if (null===$grid) {
            $this->grid = $tmpGrid;
        }

        return $tmpGrid;
    }

    /**
     * Calculate the impact a placement would have
     *
     * Impact is defined as the number of free seats lost when a certain number of people
     * is placed in a specific row
     *
     * @param string $key Grid key to place the people
     * @param int $number Number of people to place
     * @return int Impact (negative, if placement is not possible)
     */
    protected function getImpact($key, $number)
    {
        // check if the place is already booked
        if (null !== $this->grid[$key]['booking']) {
            return self::ROW_FULL;
        }

        $row = $this->grid[$key]['row'];

        // check if the place is big enough
        if ($number > $row->seats) {
            return self::ROW_TOO_SMALL;
        }

        // try splitting the row
        $splits = explode(',', ($row->split ?: $row->seats));

        // if the row cannot be split, we need the whole row
        if (count($splits) == 1) {
            return $row->seats;
        }

        // if $number is greater than the maximum split number, we need the whole row
        if ($number > max($splits)) {
            return $row->seats;
        }

        // seems like a part of the row will be enough:
        // in this case, the impact is the row maximum minus the sum of the
        // other split seats
        $possible = $row->seats;
        $impact = 0;
        foreach ($splits as $split) {
            if (($split >= $number) && ($split < $possible)) {
                $possible = $split;
            } else {
                $impact += $split;
            }
        }
        return $row->seats - $impact;
    }

    /**
     * Get the SeatingSections available for seating
     * @return mixed
     */
    protected function getAvailableSections()
    {
        // TODO: Take settings into account (sections may be closed)
        $sections = $this->location->seatingSections->sortBy('priority');
        if (trim($this->service->exclude_sections) != '') {
            $excludedSections = [];
            foreach (explode(',', $this->service->exclude_sections) as $e) {
                $excludedSections[] = strtolower(trim($e));
            };
            $forgetSections = [];
            /** @var SeatingSection $section */
            foreach ($sections as $key => $section) {
                if (in_array(strtolower($section->title), $excludedSections)) {
                    $forgetSections[] = $key;
                }
            }
            foreach ($forgetSections as $section) {
                $sections->forget($section);
            }
        }
        return $sections;
    }

    /**
     * Get all rows available for seating
     * @return array
     */
    public function getAllRows()
    {
        $rows = [];
        foreach ($this->getAvailableSections() as $section) {
            foreach ($section->seatingRows as $row) {
                $rows[$row->title] = $row;
            }
        }

        // unset pre-reserved rows:
        $preReservedPlaces = explode(',', $this->service->exclude_places);
        foreach ($preReservedPlaces as $preReservedPlace) {
            $preReservedPlace = trim($preReservedPlace);
            if (!is_numeric($preReservedPlace)) {
                $rowNumber = str_pad(preg_replace("/[^0-9]/","",$preReservedPlace), 2, 0, STR_PAD_LEFT);
                $placeLetter = preg_replace("/[0-9]/","",$preReservedPlace);
                $rows = $this->splitRow($rowNumber, $rows, true);
                unset($rows[$rowNumber.$placeLetter]);
            } else {
                $preReservedPlace = str_pad($preReservedPlace, 2, 0, STR_PAD_LEFT);
                unset ($rows[$preReservedPlace]);
            }
        }
        return $rows;
    }

    /**
     * Get the maximum seating capacity of the venue
     * @return int Number of seats
     */
    public function maximumCapacity()
    {
        $capacity = 0;
        foreach ($this->getAllRows() as $seatingRow) {
            $capacity += $seatingRow->seats;
        }
        return $capacity;
    }

    public function remainingCapacity()
    {
        $saveGrid = $this->grid;
        $this->optimize();
        $capacity = 0;
        foreach ($this->grid as $key => $place) {
            if (null === $place['booking']) {
                $capacity += $place['row']->seats;
            }
        }
        $this->grid = $saveGrid;
        return $capacity;
    }

    /**
     * Get the full row record, even for a partial row
     * @param $key
     * @return mixed
     */
    public function getFullRow($key) {
        if (is_numeric($key)) return $this->originalGrid[$key]['row'];
        return $this->originalGrid[substr($key, 0, -1)]['row'];
    }
}
