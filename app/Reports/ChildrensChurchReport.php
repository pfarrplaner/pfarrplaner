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
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Inertia\Inertia;


/**
 * Class ChildrensChurchReport
 * @package App\Reports
 */
class ChildrensChurchReport extends AbstractPDFDocumentReport
{

    /**
     * @var string
     */
    public $title = 'Programm für die Kinderkirche';
    /**
     * @var string
     */
    public $group = 'Listen';
    /**
     * @var string
     */
    public $description = 'Übersicht aller Termine der Kinderkirche mit Themen und Mitarbeitern';

    protected $inertia = true;

    /**
     * @return \Inertia\Response
     */
    public function setup()
    {
        $cities = Auth::user()->cities;
        return Inertia::render('Report/ChildrensChurch/Setup', compact('cities'));
    }

    /**
     * @param Request $request
     * @return mixed|string
     */
    public function render(Request $request)
    {
        $data = $request->validate(
            [
                'city' => 'required',
                'start' => 'required|date',
                'end' => 'required|date',
            ]
        );

        $serviceList = Service::between(Carbon::parse($data['start']), Carbon::parse($data['end']))
            ->notHidden()
            ->where('city_id', $data['city'])
            ->where('cc', 1)
            ->ordered()
            ->get()
            ->groupBy('key_date');

        $dates = Service::select(DB::raw('DISTINCT DATE(date) as day'))
            ->between(Carbon::createFromFormat('d.m.Y', $data['start'])->subWeek(1), Carbon::createFromFormat('d.m.Y', $data['end']))
            ->notHidden()
            ->where('city_id', $data['city'])
            ->where('cc', 1)
            ->orderBy('day', 'ASC')
            ->get()
            ->pluck('day');

        $city = City::findOrFail($data['city']);

        if (count($dates)) {
            $minDate = $dates->min();
            $maxDate = $dates->max();
        } else {
            return redirect()->back()->with('error', 'In der gewählten Zeit findet kein Kindergottesdienst statt.');
        }

        return $this->sendToBrowser(
            date('Ymd') . ' Kinderkirche ' . $city->name . '.pdf',
            [
                'start' => $minDate,
                'end' => $maxDate,
                'city' => $city,
                'services' => $serviceList,
                'count' => count($dates),
            ],
            ['format' => 'A4']
        );
    }

}
