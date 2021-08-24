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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



use App\Http\Controllers\CalController; 

Route::get('/calendar/{date?}/{month?}', [CalController::class, 'index'])->name('calendar');
Route::get('/cal/city/{date}/{city}', [CalController::class, 'city'])->name('cal.city');
Route::get('/cal/day/{day}/{city}', [CalController::class, 'day'])->name('cal.day');
Route::get('/calendar-day/{day}/{city}', [CalController::class, 'singleDay'])->name('calendar.day');
Route::get('/ical/private/{name}/{token}', [ICalController::class, 'private'])->name('ical.private');
Route::get('/ical/gemeinden/{locationIds}/{token}', [ICalController::class, 'byLocation'])->name('ical.byLocation');
Route::get('/ical/urlaub/{user}/{token}', [ICalController::class, 'absences'])->name('ical.absences');
Route::get('/ical/connect', [ICalController::class, 'connect'])->name('ical.connect');
Route::get('/ical/setup/{key}', [ICalController::class, 'setup'])->name('ical.setup');
Route::match(['GET', 'POST'], '/ical/link/{key}', [ICalController::class, 'link'])->name('ical.link');
Route::get('/ical/export/{user}/{token}/{key}', [ICalController::class, 'export'])->name('ical.export');
