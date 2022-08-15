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

use App\Http\Controllers\Api\DiaryController;

Route::post('amtskalender/kategorie/{category}/gottesdienste/{service}', [DiaryController::class, 'addService'])->name('diary.category.services.add');
Route::post('amtskalender/kategorie/{category}/verschieben/{diaryEntry}', [DiaryController::class, 'moveItem'])->name('diary.category.items.move');

Route::post('amtskalender/eintrag', [DiaryController::class, 'store'])->name('diary.entry.store');
Route::patch('amtskalender/eintrag/{diaryEntry}', [DiaryController::class, 'update'])->name('diary.entry.update');
Route::delete('amtskalender/eintrag/{diaryEntry}', [DiaryController::class, 'destroy'])->name('diary.entry.destroy');

Route::get('amtskalender/kalender/{calendarConnection}/{date}', [DiaryController::class, 'getCalendar'])->name('diary.calendar');
Route::post('amtskalender/kategorie/{category}/events', [DiaryController::class, 'addEvent'])->name('diary.category.events.add');
