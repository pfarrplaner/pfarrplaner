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

namespace App\Inputs;

use App\Day;
use App\Location;
use App\Mail\ServiceCreatedMultiple;
use App\Service;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Inertia\Inertia;

/**
 * Class MultipleServicesInput
 * @package App\Inputs
 */
class MultipleServicesInput extends AbstractInput
{

    /**
     * @var string
     */
    public $title = 'Mehrere Gottesdienste';
    public $description = 'Eine ganze Reihe von Gottesdiensten auf einmal anlegen';

    public function canEdit(): bool
    {
        return Auth::user()->can('gd-bearbeiten');
    }

    /**
     * @param Request $request
     * @return \Inertia\Response
     */
    public function setup(Request $request)
    {
        $locations = Location::whereIn('city_id', Auth::user()->writableCities->pluck('id'))->get();
        return Inertia::render('Inputs/MultipleServices/Setup', compact('locations'));
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function input(Request $request)
    {
        return redirect()->route('inputs.setup', 'multipleServices');
    }

    /**
     * @param Request $request
     * @return RedirectResponse|void
     */
    public function save(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string',
            'services.*.date' => 'date',
            'services.*.time' => 'date_format:H:i',
            'services.*.location' => 'int|exists:locations,id',
                                   ]);
        $serviceRecords = [];
        $ctrExisting = $ctrAdded = 0;

        foreach ($data['services'] as $serviceData) {
            $location = Location::find($serviceData['location']);
            $date = Carbon::parse($serviceData['date'], 'UTC')
                ->setTimezone('Europe/Berlin')
                ->setTimeFromTimeString($serviceData['time'])
                ->setTimezone('UTC');

            // check if service already exists
            $existing = Service::select('services.*')
                ->where('location_id', $serviceData['location'])
                ->where('date', $date)
                ->first();

            $service = null;
            if (!$existing) {
                $service = [
                    'date' =>  $date,
                    'location_id' => $serviceData['location'],
                    'city_id' => $location->city_id,
                    'description' => '',
                    'need_predicant' => false,
                    'baptism' => false,
                    'eucharist' => false,
                    'offerings_counter1' => '',
                    'offerings_counter2' => '',
                    'offering_goal' => '',
                    'offering_description' => '',
                    'offering_type' => '',
                ];
                $serviceRecords[] = Service::create($service);
                $ctrAdded++;
            } else {
                $ctrExisting++;
                $serviceRecords[] = $existing;
            }
        }

        foreach ($serviceRecords as $key => $record) {
            $serviceRecords[$key] = $record->load(['location']);
        }

        // use the first service created to create a mass notification
        $service = reset($serviceRecords);
        Subscription::send($service, ServiceCreatedMultiple::class, ['services' => $serviceRecords]);


        if ($ctrExisting) {
            return redirect()->route('calendar')->with(
                'warning',
                sprintf(
                    '%d Gottesdienste wurden hinzugefügt. %d Gottesdienste waren bereits vorhanden.',
                    $ctrAdded,
                    $ctrExisting
                )
            );
        } else {
            return redirect()->route('calendar')->with(
                'success',
                sprintf('%d Gottesdienste wurden hinzugefügt.', $ctrAdded)
            );
        }
    }


}
