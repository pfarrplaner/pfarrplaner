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
use App\Service;
use Illuminate\Support\Collection;

class AbstractSeatFinder
{
    public $viewName = '';

    /** @var Service $service  */
    protected $service = null;
    protected $errors = [];


    /**
     * SeatFinder constructor.
     * @param Service $service
     * @param bool $preloadExistingBookings Optional: Preload existing bookings?
     */
    public function __construct(Service $service, $preloadExistingBookings = true)
    {
        $this->service = $service;
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
    }

    /**
     * Check if seating is still possible after proposed changes to a booking
     * @param Booking $booking
     * @param array $data
     * @return bool True if this booking can be changed
     */
    public function checkIfBookingCanBeChanged(Booking $booking, array $data): bool
    {
    }

    public function finalList()
    {
    }


    /**
     * Get all rows available for seating
     * @return array
     */
    public function getAllRows()
    {
    }

    /**
     * Get the maximum seating capacity of the venue
     * @return int Number of seats
     */
    public function maximumCapacity()
    {
    }

    public function remainingCapacity($bookings = null)
    {
    }

    /**
     * Get the full row record, even for a partial row
     * @param $key
     * @return mixed
     */
    public function getFullRow($key)
    {
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

    public function freeSeatsText() {
        return $this->remainingCapacity().' / '.$this->maximumCapacity();
    }

}
