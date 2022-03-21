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

/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 06.08.2019
 * Time: 09:01
 */

namespace App\Reports;


use App\Location;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Inertia\Inertia;

/**
 * Class EmbedServiceTableReport
 * @package App\Reports
 */
class EmbedServiceTableReport extends AbstractEmbedReport
{

    /**
     * @var string
     */
    public $title = 'Liste von Gottesdiensten';
    /**
     * @var string
     */
    public $group = 'Website (Gemeindebaukasten)';
    /**
     * @var string
     */
    public $description = 'Erzeugt HTML-Code für die Einbindung einer Gottesdiensttabelle in die Website der Gemeinde';
    /**
     * @var string
     */
    public $icon = 'fa fa-file-code';

    protected $inertia = true;

    /**
     * @return \Inertia\Response
     */
    public function setup()
    {
        $cities = Auth::user()->cities;
        $locations = Location::whereIn('city_id', $cities->pluck('id'))->get();
        return Inertia::render('Report/EmbedServiceTable/Setup', compact('cities', 'locations'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|string
     */
    public function render(Request $request)
    {
        $data = $request->validate(
            [
                'listType' => 'required',
                'limit' => 'required|int',
                'cities.*' => 'required|int|exists:cities,id',
                'locations.*' => 'required|int|exists:cities,id',
                'maxBaptisms' => 'nullable|int',
                'cors-origin' => 'required|url',
                'withStreaming' => 'nullable|boolean',
            ]
        );

        $listType = $request->get('listType');
        $ids = $data['listType'] == 'table-locations' ? join(',', $data['locations']) : join(',', $data['cities']);

        $limit = $data['limit'] ?? 5;
        $maxBaptisms = $data['maxBaptisms'] ?? null;
        $corsOrigin = $data['cors-origin'] ?? '';

        $url = route('embed.' . $data['listType'], compact('ids', 'limit', 'maxBaptisms', 'corsOrigin'));
        $randomId = uniqid();

        $html = \Illuminate\Support\Facades\View::make('reports.embedservicetable.render', compact('url', 'randomId'))->render();
        $title= 'HTML-Code für eine Gottesdienstliste erstellen';
        return Inertia::render('Report/EmbedServiceTable/Render', compact('html', 'title'));
    }


}
