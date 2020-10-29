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

namespace App\Http\Controllers;


use App\Booking;
use App\Seating\SeatFinder;
use App\Service;
use Illuminate\Http\Request;

class BookingController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except(['findSeat', 'store']);
    }

    /**
     * @param Request $request
     * @param Service $service
     * @param int|null $number
     */
    public function findSeat(Request $request, Service $service, $number = null)
    {
        return view('bookings.seatfinder', compact('service'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);
        $service = Service::findOrFail($data['service_id']);
        $seatFinder = new SeatFinder($service);
        if (!$seatFinder->find($data['number'])) {
            $message = ($data['number'] == 1 ? 'Es konnte kein Sitzplatz in diesem Gottesdienst reserviert werden.' : 'Es konnten keine ' . $data['number'] . ' zusammenhängenden Sitzplätze in diesem Gottesdienst reserviert werden.');
            return redirect()->back()->with('error', $message);
        }

        $data['code'] = Booking::createCode();
        $booking = Booking::create($data);
        $message = ($data['number'] == 1 ? 'Der Sitzplatz wurde reserviert.' : $data['number'] . ' zusammenhängenden Sitzplätze wurden reserviert.');
        return redirect()->back()->with('success', $message.' (Code: '.strtoupper($booking->code).')');
    }

    protected function validateRequest(Request $request)
    {
        $data = $request->validate(
            [
                'service_id' => 'required|int|exists:services,id',
                'code' => 'nullable|string',
                'name' => 'required',
                'first_name' => 'nullable',
                'contact' => 'required|string',
                'number' => 'required|int|min:1',
            ]
        );
        return $data;
    }

}
