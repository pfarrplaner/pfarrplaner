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

namespace App\Reports;

use App\City;
use App\Day;
use App\Integrations\KonfiApp\KonfiAppIntegration;
use App\Service;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


/**
 * Class KonfiAppQRReport
 * @package App\Reports
 */
class KonfiAppQRReport extends AbstractPDFDocumentReport
{

    /**
     * @var string
     */
    public $title = 'QR-Codes für Gottesdienste';
    /**
     * @var string
     */
    public $group = 'Konfi';
    /**
     * @var string
     */
    public $description = 'QR Codes für Gottesdienste, die von den Konfis mit der KonfiApp gescannt werden können.';

    /**
     * Only active if at least one of the user's cities has KonfiApp integration
     * @return bool
     */
    public function isActive(): bool
    {
        $isActive = false;
        foreach (Auth::user()->cities as $city) {
            $isActive = $isActive || ($city->konfiapp_apikey != '');
        }
        return $isActive;
    }


    /**
     * @return Application|Factory|View
     */
    public function setup()
    {
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first();
        $users = User::all();
        $cities = Auth::user()->writableCities->reject(function ($item) {
            return $item->konfiapp_apikey == '';
        });
        return $this->renderSetupView(compact('maxDate', 'users', 'cities'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse|mixed|string
     * @throws Exception
     */
    public function render(Request $request)
    {
        $data = $request->validate(
            [
                'city' => 'required|int|exists:cities,id',
                'start' => 'required|date|date_format:d.m.Y',
                'end' => 'required|date|date_format:d.m.Y',
                'copies' => 'required|int',
            ]
        );

        $allServices = Service::where('city_id', $data['city'])
            ->where('konfiapp_event_qr', '!=', '')
            ->whereHas(
                'day',
                function ($query) use ($data) {
                    $query->where('date', '>=', Carbon::createFromFormat('d.m.Y', $data['start'])->format('Y-m-d'))
                        ->where('date', '<=', Carbon::createFromFormat('d.m.Y', $data['end'])->format('Y-m-d'));
                }
            )
            ->get();


        // group by location
        $services = [];
        foreach ($allServices as $service) {
            $services[$service->locationText()][] = $service;
        }
        ksort($services);


        if (count($services) == '0') {
            return redirect()->route('reports.setup', 'konfiAppQR');
        }

        $types = KonfiAppIntegration::get(City::find($data['city']))->listEventTypes();


        return $this->sendToFile(
            date('Ymd') . ' QR-Codes.pdf',
            [
                'services' => $services,
                'types' => $types,
                'copies' => $data['copies'],
            ],
            ['format' => 'A4-L']
        );
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function single(Request $request)
    {
        $service = Service::findOrFail($request->get('service'));
        $services[$service->locationText()][] = $service;
        $types = KonfiAppIntegration::get($service->city)->listEventTypes();
        $copies = $request->get('copies', 1);
        return $this->sendToFile(
            $service->day->date->format('Ymd') . ' QR-Code.pdf',
            [
                'services' => $services,
                'types' => $types,
                'copies' => $copies,
            ],
            ['format' => 'A4-L']
        );
    }

}
