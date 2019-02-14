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

Route::get('/reports/gemeindebrief/setup', ['as' => 'reports.gemeindebrief.setup', 'uses' => 'ReportsController@gemeindebriefSetup']);
Route::post('/reports/gemeindebrief', ['as' => 'reports.gemeindebrief', 'uses' => 'ReportsController@gemeindebrief']);
Route::get('/reports/person/setup', ['as' => 'reports.person.setup', 'uses' => 'ReportsController@personSetup']);
Route::post('/reports/person', ['as' => 'reports.person', 'uses' => 'ReportsController@person']);
Route::get('/reports/predicants/setup', ['as' => 'reports.predicants.setup', 'uses' => 'ReportsController@predicantsSetup']);
Route::post('/reports/predicants', ['as' => 'reports.predicants', 'uses' => 'ReportsController@predicants']);
Route::get('/reports/largetable/setup', ['as' => 'reports.largetable.setup', 'uses' => 'ReportsController@largetableSetup']);
Route::post('/reports/largetable', ['as' => 'reports.largetable', 'uses' => 'ReportsController@largetable']);



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
});

Route::get('/ical/private/{name}/{token}', ['uses' => 'ICalController@private'])->name('ical.private');
Route::get('/ical/gemeinden/{locationIds}/{token}', ['uses' => 'ICalController@byLocation'])->name('ical.byLocation');
Route::get('/connectWithOutlook', ['uses' => 'HomeController@connectWithOutlook'])->name('connectWithOutlook');


Route::get('/whatsnew', function(){
    return view('whatsnew');
})->name('whatsnew');


Route::get('/auto', function(){
    $services = \App\Service::with('location')->get();
    foreach ($services as $service) {
        if ($service->location_id > 0) {
            $service->city_id = $service->location->city_id;
            $service->save();
        }
    }
    die('Done');
});
