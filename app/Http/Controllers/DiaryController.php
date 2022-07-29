<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2022 Christoph Fischer, https://christoph-fischer.org
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

namespace App\Http\Controllers;

use App\CalendarConnection;
use App\DiaryEntry;
use App\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DiaryController extends Controller
{

    /**
     * Constructor
     */
    public function __construct()
    {
        return $this->middleware('auth');
    }

    /**
     * Show the user's diary
     *
     * @return \Inertia\Response Inertia-rendered view data
     */
    public function index($date = null)
    {
        $start = $date ? Carbon::parse($date . '-01 0:00:00') : Carbon::now()->startOfMonth();
        $end = $start->copy()->addMonth(1)->subSecond(1);
        $date ??= Carbon::now()->startOfMonth();

        $services = Service::with('location')
            ->between($start, $end)
            ->userParticipates(Auth::user())
            ->where(function ($q) {
                $q->whereDoesntHave('diaryEntries')
                    ->orWhereHas('diaryEntries', function ($q2) {
                        $q2->where('user_id', '!=', Auth::user()->id);
                    });
            })
            ->ordered()
            ->get();

        $diaryEntries = DiaryEntry::where('user_id', Auth::user()->id)
            ->whereDate('date', '>=', $start)
            ->whereDate('date', '<=', $end)
            ->orderBy('date')
            ->get();

        $calendarConnections = collect();
        $allCalendarConnections = CalendarConnection::where('user_id', Auth::user()->id)->get();
        foreach ($allCalendarConnections as $calendarConnection) {
            if (substr($calendarConnection->connection_string, 0, 4) == 'exc:')
                $calendarConnections->push(['id' => $calendarConnection->id, 'name' => $calendarConnection['title']]);
        }

        return Inertia::render('Diary/Index', compact('date', 'services', 'diaryEntries', 'calendarConnections'));
    }

    /**
     * Auto-create DiaryEntry records for all services in a month
     *
     * @param null $date
     * @return \Illuminate\Http\RedirectResponse
     */
    public function autoSortServices($date)
    {
        $start = Carbon::parse($date . '-01 0:00:00');
        $end = $start->copy()->addMonth(1)->subSecond(1);

        $services = Service::with('location')
            ->between($start, $end)
            ->userParticipates(Auth::user())
            ->where(function ($q) {
                $q->whereDoesntHave('diaryEntries')
                    ->orWhereHas('diaryEntries', function ($q2) {
                        $q2->where('user_id', '!=', Auth::user()->id);
                    });
            })
            ->ordered()
            ->get();

        foreach ($services as $service) {
            DiaryEntry::createFromService(
                $service,
                (count($service->weddings) || count($service->funerals)) ? 'AMT' : 'GTA'
            );
        }

        return redirect()->route('diary.index', compact('date'));
    }

}
