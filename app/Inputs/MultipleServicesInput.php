<?php
/*
 * dienstplan
 *
 * Copyright (c) 2019 Christoph Fischer, https://christoph-fischer.org
 * Author: Christoph Fischer, chris@toph.de
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

namespace App\Inputs;

use App\City;
use App\Day;
use App\Location;
use App\Mail\ServiceCreatedMultiple;
use App\Mail\ServiceUpdated;
use App\Service;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MultipleServicesInput extends AbstractInput
{

    public $title = 'Mehrere Gottesdienste';

    public function canEdit(): bool
    {
        return Auth::user()->can('gd-bearbeiten');
    }

    public function setup(Request $request) {
        $cities = Auth::user()->writableCities;
        $locations = Location::whereIn('city_id', $cities->pluck('id'))->get();

        return view($this->getViewName('setup'), [
            'input' => $this,
            'cities' => $cities,
            'locations' => $locations,
        ]);
    }


    public function input(Request $request) {
        $request->validate([
            'includeLocations' => 'required',
            'from' => 'required',
            'to' => 'required',
            'rhythm' => 'required|int',
            'title' => 'nullable|string',
        ]);

        $rhythm = $request->get('rhythm') ?: 1;
        $title = $request->get('title') ?: '';


        $locations = Location::whereIn('id', $request->get('includeLocations') ?: [])->get();

        $from = Carbon::parse(Carbon::createFromFormat('d.m.Y', $request->get('from')));
        while ($from->format('l') != $request->get('weekday')) $from->addDay(1);
        $to = Carbon::createFromFormat('d.m.Y', $request->get('to'));


        $today = $from->copy();
        $ctr = 0;
        while ($today <= $to) {
            foreach ($locations as $location) {
                $services[] = [
                    'index' => $ctr,
                    'date' => $today->copy(),
                    'location' => $location,
                ];
                $ctr++;
            }
            $today->addWeek($rhythm);
        }

        $input = $this;
        return view($this->getInputViewName(), compact('input', 'from', 'to', 'locations', 'services', 'title'));

    }

    public function save(Request $request) {

        $ctrAdded = $ctrExisting = 0;
        $firstDay = null;

        $data = $request->get('service') ?: [];
        foreach ($data as $dayDate => $services) {
            // check if day already exists
            $day = Day::where('date', $dayDate)->first();
            $type = Carbon::createFromFormat('d.m.Y', $dayDate)->dayOfWeek == 0 ? Day::DAY_TYPE_DEFAULT : Day::DAY_TYPE_LIMITED;
            if (null === $day) {
                $day = Day::create(['date' => $dayDate, 'day_type' => $type, 'name' => '', 'description' => '']);
            } else {
                $type = $day->day_type;
            }

            if (null === $firstDay) $firstDay = $day;

            foreach ($services as $service) {
                $location = Location::find($service['location']);
                if ($type == Day::DAY_TYPE_LIMITED) {
                    $day->cities()->attach($location->city);
                }

                $time = $service['time'];
                if (($time == '') || (!preg_match('/^[0-9]{2}:[0-9]{2}$/', $time))) {
                    $time = $location->default_time;
                }

                // check if service already exists
                $service = Service::select('services.*')
                    ->where('location_id', $service['location'])
                    ->where('day_id', $day->id)
                    ->first();
                if (null == $service) {
                    $newService = new Service([
                        'location_id' => $location->id,
                        'city_id' => $location->city_id,
                        'day_id' => $day->id,
                        'time' => $time,
                        'description' => '',
                        'need_predicant' => false,
                        'baptism' => false,
                        'eucharist' => false,
                        'offerings_counter1' => '',
                        'offerings_counter2' => '',
                        'offering_goal' => '',
                        'offering_description' => '',
                        'offering_type' => '',
                        'title' => $request->get('title', ''),
                    ]);
                    $newService->save();
                    $serviceRecords[] = $newService;
                    $ctrAdded++;
                } else {
                    $ctrExisting++;
                    $serviceRecords[] = $service;
                }
            }
        }

        $data = [
            'month' => $firstDay->date->format('m'),
            'year' => $firstDay->date->format('Y'),
        ];

        foreach ($serviceRecords as $key => $record) {
            $serviceRecords[$key] = $record->load(['day', 'location']);
        }

        // use the first service created to create a mass notification
        $service = reset($serviceRecords);
        Subscription::send($service, ServiceCreatedMultiple::class, ['services' => $serviceRecords]);

        if ($ctrExisting) {
            return redirect()->route('calendar', $data)->with('warning', sprintf('%d Gottesdienste wurden hinzugefügt. %d Gottesdienste waren bereits vorhanden.', $ctrAdded, $ctrExisting));
        } else {
            return redirect()->route('calendar', $data)->with('success', sprintf('%d Gottesdienste wurden hinzugefügt.', $ctrAdded));
        }
    }


}
