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
use App\Service;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use League\Flysystem\Adapter\AbstractAdapter;
use niklasravnsborg\LaravelPdf\Pdf;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Tab;


class KonfiAppQRReport extends AbstractPDFDocumentReport
{

    public $title = 'QR-Codes für Gottesdienste';
    public $group = 'Konfi';
    public $description = 'QR Codes für Gottesdienste, die von den Konfis mit der Konfi-App gescannt werden können.';

    public function setup() {
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first();
        $users = User::all();
        $cities = Auth::user()->writableCities;
        return $this->renderSetupView(compact('maxDate', 'users', 'cities'));
    }

    public function render(Request $request)
    {
        $data = $request->validate([
            'city' => 'required|int|exists:cities,id',
            'start' => 'required|date|date_format:d.m.Y',
            'end' => 'required|date|date_format:d.m.Y',
        ]);

        $allServices = Service::where('city_id', $data['city'])
            ->whereNotNull('konfiapp_event_qr')
            ->whereHas('day', function ($query) use ($data) {
                $query->where('date', '>=', $data['start'])
                    ->where('date', '<=', $data['end']);
            })
            ->get();

        // group by location
        $services = [];
        foreach ($allServices as $service) {
            $services[$service->locationText()][] = $service;
        }
        ksort($services);


        return $this->sendToBrowser(
            date('Ymd') . ' QR-Codes.pdf',
            [
                'start' => $request->get('start'),
                'end' => $request->get('end'),
                'services' => $services,
            ],
            ['format' => 'A4']);

    }

}
