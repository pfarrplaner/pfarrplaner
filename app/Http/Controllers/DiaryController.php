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
use App\Calendars\Exchange\ExchangeCalendar;
use App\DiaryEntry;
use App\Documents\Word\OfficialDiaryWordDocument;
use App\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\LineSpacingRule;
use PhpOffice\PhpWord\Style\Tab;

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
            if (substr($calendarConnection->connection_string, 0, 4) == 'exc:') {
                $calendarConnections->push(['id' => $calendarConnection->id, 'name' => $calendarConnection['title']]);
            }
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


    /**
     * Auto-categorize a calendar event
     * @param $event
     * @return false|string
     */
    private function classifyEvent($event) {
        if ($category = $this->classifyByCategory($event)) return $category;
        if ($category = $this->classifyBySubject($event)) return $category;
        return false;
    }

    /**
     * Auto-categorize calendar event by Outlook category
     * @param $event
     * @return false|string
     */
    private function classifyByCategory($event) {
        if (!$event->Categories)  return false;
        foreach ($event->Categories->String as $category) {
            if (substr(strtolower($category), 0, 13) == 'amtskalender:') {
                $category = strtolower(strtr(trim(substr($category, 13)), [' ' => '', ',' => '/', ';' => '/']));
                switch ($category) {
                    case 'gottesdienst/taufe/abendmahl':
                        return 'GTA';
                    case 'amtshandlungen':
                        return 'AMT';
                    case 'seelsorge/diakonie':
                        return 'SSD';
                    case 'unterricht/jugendarbeit':
                        return 'UJU';
                    case 'bibelarbeit/erwachsenenbildung':
                        return 'BEB';
                    case 'mitarbeiterschaft/gremien/dienstbesprechung':
                        return 'MGD';
                }
            }
        }
        return false;
    }

    /**
     * Auto-categorize calendar event by subject keywords
     *
     * Recognized keywords:
     * SSD: Trauergespräch, Geburtstagsbesuch, Besuch, Traugespräch, Taufgespräch, Ehejubiläum
     * UJU: RU, KU, Konfi, Konfi...
     * MGD: KGR, Sitzung, Planung, Beirat, Ausschuss, DB, Dienstbesprechung
     *
     * @param $event
     * @return false|string
     */
    private function classifyBySubject($event) {
        if (!$event->Subject)  return false;
        $subject = strtolower($event->Subject);
        $firstWord = strtok($subject, ' ');

        // SSD
        if ($firstWord == 'trauergespräch') return 'SSD';
        if ($firstWord == 'traugespräch') return 'SSD';
        if ($firstWord == 'taufgespräch') return 'SSD';
        if ($firstWord == 'geburtstagsbesuch') return 'SSD';
        if ($firstWord == 'besuch') return 'SSD';
        if ($firstWord == 'ehejubiläum') return 'SSD';

        // UJU
        if ($firstWord == 'ru') return 'UJU';
        if ($firstWord == 'ku') return 'UJU';
        if ($firstWord == 'konfi') return 'UJU';
        if (str_contains($firstWord, 'konfi') && ($firstWord != 'konfirmation')) return 'UJU';

        // MGD
        if ($firstWord == 'kgr') return 'MGD';
        if ($firstWord == 'kgr-sitzung') return 'MGD';
        if ($firstWord == 'sitzung') return 'MGD';
        if ($firstWord == 'planung') return 'MGD';
        if ($firstWord == 'beirat') return 'MGD';
        if ($firstWord == 'ausschuss') return 'MGD';
        if ($firstWord == 'db') return 'MGD';
        if ($firstWord == 'dienstbesprechung') return 'MGD';


        return false;
    }

    /**
     * Auto-create DiaryEntry records for calendar entries based on categories and keywords
     *
     * @param null $date
     * @return \Illuminate\Http\RedirectResponse
     */
    public function autoSortCalendar($date, CalendarConnection $calendarConnection)
    {
        $start = Carbon::parse($date . '-01 0:00:00');
        $end = $start->copy()->addMonth(1)->subSecond(1);

        /** @var ExchangeCalendar $calendar */
        $calendar = $calendarConnection->getSyncEngine()->getCalendar();
        $events = $calendar->getAllEventsForRange($start, $end);

        foreach ($events as $event) {
            if ($category = $this->classifyEvent($event)) {
                DiaryEntry::createFromEvent((array)$event, $category);
            }

        }

        return redirect()->route('diary.index', compact('date'));
    }

    /**
     * Render word document for a month
     *
     * @param $date
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function exportToWord($date)
    {
        $user = Auth::user();
        $start = Carbon::parse($date . '-01 0:00:00');
        $end = $start->copy()->addMonth(1)->subSecond(1);

        $diaryEntries = DiaryEntry::where('user_id', Auth::user()->id)
            ->whereDate('date', '>=', $start)
            ->whereDate('date', '<=', $end)
            ->orderBy('date')
            ->get();

        $doc = new OfficialDiaryWordDocument();
        $doc->render($start, $end, $diaryEntries);
        $doc->sendToBrowser($date.' Amtskalender '.$user->lastName().' '.$user->first_name);

    }

    /**
     * Delete a single record
     *
     * @param DiaryEntry $diaryEntry
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(DiaryEntry $diaryEntry)
    {
        $date = Carbon::parse($diaryEntry->date);
        $diaryEntry->delete();
        return redirect()->route('diary.index', ['date' => $date->format('Y-m')]);
    }

}
