<?php

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






