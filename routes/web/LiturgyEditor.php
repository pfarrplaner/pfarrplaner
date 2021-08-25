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



use App\Http\Controllers\LiturgyEditorController;

Route::get('/liturgie/{service:slug}', [LiturgyEditorController::class, 'editor'])->name('liturgy.editor');
Route::get('/liturgie/{service:slug}/optionen/{key}', [LiturgyEditorController::class, 'liturgy.configure']);
Route::match(['GET', 'POST'], '/liturgie/{service:slug}/download/{key}', [LiturgyEditorController::class, 'download'])->name('liturgy.download');
Route::post('/liturgie/{service:slug}', [LiturgyEditorController::class, 'save'])->name('liturgy.save');
Route::get('/liturgie/{service:slug}/quellen', [LiturgyEditorController::class, 'sources'])->name('liturgy.sources');
Route::get('/liturgie/{service:slug}/predigten', [LiturgyEditorController::class, 'sermons'])->name('liturgy.sermons');
Route::post('/liturgie/{service:slug}/import/{source}', [LiturgyEditorController::class, 'import'])->name('liturgy.import');
