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
use Illuminate\Support\Facades\Validator;

class SeatingValidators
{
    public static function register() {
        Validator::extend('seatable', function($attribute, $value, $parameters, $validator){
            return SeatingValidators::seatable($attribute, $value, $parameters, $validator);
        });
        Validator::extend('seatable_fixed', function($attribute, $value, $parameters, $validator){
            return SeatingValidators::seatable($attribute, $value, $parameters, $validator);
        });
    }

    public static function seatable($attribute, $value, $parameters, $validator) {
        /** @var \Illuminate\Validation\Validator $data */
        $data = $validator->getData();
        if ($attribute == 'number') {
            $data['fixed_seat'] = '';
        } else {
            $data['number'] = 1;
        }

        /** @var Service $service */
        $service = Service::findOrFail($data['service_id']);
        /** @var SeatFinder $seatFinder */
        $seatFinder = $service->getSeatFinder();

        if (isset($parameters[0]) && isset($data[$parameters[0]])) {
            // existing booking
            $booking = Booking::find($data[$parameters[0]]);
            $success = $seatFinder->checkIfBookingCanBeChanged($booking, $data);
        } else {
            // new booking
            $success = $seatFinder->find(
                $data['number'],
                $data['fixed_seat'],
                $data['override_seats'],
                $data['override_split']
            );
        }
        return $success;
    }
}
