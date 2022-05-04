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

use App\Http\Controllers\SongbookController;

Route::get('/liederbuecher', [SongbookController::class, 'index'])->name('songbooks.index');
Route::get('/liederbuecher/neu', [SongbookController::class, 'create'])->name('songbook.create');
Route::post('/liederbuecher', [SongbookController::class, 'store'])->name('songbook.store');
Route::get('/liederbuch/{songbook}', [SongbookController::class, 'edit'])->name('songbook.edit');
Route::patch('/liederbuch/{songbook}', [SongbookController::class, 'update'])->name('songbook.update');
Route::delete('/liederbuch/{songbook}', [SongbookController::class, 'destroy'])->name('songbook.destroy');

// Cover image
Route::post('/liederbuch/{model}/cover', [SongbookController::class, 'attachImage'])->name('songbook.cover.attach');
Route::delete('/liederbuch/{model}/cover', [SongbookController::class, 'detachImage'])->name('songbook.cover.detach');

