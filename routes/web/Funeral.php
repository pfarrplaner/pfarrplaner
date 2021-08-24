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



use App\Http\Controllers\FuneralController; 

Route::get('funerals/create/{service}', [FuneralController::class, 'create'])->name('funerals.create');
Route::get('funerals/{funeral}', [FuneralController::class, 'edit'])->name('funerals.edit');
Route::patch('funerals/{funeral}', [FuneralController::class, 'update'])->name('funerals.update');
Route::delete('funerals/{funeral}', [FuneralController::class, 'destroy'])->name('funerals.destroy');
Route::post('funerals/{funeral}/attachment', [FuneralController::class, 'attach'])->name('funeral.attach');
Route::delete('funerals/{funeral}/attachment/{attachment}', [FuneralController::class, 'detach'])->name('funeral.detach');
Route::get('/funeral/add/{service}', [FuneralController::class, 'create'])->name('funeral.add');
Route::get('/funeral/destroy/{funeral}', [FuneralController::class, 'destroy'])->name('funeral.destroy');
Route::get('/funeral/{funeral}/Formular KRA.pdf', [FuneralController::class, 'pdfForm'])->name('funeral.form');
Route::get('/funeral/wizard', [FuneralController::class, 'wizard'])->name('funerals.wizard');
Route::post('/funeral/wizard', [FuneralController::class, 'wizardSave'])->name('funerals.wizard.save');
Route::get('/funeral/{funeral}/appointment/ical', [FuneralController::class, 'appointmentIcal'])->name('funeral.appointment.ical');
