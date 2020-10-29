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
use App\SeatingRow;

class RowBasedSeatingModel extends AbstractSeatingModel
{
    protected $title = 'Reihenbasiert';

    public function rowCapacity(SeatingRow $row) {
        // check if we still have space in this row
        if ($x = count($row->bookings)) {
            // all divided seats booked?
            if ($x >= $row->divides_into) return 0;
            // only one booking, but complete row?
            if ($row->bookings[0]->number > 2) return 0;

            return ($row->divides_into-$x)*2;
        } else {
            return $row->seats;
        }
    }

    public function impact(SeatingRow $row, $number)
    {
        if ($number > $row->seats) return -1;

        // check if we still have space in this row
        if ($x = count($row->bookings)) {
            // partially booked -> no more than two people possible
            if ($number > 2) return -1;
            // all divided seats booked?
            if ($x >= $row->divides_into) return -1;
            // only one booking, but complete row?
            if ($row->bookings[0]->number > 2) return -1;
        }

        if (($row->divides_into > 1) && ($number <=2)) {
            $remaining = ($row->divides_into-1)*2;
            return $row->seats-$remaining;
        } else {
            return $row->seats;
        }
    }

    public function book(SeatingRow $row, Booking $booking)
    {
        $row->bookings[] = $booking;
    }

    public function isAvailable(SeatingRow $row)
    {
        if ($x = count($row->bookings)) {
            if ($x >= $row->divides_into) return false;
            if ($row->bookings[0]->number > 2) return false;
        }
        return true;
    }


}
