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

use App\Day;
use App\Participant;
use App\Service;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


/**
 * Class PersonReport
 * @package App\Reports
 */
class SingleMinistryReport extends AbstractPDFDocumentReport
{

    /**
     * @var string
     */
    public $title = 'Dienstplan für einen bestimmten Dienst';
    /**
     * @var string
     */
    public $group = 'Listen';
    /**
     * @var string
     */
    public $description = 'Liste mit allen eingeteilten Personen für einen bestimmten Dienst';

    /**
     * @return Application|Factory|View
     */
    public function setup()
    {
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first();
        $cities = Auth::user()->cities;

        $ministries = $this->getAvailableMinistries();


        return $this->renderSetupView(compact('maxDate', 'cities', 'ministries'));
    }

    /**
     * @param Request $request
     * @return mixed|string
     */
    public function render(Request $request)
    {
        $data = $request->validate(
            [
                'start' => 'required|date|date_format:d.m.Y',
                'end' => 'required|date|date_format:d.m.Y',
                'city' => 'required|int|exists:cities,id',
                'ministry' => 'required|string',
            ]
        );

        $services = Service::with(['location'])
            ->select(['services.*', 'days.date'])
            ->join('days', 'days.id', '=', 'day_id')
            ->whereDoesntHave('funerals')
            ->whereDoesntHave('weddings')
            ->where('city_id', $data['city'])
            ->whereHas(
                'day',
                function ($query) use ($data) {
                    $query->where('date', '>=', Carbon::createFromFormat('d.m.Y', $data['start']));
                    $query->where('date', '<=', Carbon::createFromFormat('d.m.Y', $data['end']));
                }
            )->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->get();

        return $this->sendToBrowser(
            date('Ymd') . ' Dienstplan ' . $data['ministry'] . '.pdf',
            [
                'start' => $data['start'],
                'end' => $data['end'],
                'services' => $services,
                'ministry' => $data['ministry'],
            ],
            ['format' => 'A4']
        );
    }

    protected function getAvailableMinistries()
    {
        $tmpMinistries = Participant::all()
                ->pluck('category')
                ->unique();

        $ministries = [];
        foreach ($tmpMinistries as $ministry) {
            switch ($ministry) {
                case 'P':
                    if (Auth::user()->can('gd-pfarrer-bearbeiten')) {
                        $ministries[$ministry] = 'Pfarrer*in';
                    }
                    break;
                case 'O':
                    if (Auth::user()->can('gd-organist-bearbeiten')) {
                        $ministries[$ministry] = 'Organist*in';
                    }
                    break;
                case 'M':
                    if (Auth::user()->can('gd-mesner-bearbeiten')) {
                        $ministries[$ministry] = 'Mesner*in';
                    }
                    break;
                case 'A':
                    if (Auth::user()->can('gd-allgemein-bearbeiten')) {
                        $ministries[$ministry] = 'Weitere Beteiligte';
                    }
                    break;
                default:
                    if (Auth::user()->can('gd-allgemein-bearbeiten')) {
                        $ministries[$ministry] = $ministry;
                    }
            }
        }

        return $ministries;
    }
}
