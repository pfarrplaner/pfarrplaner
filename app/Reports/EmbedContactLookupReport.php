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


use App\City;
use App\Parish;
use App\StreetRange;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Class EmbedContactLookupReport
 * @package App\Reports
 */
class EmbedContactLookupReport extends AbstractEmbedReport
{

    /**
     * @var string
     */
    public $title = 'Ansprechpartner finden';
    /**
     * @var string
     */
    public $group = 'Website (Gemeindebaukasten)';
    /**
     * @var string
     */
    public $description = 'Erzeugt HTML-Code für die Einbindung einer Ansprechpartner-Box in die Website der Gemeinde';
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
                'city' => 'required',
            ]
        );
        $city = City::findOrFail($request->get('city'));
        $corsOrigin = $request->get('cors-origin');
        $report = $this->getKey();

        $url = route('report.embed', compact('report', 'city', 'corsOrigin'));
        $randomId = uniqid();

        return $this->renderView('render', compact('url', 'randomId'));
    }


    /**
     * @param Request $request
     * @return Response
     */
    public function embed(Request $request)
    {
        $city = City::findOrFail($request->get('city'));

        $street = $request->get('street', '');
        $number = $request->get('number', '');

        $streetRanges = StreetRange::whereHas(
            'parish',
            function ($query) use ($city) {
                $query->where('parishes.city_id', $city->id);
            }
        )->get();

        $streets = [];

        /** @var StreetRange $streetRange */
        foreach ($streetRanges as $streetRange) {
            $streets[$streetRange->name] = $streetRange->name;
        }
        ksort($streets);

        $randomId = uniqid();
        $corsOrigin = $request->get('cors-origin');
        $report = $this->getKey();

        $parish = false;
        if ($request->has('parish')) {
            $parish = Parish::findOrFail($request->get('parish'));
        } elseif (($street != '') && ($number != '')) {
            $parish = Parish::byAddress($street, $number)->first();
        } elseif ($request->cookie('parish')) {
            $parish = Parish::findOrFail($request->cookie('parish'));
        }

        $url = route('report.embed', compact('report', 'city', 'corsOrigin'));
        $report = $this->getKey();

        /** @var Response $response */
        $response = response()
            ->view(
                $this->getViewName('embed'),
                compact('city', 'street', 'number', 'streets', 'randomId', 'url', 'parish', 'report')
            );

        if (false !== $parish) {
            $response->withCookie('parish', $parish->id);
        }

        return $response;
    }
}
