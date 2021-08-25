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



use App\Http\Controllers\SermonController;

// multiple sermons

// one sermon
Route::get('/predigt/{sermon}', [SermonController::class, 'editor'])->name('sermon.editor');
Route::patch('/predigt/{sermon}', [SermonController::class, 'update'])->name('sermon.update');
Route::delete('/predigt/gottesdienst-abkoppeln/{service:slug}', [SermonController::class, 'uncouple'])->name('sermon.uncouple');
Route::post('/predigt/{model}/bild', [SermonController::class, 'attachImage'])->name('sermon.image.attach');
Route::delete('/predigt/{model}/bild', [SermonController::class, 'detachImage'])->name('sermon.image.detach');

// one sermon via-service
Route::get('/gottesdienst/{service:slug}/predigt', [SermonController::class, 'editorByService'])->name('service.sermon.editor');
Route::post('/gottesdienst/{service:slug}/predigt', [SermonController::class, 'store'])->name('sermon.store');


