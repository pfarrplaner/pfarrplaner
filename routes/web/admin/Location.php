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

use App\Http\Controllers\LocationController;
use App\Http\Controllers\SeatingRowController;
use App\Http\Controllers\SeatingSectionController;

Route::get('/orte', [LocationController::class, 'index'])->name('locations.index');

Route::get('/orte/neu', [LocationController::class, 'create'])->name('location.create');
Route::post('/orte/neu', [LocationController::class, 'store'])->name('location.store');

Route::get('/ort/{location}', [LocationController::class, 'edit'])->name('location.edit');
Route::patch('/ort/{location}', [LocationController::class, 'update'])->name('location.update');
Route::delete('/ort/{location}', [LocationController::class, 'destroy'])->name('location.destroy');

// seating sections
Route::get('/ort/{location}/bereich',[SeatingSectionController::class, 'create'])->name('seatingSection.create');
Route::get('/bereich/{seatingSection}',[SeatingSectionController::class, 'edit'])->name('seatingSection.edit');
Route::post('/bereich',[SeatingSectionController::class, 'store'])->name('seatingSection.store');
Route::patch('/bereich/{seatingSection}',[SeatingSectionController::class, 'update'])->name('seatingSection.update');
Route::delete('/bereich/{seatingSection}',[SeatingSectionController::class, 'destroy'])->name('seatingSection.destroy');

// seating rows
Route::get('/ort/{location}/reihe', [SeatingRowController::class, 'create'])->name('seatingRow.create');
Route::get('/reihe/{seatingRow}', [SeatingRowController::class, 'edit'])->name('seatingRow.edit');
Route::post('/reihe', [SeatingRowController::class, 'store'])->name('seatingRow.store');
Route::patch('/reihe/{seatingRow}', [SeatingRowController::class, 'update'])->name('seatingRow.update');
Route::delete('/reihe/{seatingRow}', [SeatingRowController::class, 'destroy'])->name('seatingRow.destroy');
