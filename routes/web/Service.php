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



use App\Http\Controllers\ServiceController;

// multiple services
Route::get('/gottesdienste/neu/{city}/{date}', [ServiceController::class, 'create'])->name('service.create');
Route::get('/gottesdienste/meine/letzte-aktualisierung', [ServiceController::class, 'lastUpdate'])->name('services.currentUser.lastUpdate');

// one service
Route::get('/gottesdienst/{service:slug}', [ServiceController::class, 'edit'])->name('service.edit');
Route::patch('/gottesdienst/{service:slug}', [ServiceController::class, 'update'])->name('service.update');
Route::delete('/gottesdienst/{service:slug}', [ServiceController::class, 'destroy'])->name('service.destroy');
Route::get('/gottesdienst-daten/{service:slug}', [ServiceController::class, 'data'])->name('service.data');
Route::patch('/gottesdienst-predigt/{service:slug}', [ServiceController::class, 'setSermon'])->name('service.setsermon');

// additional service routes
Route::get('/gottesdienst/{service:slug}/ical', [ServiceController::class, 'ical'])->name('service.ical');
Route::get('/gottesdienst/{service:slug}/liedblatt', [ServiceController::class, 'songsheet'])->name('service.songsheet');
Route::post('/gottesdienst/{service:slug}/dateien', [ServiceController::class, 'attach'])->name('service.attach');
Route::delete('/gottesdienst/{service:slug}/datei/{attachment}', [ServiceController::class, 'detach'])->name('service.detach');

