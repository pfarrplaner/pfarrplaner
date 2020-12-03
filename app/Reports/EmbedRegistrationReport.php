<?php
/**
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

/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 02.11.2019
 * Time: 12:30
 */

namespace App\Reports;


use App\Booking;
use App\Http\CORS;
use App\Service;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;

/**
 * Class EmbedContactLookupReport
 * @package App\Reports
 */
class EmbedRegistrationReport extends AbstractEmbedReport
{

    /**
     * @var string
     */
    public $title = 'Anmeldung zu Gottesdiensten';
    /**
     * @var string
     */
    public $group = 'Website (Gemeindebaukasten)';
    /**
     * @var string
     */
    public $description = 'Erzeugt HTML-Code für die Einbindung einer Anmeldebox für Gottesdienste';
    /**
     * @var string
     */
    public $icon = 'fa fa-file-code';

    /**
     * @return Application|Factory|View
     */
    public function setup()
    {
        $cities = Auth::user()->cities;
        return $this->renderSetupView(compact('cities'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|string
     */
    public function render(Request $request)
    {
        $request->validate(
            [
                'cors-origin' => 'required|url',
                'includeCities' => 'required',
                'includeCities.*' => 'required|exists:cities,id',
                'singleDay' => 'nullable|date_format:d.m.Y',
            ]
        );
        $cities = join(',', $request->get('includeCities'));
        $corsOrigin = CORS::formatUrl($request->get('cors-origin'));
        $report = $this->getKey();
        $singleDay = $request->get('singleDay', '');
        if ($singleDay != '') $singleDay = Carbon::createFromFormat('d.m.Y', $singleDay)->format('Y-m-d');

        $url = route('report.embed', compact('report', 'cities', 'corsOrigin', 'singleDay'));
        $randomId = uniqid();

        return $this->renderView('render', compact('url', 'randomId'));
    }


    /**
     * @param Request $request
     * @return Response|Application|Factory|View|string
     */
    public function embed(Request $request)
    {
        $cities = $request->get('cities', []);
        if (!is_array($cities)) {
            $cities = explode(',', $cities);
        }

        $noScript = $request->get('noScript', 0);
        $singleService = $request->get('singleService', '');

        $singleDay = $request->get('singleDay', '');
        if ($singleDay != '') {
            $start = Carbon::createFromFormat('Y-m-d', $request->get('singleDay'))->setTime(0,0,0);
            $end = $start->copy()->setTime(23.59,59);
        }else {
            $start = Carbon::now()->setTime(0,0,0);
            $end = $start->copy()->addMonth(1);
        }


        $errors = new MessageBag();
        $success = new MessageBag();

        if ($request->has('name')) {
            $data = Validator::make(
                $request->all(),
                [
                    'name' => 'required|string',
                    'first_name' => 'required|string',
                    'contact' => 'required|string',
                    'number' => 'required|int|min:1',
                    'service' => 'required|int:exists:services,id'
                ]
            );
            $errors = $data->errors();
            $data = $data->getData();
            if (count($errors) == 0) {
                $service = Service::find($data['service']);
                $test = $service->getSeatFinder()->find($data['number']);
                if ($test) {
                    $data['service_id'] = $data['service'];
                    $data['code'] = Booking::createCode();
                    $booking = Booking::create($data);
                    $message = (($data['number'] == 1) ? 'Ihr Sitzplatz wurde ' : $data['number'].' Sitzplätze wurden ')
                        .'erfolgreich reserviert. Am Eingang zum Gottesdienst erfahren Sie, '
                        .'wo genau Sie sitzen.';
                    $success->add('success', $message);
                } else {
                    $message = ($data['number'] == 1 ? 'Leider konnte in diesem Gottesdienst kein Sitzplatz reserviert werden.' : 'Leider konnten in diesem Gottesdiens tkeine ' . $data['number'] . ' zusammenhängenden Sitzplätze reserviert werden.');
                    $errors->add('sorry', $message);
                }
            }
        }

        if ($singleService) {
            $tmpServices = Service::with('day')->where('id', $singleService)->get();
        } else {
            $tmpServices = Service::where('needs_reservations', 1)
                ->select(['services.*', 'days.date'])
                ->join('days', 'days.id', '=', 'day_id')
                ->whereIn('city_id', $cities)
                ->where('hidden', '!=', 1)
                ->whereHas('location')
                ->whereHas(
                    'day',
                    function ($query) use ($start, $end) {
                        $query->where('date', '>=', $start);
                        $query->where('date', '<=', $end);
                    }
                )
                ->orderBy('days.date', 'ASC')
                ->orderBy('time', 'ASC')
                ->get();
        }

        $services = [];
        foreach ($tmpServices as $service) {
            $services[$service->day->date->format('Ymd')][] = $service;
        }


        $corsOrigin = $request->get('corsOrigin', '');
        $report = $this->getKey();
        $url = route('report.embed', compact('report', 'corsOrigin', 'singleDay', 'singleService', 'noScript'));

        $randomId = uniqid();
        /** @var Response $response */
        $response = response()
            ->view(
                $this->getViewName('embed'),
                compact('randomId', 'services', 'url', 'cities', 'errors', 'success', 'singleService', 'noScript')
            );


        return $response;
    }
}
