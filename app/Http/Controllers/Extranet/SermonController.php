<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
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

namespace App\Http\Controllers\Extranet;


use App\Http\Controllers\Controller;
use App\Sermon;
use App\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SermonController extends Controller
{

    protected function transformSermon(Sermon $sermon, $includePreview = false) {
        $services = [];
        $isPrivate = false;
        foreach ($sermon->services as $service) {
            $isPrivate = $isPrivate || (count($service->funerals) || count($service->weddings));
            if (($service->day->date <= Carbon::now()) || ($includePreview)) {
                $services[] = [
                    /** @var \App\Service $service */
                    'date' => $service->dateTime,
                    'time' => $service->timeText(),
                    'title' => $service->titleText(false),
                    'location' => $service->locationText(),
                    'liturgy' => $service->day->liturgy,
                    'video' => $service->youtube_url,
                ];
            }
        }
        $sermon->events = $services;
        $sermon->isPrivate = $isPrivate;
        return $sermon;
    }

    public function latest(Request $request)
    {
        $user = Auth::user();
        $events = [];
        $includeText = $request->get('includeText', false);

        $tmpSermons = Sermon::with('services')
            ->whereHas(
                'services',
                function ($query) use ($user) {
                    $query->userParticipates($user, 'P');
                    $query->endingAt(Carbon::now());
                }
            )->get();

        $sermons = [];
        foreach ($tmpSermons as $sermon) {
            $key = $sermon->latestService();
            $sermon = $this->transformSermon($sermon, false);
            if (!$includeText) $sermon->text = '';
            if (count($sermon->services)) $sermons[$key] = $sermon;
            unset($sermon->services);
        }
        krsort($sermons);

        return response()->json(compact('user', 'sermons'));
    }

    public function details($sermonId)
    {
        if (is_numeric($sermonId)) {
            $sermon = Sermon::findOrFail($sermonId);
        } else {
            $sermon = Sermon::where('slug', $sermonId)->first() or abort(404);
        }
        return response()->json($this->transformSermon($sermon, true));
    }

}
