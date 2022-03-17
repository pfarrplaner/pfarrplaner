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


use App\Service;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Inertia\Inertia;

/**
 * Class EmbedStreamingReport
 * @package App\Reports
 */
class EmbedStreamingReport extends AbstractEmbedReport
{

    /**
     * @var string
     */
    public $title = 'Streaming von Gottesdiensten';
    /**
     * @var string
     */
    public $group = 'Website (Gemeindebaukasten)';
    /**
     * @var string
     */
    public $description = 'Erzeugt HTML-Code für die Einbindung eines Streaming-Gottesdiensts in die Website der Gemeinde';
    /**
     * @var string
     */
    public $icon = 'fa fa-file-code';

    protected $inertia = true;

    /**
     * @return \Inertia\Render
     */
    public function setup()
    {
        $cities = Auth::user()->cities;
        return Inertia::render('Report/EmbedStreaming/Setup', compact('cities'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|string
     */
    public function render(Request $request)
    {
        $data = $request->validate(
            [
                'city' => 'required',
                'cors-origin' => 'required|url',
            ]
        );
        $data['url'] = route('embed.report', ['report' => 'streaming', 'city' => $data['city']]);
        $data['randomId'] = uniqid();
        $html = \Illuminate\Support\Facades\View::make('reports.embedstreaming.render', $data)->render();
        $title = 'HTML-Code für Gottesdienststreams erstellen';
        return Inertia::render('Report/EmbedStreaming/Render', compact('html', 'title'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function embed(Request $request)
    {
        if (!$request->has('city')) {
            abort(404);
        }
        $nextService = Service::where('city_id', $request->get('city'))
            ->select('services.*')
            ->join('days', 'days.id', 'services.day_id')
            ->notHidden()
            ->whereHas(
                'day',
                function ($query) {
                    $query->where('date', '>=', Carbon::now()->format('Y-m-d'));
                }
            )
            ->where('youtube_url', '!=', '')
            ->orderBy('days.date')
            ->orderBy('time')
            ->first();

        $lastServices = Service::where('city_id', $request->get('city'))
            ->select('services.*')
            ->join('days', 'days.id', 'services.day_id')
            ->notHidden()
            ->whereHas(
                'day',
                function ($query) {
                    $query->where('date', '<=', Carbon::now()->format('Y-m-d'));
                }
            )
            ->orderBy('days.date', 'desc')
            ->orderBy('time', 'desc')
            ->limit(100)
            ->get();

        return view('reports.embedstreaming.embed', compact('nextService', 'lastServices'));
    }


}
