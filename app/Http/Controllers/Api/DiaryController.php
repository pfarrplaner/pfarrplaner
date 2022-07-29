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

use App\DiaryEntry;
use App\Service;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
        $diaryEntry = DiaryEntry::create([
                                             'date' => $service->date->copy()->setTimeZone('Europe/Berlin'),
                                             'title' => $service->titleText(false),
                                             'user_id' => Auth::user()->id,
                                             'service_id' => $service->id,
                                             'category' => $category,
                                         ]);

        return response()->json($diaryEntry);
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

    /**
     * Delete a record
     *
     * @param DiaryEntry $diaryEntry DiaryEntry to be deleted
     * @return JsonResponse Service record for the deleted item
     */
    public function destroy(DiaryEntry $diaryEntry)
    {
        $service = $diaryEntry->service;
        $diaryEntry->delete();
        return response()->json($service);
    }

}
