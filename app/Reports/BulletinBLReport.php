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

namespace App\Reports;

use App\City;
use App\Day;
use App\Liturgy;
use App\Location;
use App\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Mpdf\Config\ConfigVariables;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Tab;


class BulletinBLReport extends AbstractPDFDocumentReport
{

    public $title = 'Gemeindebrief (BL)';
    public $group = 'Listen';
    public $description = 'Gottestabelle für den Gemeindebrief';

    public $formats = ['Balingen'];

    public function setup() {
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first();
        $cities = Auth::user()->cities;
        return $this->renderView('setup', ['maxDate' => $maxDate, 'cities' => $cities, 'formats' => $this->formats]);
    }

    public function configure(Request $request) {
        $request->validate([
            'includeCities' => 'required',
            'start' => 'required|date|date_format:d.m.Y',
            'end' => 'required|date|date_format:d.m.Y',
        ]);

        $includeCities = $request->get('includeCities');
        $start = $request->get('start');
        $end = $request->get('end');

        $locations = Location::with('alternateLocation')->whereIn('city_id', $includeCities)->get();

        // find empty spots:
        $days = Day::where('date', '>=', Carbon::createFromFormat('d.m.Y H:i:s', $request->get('start').' 0:00:00'))
            ->where('date', '<=', Carbon::createFromFormat('d.m.Y H:i:s', $request->get('end').' 23:59:59'))
            ->where('day_type', Day::DAY_TYPE_DEFAULT)
            ->orderBy('date', 'ASC')
            ->get();
        foreach ($days as $day) {
            foreach ($locations as $location) {
                $service = Service::where('location_id', $location->id)
                    ->where('day_id', $day->id)
                    ->get();
                if (!count($service)) {
                    // check if there is a replacement
                    $replacement = '';
                    if (null !== $location->alternateLocation) {
                        $service = Service::where('location_id', $location->alternateLocation->id)
                            ->where('day_id', $day->id)
                            ->get();
                        if (count($service)) {
                            $replacement = 'Gottesdienst '.$service->first()->timeText(true, '.', true)."\r\n".$location->alternateLocation->general_location_name;
                        }
                    }

                    $empty[] = compact('day', 'location', 'replacement');
                }
            }
        }

        return $this->renderView('configure', compact('start', 'end', 'includeCities', 'locations', 'empty'));
    }



    public function render(Request $request)
    {
        $request->validate([
            'includeCities' => 'required',
            'start' => 'required|date|date_format:d.m.Y',
            'end' => 'required|date|date_format:d.m.Y',
        ]);

        $start = $request->get('start');
        $end = $request->get('end');

        $includeCities = $request->get('includeCities');

        $days = Day::where('date', '>=', Carbon::createFromFormat('d.m.Y H:i:s', $request->get('start').' 0:00:00'))
            ->where('date', '<=', Carbon::createFromFormat('d.m.Y H:i:s', $request->get('end').' 23:59:59'))
            ->where('day_type', Day::DAY_TYPE_DEFAULT)
            ->orderBy('date', 'ASC')
            ->get();

        $locationIds = $request->get('locations');
        $locations = Location::whereIn('id', $locationIds)
            ->orderByRaw('FIELD (id, ' . implode(', ', $locationIds) . ') ASC')
            ->get();

        $pageno = $request->get('pageno') ?: 14;


        $serviceList = [];
        foreach ($locations as $location) {
            foreach ($days as $day) {
                $serviceList[$location->id][$day->date->format('Y-m-d')] = Service::with(['location', 'day', 'tags'])
                    ->where('day_id', $day->id)
                    ->whereIn('city_id', $includeCities)
                    ->where('location_id', $location->id)
                    ->orderBy('time', 'ASC')
                    ->get();
            }
        }


        $specialDays = Day::with('cities')
            ->whereHas('cities', function($query) use ($includeCities){
                $query->whereIn('city_id', $includeCities);
            })
            ->where('date', '>=', Carbon::createFromFormat('d.m.Y H:i:s', $request->get('start').' 0:00:00'))
            ->where('date', '<=', Carbon::createFromFormat('d.m.Y H:i:s', $request->get('end').' 23:59:59'))
            ->where('day_type', Day::DAY_TYPE_LIMITED)
            ->orderBy('date', 'ASC')
            ->get();

        $specialServices = [];
        foreach($specialDays as $day) {
            $theseServices = Service::with('location', 'day', 'tags', 'serviceGroups')
                ->whereHas('serviceGroups')
                ->where('day_id', $day->id)
                ->whereIn('city_id', $includeCities)
                ->orderBy('time', 'ASC')
                ->get();
            foreach ($theseServices as $service) {
                foreach ($service->serviceGroups as $group) {
                    $specialServices[$group->name]['services'][] = $service;
                }
            }
        }

        // check for same times as locations
        foreach ($specialServices as $group => $data) {
            $services = $data['services'];
            $firstService = array_first($services);
            $time = $firstService->timeText(true, '.', true);
            $sameTime = true;
            $location = $firstService->locationText();
            $sameLocation = true;

            /** @var Service $service */
            foreach ($services as $service) {
                $sameTime = $sameTime && ($time == $service->timeText(true, '.', true));
                $sameLocation = $sameLocation && ($location == $service->locationText());
            }

            $specialServices[$group]['options'] = compact('sameLocation', 'sameTime', 'location', 'time', 'group');
        }

        $empty = $request->get('empty') ?: [];

        return $this->sendToBrowser(
            date('Ymd') . ' Tabelle fuer Gemeindebrief.pdf',
            compact('start', 'end', 'serviceList', 'days', 'locations', 'specialServices', 'pageno', 'empty'),
            [
                'debug' => true,
                'format' => [176, 249],
                'fontDir' => [
                    resource_path('fonts/'),
                ],
                'fontdata' => [
                        'ptsans' => [
                            'R' => 'PTSans-Regular.ttf',
                            'I' => 'PTSans-Italic.ttf',
                            'B' => 'PTSans-Bold.ttf',
                            'BI' => 'PTSans-BoldItalic.ttf',
                        ]
                    ],
                'default_font' => 'ptsans',
                'mirrorMargins' => 1,
                'list_marker_offset' => '5.5pt',
                'list_symbol_size' => '3.6pt',
            ]
        );

    }


}
