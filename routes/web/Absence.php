<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
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

use App\Http\Controllers\AbsenceController;

Route::resource('absences', 'AbsenceController')->except(['index', 'create'])->middleware('auth');
Route::get('planner/users', [AbsenceController::class, 'users'])->name('planner.users');
Route::get('planner/days/{date}/{user}', [AbsenceController::class, 'days'])->name('planner.days');
Route::get('absences/{year?}/{month?}', [AbsenceController::class, 'index'])->name('absences.index');
Route::get('absences/create/{year}/{month}/{user}/{day?}', [AbsenceController::class, 'create'])->name('absences.create');
Route::get('absence/{absence}/approve', [AbsenceController::class, 'approve'])->name('absence.approve');
Route::get('absence/{absence}/reject', [AbsenceController::class, 'approve'])->name('absence.reject');


