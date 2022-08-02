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

namespace App\Http\Controllers\Api;

use App\CalendarConnection;
use App\Calendars\Exchange\ExchangeCalendar;
use App\DiaryEntry;
use App\Service;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseFolderIdsType;
use jamesiarmes\PhpEws\Request\FindItemType;

class DiaryController extends \App\Http\Controllers\Controller
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Add a record from an existing sermon
     *
     * @param $category Target category
     * @param Service $service Service record
     * @return JsonResponse New DiaryEntry record
     */
    public function addService($category, Service $service)
    {
        return response()->json(DiaryEntry::createFromService($service, $category));
    }

    /**
     * Add a record from a calendar event
     *
     * @param $category Target category
     * @return JsonResponse New DiaryEntry record
     */
    public function addEvent(Request $request, $category)
    {
        $eventData = $request->get('event', null);
        if (!$eventData) abort(500);
        return response()->json(DiaryEntry::createFromEvent($eventData, $category));
    }

    /**
     * Move DiaryEntry to another category
     *
     * @param $category Target category
     * @param DiaryEntry $diaryEntry Diary entry
     * @return JsonResponse Diary entry with updated info
     */
    public function moveItem($category, DiaryEntry $diaryEntry)
    {
        $diaryEntry->update(['category' => $category]);
        return response()->json($diaryEntry);
    }

    public function update(Request $request, DiaryEntry $diaryEntry)
    {
        $diaryEntry->update($request->validate([
            'title' => 'required|string',
            'date' => 'required|date',
            'category' => 'required|string',
                                               ]));
        return response()->json($diaryEntry);
    }

    /**
     * Delete a record
     *
     * @param DiaryEntry $diaryEntry DiaryEntry to be deleted
     * @return JsonResponse Service record for the deleted item
     */
    public function destroy(DiaryEntry $diaryEntry)
    {
        $service = $diaryEntry->service;
        if (!$service) {
            $service = ['date' => $diaryEntry->date, 'title' => $diaryEntry->title];
        }
        $diaryEntry->delete();
        return response()->json($service);
    }


    public function getCalendar(CalendarConnection $calendarConnection, $date)
    {
        $start = Carbon::parse($date.'-01 0:00:00');
        $end = $start->copy()->endOfMonth();
        /** @var ExchangeCalendar $calendar */
        $calendar = $calendarConnection->getSyncEngine()->getCalendar();
        $events = $calendar->getAllEventsForRange($start, $end);

        $days = [];
        foreach ($events as $event) {
            if ($event->TimeZone == 'UTC') {
                $tzCode = 'UTC';
            } else {
                preg_match('/[\+\-]\d\d\:\d\d/', $event->TimeZone, $matches);
                $tzCode = $matches[0];
            }

            $days[Carbon::parse($event->Start, $tzCode)->setTimezone('Europe/Berlin')->format('Y-m-d')][] = $event;
        }

        return response()->json($days);
    }

}
