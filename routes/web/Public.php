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



use App\Http\Controllers\PublicController;

Route::get('/kinderkirche/{city}/pdf', [PublicController::class, 'childrensChurch'])->name('cc-public-pdf');
Route::get('/kinderkirche/{city}', [PublicController::class, 'childrensChurch'])->name('cc-public');
Route::get('/dimissoriale/{type}/{id}', [PublicController::class, 'showDimissorial'])->name('dimissorial.show');
Route::post('/dimissoriale/{type}/{id}', [PublicController::class, 'grantDimissorial'])->name('dimissorial.grant');
Route::get('/anfrage/{ministry}/{user}/{services}/{sender?}', [PublicController::class, 'ministryRequest'])->name('ministry.request');
Route::post('/anfrage/{ministry}/{user}/{sender?}', [PublicController::class, 'ministryRequestFilled'])->name('ministry.request.fill');
Route::get('/dienste/{cityName}/{ministry}', [PublicController::class, 'ministryPlan'])->name('ministry.plan');
Route::get('/services/{city}/streaming/next', [PublicController::class, 'nextStream'])->name('service.nextstream');

Route::post('/kontaktformular', [PublicController::class, 'submitContactForm'])->name('contactForm.submit');
