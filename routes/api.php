<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
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

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * Route::middleware('auth:api')->get('/user', function (Request $request) {
 * return $request->user();
 * });
 */


Route::namespace('Api')->group(
    function () {
        Route::resource('cities', 'CityController');
        Route::get(
            'calendar/month/{year}/{month}',
            ['as' => 'api.calendar.month', 'uses' => 'CalendarController@month']
        );
        Route::get(
            'servicesByDayAndCity/{day}/{city}',
            ['as' => 'services.byDayAndCity', 'uses' => 'ServiceController@byDayAndCity']
        );
        Route::get('user/{user}/services', ['as' => 'api.user.services', 'uses' => 'ServiceController@byUser']);

        Route::middleware('auth:api')->group(
            function () {
                // Services
                Route::get('service/{service}', 'ServiceController@show')->name('service.show');
                //Route::patch('service/{service}', 'ServiceController@update')->name('api.service.update')->middleware('auth');
                Route::patch('service/{service}', 'ServiceController@update')->name('api.service.update')->middleware(
                    'auth:api'
                );
            }
        );
    }
);

Route::get(
    'test',
    function () {
        return response()->json();
    }
);






