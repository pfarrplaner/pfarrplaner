<?php

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

Route::resource('cities', 'CityController')->middleware('auth');
Route::resource('locations', 'LocationController')->middleware('auth');
Route::resource('services', 'ServiceController')->middleware('auth');
Route::resource('days', 'DayController')->middleware('auth');
Route::resource('users', 'UserController')->middleware('auth');
Route::get('/days/add/{year}/{month}', ['uses' => 'DayController@add'])->name('days.add');
Route::get('/services/add/{date}/{city}', ['uses' => 'ServiceController@add'])->name('services.add');
Route::get('/calendar/{year?}/{month?}', ['uses' => 'CalendarController@month'])->name('calendar');
Route::get('/calendar/{year?}/{month?}/printsetup', ['uses' => 'CalendarController@printSetup'])->name('calendar.printsetup');
Route::post('/calendar/{year?}/{month?}/print', ['uses' => 'CalendarController@print'])->name('calendar.print');

Route::get('/reports', ['as' => 'reports.list', 'uses' => 'ReportsController@list']);
Route::get('/reports/setup/{report}', ['as' => 'reports.setup', 'uses' => 'ReportsController@setup']);
Route::post('/reports/render/{report}', ['as' => 'reports.render', 'uses' => 'ReportsController@render']);

Route::get('/input/{input}', ['as' => 'inputs.setup', 'uses' => 'InputController@setup']);
Route::post('/input/collect/{input}', ['as' => 'inputs.input', 'uses' => 'InputController@input']);
Route::post('/input/save/{input}', ['as' => 'inputs.save', 'uses' => 'InputController@save']);

Route::get('/vertretungen', ['as' => 'absences', 'uses' => 'PublicController@absences']);



Route::get('/', function () {
    if (Auth::user()) return redirect()->route('calendar');
    return redirect()->route('login');
});

Route::get('/changePassword','HomeController@showChangePassword');
Route::post('/changePassword','HomeController@changePassword')->name('changePassword');

Auth::routes();
Route::get('/logout', function() { Auth::logout(); return redirect()->route('login'); });


Route::get('/home', function(){
    return redirect()->route('calendar');
})->name('home');

Route::get('/ical/private/{name}/{token}', ['uses' => 'ICalController@private'])->name('ical.private');
Route::get('/ical/gemeinden/{locationIds}/{token}', ['uses' => 'ICalController@byLocation'])->name('ical.byLocation');
Route::get('/connectWithOutlook', ['uses' => 'HomeController@connectWithOutlook'])->name('connectWithOutlook');


Route::get('/whatsnew', function(){
    return view('whatsnew');
})->name('whatsnew');


Route::get('/kinderkirche/{city}', ['as' => 'cc-public', 'uses' => 'PublicController@childrensChurch']);
