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
use Illuminate\Support\Collection;

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

    protected $errors = [];

    protected $loaded = false;

    /**
     * SeatFinder constructor.
     * @param Service $service
     * @param bool $preloadExistingBookings Optional: Preload existing bookings?
     */
    public function __construct(Service $service, $preloadExistingBookings = true)
    {
        $this->service = $service;
        $this->location = $service->location;
        $this->grid = $this->originalGrid = $this->buildGrid();
        if ($preloadExistingBookings) {
            $this->loadExistingBookings();
            $this->loaded = true;
        }
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
     * @param string $fixedSeat Desired fixed seating position
     * @param string $overrideSeats Override the default maximum number of seats for a fixed position
     * @param string $overrideSplit Override the default splitting configuration for a fixed position
     * @param Collection $bookings Existing bookings used for the check
     * @return bool True if this booking can be added
     */
    public function find($number, $fixedSeat = '', $overrideSeats = '', $overrideSplit = '', $bookings = null)
    {
        if ($number > $this->remainingCapacity()) {
            return false;
        }

        $savedGrid = $this->grid;


        // reset whole Grid
        $this->grid = $this->originalGrid;

        // add a fake booking to the existing bookings and try again
        if (null === $bookings) {
            $bookings = $this->service->bookings;
        }
        $myBooking = new Booking(
            [
                'service_id' => $this->service->id,
                'name' => '___FINDER___',
                'number' => $number,
                'fixed_seat' => $fixedSeat,
                'override_seats' => $overrideSeats,
                'override_split' => $overrideSplit
            ]
        );
        $bookings->push($myBooking);
        $success = $this->loadExistingBookings($bookings);

        // reset grid
        if ($success) {
            $this->grid = $savedGrid;
        }
        return $success;
    }

    /**
     * Check if seating is still possible after proposed changes to a booking
     * @param Booking $booking
     * @param array $data
     * @return bool True if this booking can be changed
     */
    public function checkIfBookingCanBeChanged(Booking $booking, array $data): bool
    {
        // test: remove this booking from the existing list and re-add it with changed data
        $bookings = $this->service->bookings->filter(function ($item) use ($booking){
            return $item->id != $booking->id;
        });
        return $this->find(
            $data['number'],
            $data['fixed_seat'],
            $data['override_seats'],
            $data['override_split'],
            $bookings
        );
    }

    /**
     * Find a place for all existing bookings
     * @param Collection|array $bookings Optional: array or collection of bookings
     * @return bool Success
     */
    protected function loadExistingBookings($bookings = null)
    {
        if (null === $bookings) {
            $bookings = $this->service->bookings->sortByDesc('number');
        }

        // override default grid settings with manually booked bookings
        foreach ($bookings as $booking) {
            if (($booking->override_seats != '') && ($booking->fixed_seat != '')) {
                $rowNumber = str_pad(preg_replace("/[^0-9]/", "", $booking->fixed_seat), 2, 0, STR_PAD_LEFT);
                if (isset($this->grid[$rowNumber])) {
                    $this->grid[$rowNumber]['row']->seats = $booking->override_seats;
                }
            }
        }

        // book manually placed bookings
        $autoBookings = [];
        foreach ($bookings as $booking) {
            if ($booking->fixed_seat == '') {
                $autoBookings[] = $booking;
            } else {
                $success = $this->manualPlacement($booking);
                if (!$success) {
                    return false;
                }
            }
        }

        // book automatically assigned seats
        foreach ($autoBookings as $booking) {
            $success = $this->getSeating($booking);
            if (!$success) {
                $this->setError(
                        'number',
                    $booking->number == 1 ? 'Es konnte kein Sitzplatz in diesem Gottesdienst reserviert werden.' : 'Es konnten keine ' . $booking->number . ' zusammenhängenden Sitzplätze in diesem Gottesdienst reserviert werden.'
                );
                return false;
            }
        }

        return true;
    }

    /**
     * Place a booking manually
     * @param Booking $booking Booking with settings for manual placement
     * @return bool True if placement succeeded
     */
    protected function manualPlacement(Booking $booking)
    {
        if ($booking->fixed_seat == '') {
            return false;
        }
        // whole row?
        if (is_numeric($booking->fixed_seat)) {
            $rowKey = str_pad($booking->fixed_seat, 2, 0, STR_PAD_LEFT);
        } else {
            $rowNumber = str_pad(preg_replace("/[^0-9]/", "", $booking->fixed_seat), 2, 0, STR_PAD_LEFT);
            $placeLetter = preg_replace("/[0-9]/", "", $booking->fixed_seat);
            $rowKey = $rowNumber . $placeLetter;
            // row not yet split?
            if (isset($this->grid[$rowNumber])) {
                // override split?
                if ($booking->override_split != '') {
                    $this->grid[$rowNumber]['row']->split = $booking->override_split;
                }
                $this->splitRow($rowNumber);
            }
        }
        if (isset($this->grid[$rowKey])) {
            if (null == $this->grid[$rowKey]['booking']) {
                $this->grid[$rowKey]['booking'] = $booking;
                return true;
            } else {
                $this->setError(
                    'fixed_seat',
                    'Der gewünschte Platz ' . $rowKey . ' ist bereits für '
                    . $this->grid[$rowKey]['booking']->name
                    . ($this->grid[$rowKey]['booking']->first_name ? ', ' . $this->grid[$rowKey]['booking']->first_name : '')
                    . ' reserviert.'
                );
                return false;
            }
        } else {
            $this->setError('fixed_seat','Der gewünschte Platz ' . $rowKey . ' ist nicht verfügbar.');
            return false;
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
            if (
                (null !== $searchPlace['booking'])
                && ($searchPlace['booking']->fixed_seat == '')
                && ($searchKey > $key)
                && ($searchPlace['booking']->number > $found)
                && ($searchPlace['booking']->number <= $place['row']->seats)
            ) {
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
                    && ($searchPlace['booking']->fixed_seat == '')
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
        $number = 0;
        foreach ($this->grid as $key => $place) {
            if (null !== $place['booking']) {
                $sortKey = $place['booking']->name . ($place['booking']->first_name ? ', ' . $place['booking']->first_name : '');
                $number += $place['booking']->number;
                while (isset($final[$sortKey])) {
                    $sortKey .= '_';
                }
                $final[$sortKey] = $place;
            } else {
                $empty[$place['row']->seatingSection->prioriy . $key] = $place;
            }
        }
        ksort($final);
        ksort($empty);
        return ['list' => $final, 'empty' => $empty, 'number' => $number];
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
        if (count($splits) == 1) {
            return;
        }

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
        if (null === $grid) {
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
        if (trim($this->service->exclude_places) != '') {
            $preReservedPlaces = explode(',', $this->service->exclude_places);
            foreach ($preReservedPlaces as $preReservedPlace) {
                $preReservedPlace = trim($preReservedPlace);
                if (!is_numeric($preReservedPlace)) {
                    $rowNumber = str_pad(preg_replace("/[^0-9]/", "", $preReservedPlace), 2, 0, STR_PAD_LEFT);
                    $placeLetter = preg_replace("/[0-9]/", "", $preReservedPlace);
                    $rows = $this->splitRow($rowNumber, $rows, true);
                    unset($rows[$rowNumber . $placeLetter]);
                } else {
                    $preReservedPlace = str_pad($preReservedPlace, 2, 0, STR_PAD_LEFT);
                    unset ($rows[$preReservedPlace]);
                }
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
    public function getFullRow($key)
    {
        if (is_numeric($key)) {
            return $this->originalGrid[$key]['row'];
        }
        return $this->originalGrid[substr($key, 0, -1)]['row'];
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param string $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * Add an error message
     * @param string $error
     */
    protected function setError(string $key, string $error): void {
        $this->errors[$key] = $error;
    }


}
