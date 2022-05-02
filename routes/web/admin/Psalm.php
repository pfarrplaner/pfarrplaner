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
 * but WITHOUT ANY WARRANTY; without even the imppsalm warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

use App\Http\Controllers\PsalmController;

Route::get('/psalmen', [PsalmController::class, 'index'])->name('psalms.index');
Route::post('/psalmen', [PsalmController::class, 'store'])->name('psalm.store');
Route::get('/psalmen/neu', [PsalmController::class, 'create'])->name('psalm.create');

Route::get('/psalm/{psalm}', [PsalmController::class, 'edit'])->name('psalm.edit');
Route::patch('/psalm/{psalm}', [PsalmController::class, 'update'])->name('psalm.update');
Route::delete('/psalm/{psalm}', [PsalmController::class, 'destroy'])->name('psalm.destroy');

Route::post('/psalm/{psalm}/split/{reference}',[PsalmController::class, 'split'])->name('psalm.psalmbook.split');

