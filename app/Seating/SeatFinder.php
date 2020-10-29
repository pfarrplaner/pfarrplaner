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
use App\SeatingRow;
use App\Service;
use Illuminate\Database\Eloquent\Collection;

class SeatFinder
{

    /** @var Location */
    protected $location;

    /** @var $service */
    protected $service;

    /** @var Collection */
    protected $rows;

    protected $capacity = 0;

    /**
     * SeatFinder constructor.
     * @param Service $service
     */
    public function __construct(Service $service)
    {
        $this->service = $service;
        $this->location = $service->location;
        $this->rows = $this->getAllRows();
        $this->capacity = $this->maximumCapacity();
    }

    public function find($number)
    {
        $bookings = clone $this->service->bookings;

        // insert new fake booking
        $myBooking = new Booking(['service_id' => $this->service->id, 'name' => '___FINDER___', 'number' => $number]);
        $bookings->push($myBooking);

        // sort bookings by number of people
        $bookings = $bookings->sortByDesc('number');

        // try to seat all booked people
        $success = true;
        foreach ($bookings as $booking) {
            $success = $success && $this->placeBooking($booking);
            if (!$success) continue;
        }
        return $success;
    }

    protected function placeBooking(Booking $booking) {
        $candidates = [];
        foreach ($this->rows as $row) {
            $impact = $row->seatingSection->seating_model->impact($row, $booking->number);
            if ($impact > 0) $candidates[$impact][] = $row;
        }
        if (!count($candidates)) return false;
        ksort($candidates);
        $targetImpact = array_key_first($candidates);
        $candidates = $candidates[$targetImpact];
        if (count($candidates)) {
            /** @var SeatingRow $target */
            $target = $candidates[0];
            $seatingModel = $target->seatingSection->seating_model;
            $seatingModel->book($target, $booking);
            $remainAvailable = $seatingModel->isAvailable($target);
            if (!$remainAvailable) {
                unset($this->rows[$target->title]);
            }
            $this->capacity -= $targetImpact;
            return true;
        }
        return false;
    }

    protected function getAvailableSections()
    {
        // TODO: Take settings into account (sections may be closed)
        return $this->location->seatingSections;
    }

    protected function getAllRows()
    {
        $rows = [];
        foreach ($this->getAvailableSections() as $section) {
            foreach ($section->seatingRows as $row) {
                $rows[$row->title] = $row;
            }
        }
        // TODO: Take bookings into account
        return $rows;
    }

    public function getAvailableRows() {
        return $this->rows;
    }


    public function maximumCapacity()
    {
        $capacity = 0;
        foreach ($this->getAllRows() as $seatingRow) {
            $capacity += $seatingRow->seats;
        }
        return $capacity;
    }

    public function remainingCapacity() {
        foreach ($this->service->bookings as $booking) {
            $this->placeBooking($booking);
        }
        $capacity = 0;
        foreach ($this->rows as $row) {
            $capacity += $row->seatingSection->seating_model->rowCapacity($row);
        }
        return $capacity;
    }
}
