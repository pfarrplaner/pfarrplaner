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
use App\Service;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Inertia\Inertia;


/**
 * Class OfferingPlanReport
 * @package App\Reports
 */
class OfferingPlanReport extends AbstractPDFDocumentReport
{

    /**
     * @var string
     */
    public $title = 'Opferplan';
    /**
     * @var string
     */
    public $group = 'Opfer';
    /**
     * @var string
     */
    public $description = 'Übersicht aller Opferzwecke für ein Jahr';

    protected $inertia = true;

    /**
     * @return \Inertia\Response
     */
    public function setup()
    {
        $cities = Auth::user()->cities;
        return Inertia::render('Report/OfferingPlan/Setup', compact('cities'));
    }

    /**
     * @param Request $request
     * @return mixed|string
     */
    public function render(Request $request)
    {
        $data = $request->validate(
            [
                'city' => 'required|int|exists:cities,id',
                'year' => 'required|int'
            ]
        );

        $city = City::findOrFail($data['city']);

        $serviceList = Service::inCity($city)
            ->whereYear('date', $data['year'])
            ->ordered()
            ->get();

        $dates = $serviceList->pluck('date')->toArray();
        $serviceList = $serviceList->groupBy('key_date');


        $minDate = min($dates);
        $maxDate = max($dates);

        return $this->sendToFile(
            $data['year'] . ' Opferplan ' . $city->name . '.pdf',
            [
                'start' => $minDate,
                'end' => $maxDate,
                'city' => $city,
                'services' => $serviceList,
                'count' => count($dates),
                'year' => $data['year'],
            ],
            ['format' => 'A4']
        );
    }

}
