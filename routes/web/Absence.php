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

Route::get('urlaubsplan/{year?}/{month?}', [AbsenceController::class, 'index'])
    ->name('absences.index');
Route::get('urlaubsplan/neu/{year}/{month}/{user}/{day?}', [AbsenceController::class, 'create'])
    ->name('absence.create')
    ->middleware('can:create,App\Absence');


Route::get('urlaub/{absence}', [AbsenceController::class, 'edit'])
    ->name('absence.edit')
    ->middleware('can:update,absence');
Route::patch('urlaub/{absence}', [AbsenceController::class, 'update'])
    ->name('absence.update')
    ->middleware('can:update,absence');
Route::delete('urlaub/{absence}', [AbsenceController::class, 'destroy'])
    ->name('absence.destroy')
    ->middleware('can:delete,absence');

Route::post('urlaub/{absence}/attachment', [AbsenceController::class, 'attach'])->name('absence.attach');
Route::delete('urlaub/{absence}/attachment/{attachment}', [AbsenceController::class, 'detach'])->name('absence.detach');


Route::get('planner/users', [AbsenceController::class, 'users'])
    ->name('planner.users');
Route::get('planner/days/{date}/{user}', [AbsenceController::class, 'days'])
    ->name('planner.days');




