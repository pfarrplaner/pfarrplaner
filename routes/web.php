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
Route::resource('days', 'DayController')->middleware('auth');


Route::resource('users', 'UserController')->middleware('auth');
Route::get('user/profile', ['as' => 'user.profile', 'uses' => 'UserController@profile'])->middleware('auth');
Route::patch('user/profile', ['as' => 'user.profile.save', 'uses' => 'UserController@profileSave'])->middleware('auth');

Route::resource('roles', 'RoleController')->middleware('auth');
Route::resource('comments', 'CommentController')->middleware('auth');

Route::resource('services', 'ServiceController')->middleware('auth');
Route::get('services/{service}/edit/{tab?}', ['as' => 'services.edit', 'uses' => 'ServiceController@edit']);
Route::get('services/{service}/ical', ['as' => 'services.ical', 'uses' => 'ServiceController@ical']);


Route::get('/days/add/{year}/{month}', ['uses' => 'DayController@add'])->name('days.add');
Route::get('/services/add/{date}/{city}', ['uses' => 'ServiceController@add'])->name('services.add');
Route::get('/calendar/{year?}/{month?}', ['uses' => 'CalendarController@month'])->name('calendar');
Route::get('/calendar/{year?}/{month?}/printsetup', ['uses' => 'CalendarController@printSetup'])->name('calendar.printsetup');
Route::post('/calendar/{year?}/{month?}/print', ['uses' => 'CalendarController@print'])->name('calendar.print');


// Calendar (JS version)
Route::get('/calendarjs/{year?}/{month?}', ['uses' => 'CalendarController@monthJS'])->name('calendarjs');
Route::get('/ajax/servicesByCityAndDay/{cityId}/{dayId}/', ['uses' => 'ServiceController@servicesByCityAndDay'])->name('servicesByCityAndDay');
Route::get('/ajax/vacationsByDay/{dayId}/', ['uses' => 'VacationController@vacationsByDay'])->name('vacationsByDay');
Route::get('/ajax/liturgicalInfo/{dayId}/', ['uses' => 'LiturgicalDaysController@info'])->name('liturgicalInfo');



Route::get('/reports', ['as' => 'reports.list', 'uses' => 'ReportsController@list']);
Route::get('/reports/setup/{report}', ['as' => 'reports.setup', 'uses' => 'ReportsController@setup']);
Route::post('/reports/render/{report}', ['as' => 'reports.render', 'uses' => 'ReportsController@render']);

Route::get('/input/{input}', ['as' => 'inputs.setup', 'uses' => 'InputController@setup']);
Route::post('/input/collect/{input}', ['as' => 'inputs.input', 'uses' => 'InputController@input']);
Route::post('/input/save/{input}', ['as' => 'inputs.save', 'uses' => 'InputController@save']);

Route::get('/vertretungen', ['as' => 'absences', 'uses' => 'PublicController@absences']);

Route::get('download/{storage}/{code}/{prettyName?}', ['as' => 'download', 'uses' => 'DownloadController@download'])->middleware('auth');


// RITES (Kasualien)
Route::resource('baptisms', 'BaptismController')->middleware('auth');
Route::get('/baptism/add/{service}', ['as' => 'baptism.add', 'uses' => 'BaptismController@create'])->middleware('auth');
Route::get('/baptism/destroy/{baptism}', ['as' => 'baptism.destroy', 'uses' => 'BaptismController@destroy']);
Route::resource('funerals', 'FuneralController')->middleware('auth');

Route::get('/funeral/add/{service}', ['as' => 'funeral.add', 'uses' => 'FuneralController@create'])->middleware('auth');
Route::get('/funeral/destroy/{funeral}', ['as' => 'funeral.destroy', 'uses' => 'FuneralController@destroy']);
Route::get('/funeral/{funeral}/Formular KRA.pdf', ['as' => 'funeral.form', 'uses' => 'FuneralController@pdfForm']);
Route::get('/funeral/wizard', ['as' => 'funerals.wizard', 'uses' => 'FuneralController@wizardStep1']);
Route::post('/funeral/wizard/step2', ['as' => 'funerals.wizard.step2', 'uses' => 'FuneralController@wizardStep2']);
Route::post('/funeral/wizard/step3', ['as' => 'funerals.wizard.step3', 'uses' => 'FuneralController@wizardStep3']);

Route::get('/wedding/wizard', ['as' => 'weddings.wizard', 'uses' => 'WeddingController@wizardStep1']);
Route::post('/wedding/wizard/step2', ['as' => 'weddings.wizard.step2', 'uses' => 'WeddingController@wizardStep2']);
Route::post('/wedding/wizard/step3', ['as' => 'weddings.wizard.step3', 'uses' => 'WeddingController@wizardStep3']);




Route::resource('weddings', 'WeddingController')->middleware('auth');
Route::get('/wedding/add/{service}', ['as' => 'wedding.add', 'uses' => 'WeddingController@create'])->middleware('auth');
Route::get('/wedding/destroy/{wedding}', ['as' => 'wedding.destroy', 'uses' => 'WeddingController@destroy']);

// Home routes
Route::get('/home', ['as' => 'home', 'uses' => 'HomeController@index']);
Route::get('/', function () {
    if (Auth::user()) return redirect()->route('home');
    return redirect()->route('login');
});

Route::get('/changePassword','HomeController@showChangePassword');
Route::post('/changePassword','HomeController@changePassword')->name('changePassword');

Auth::routes();
Route::get('/logout', function() { Auth::logout(); return redirect()->route('login'); });

Route::get('/ical/private/{name}/{token}', ['uses' => 'ICalController@private'])->name('ical.private');
Route::get('/ical/gemeinden/{locationIds}/{token}', ['uses' => 'ICalController@byLocation'])->name('ical.byLocation');
Route::get('/connectWithOutlook', ['uses' => 'HomeController@connectWithOutlook'])->name('connectWithOutlook');


Route::get('/whatsnew', ['as' => 'whatsnew', 'uses' => 'HomeController@whatsnew'])->middleware('auth');


Route::get('/kinderkirche/{city}/pdf', ['as' => 'cc-public-pdf', 'uses' => 'PublicController@childrensChurch']);
Route::get('/kinderkirche/{city}', ['as' => 'cc-public', 'uses' => 'PublicController@childrensChurch']);

Route::post('/showLimitedColumns/{switch}', function($switch){
    Session::put('showLimitedDays', (bool)$switch);
    return json_encode(['showLimitedDays', Session::get('showLimitedDays')]);
})->middleware('auth')->name('showLimitedColumns');

Route::get('/showLimitedColumns/{switch}', function($switch){
    Session::put('showLimitedDays', (bool)$switch);
    return json_encode(['showLimitedDays', Session::get('showLimitedDays')]);
})->middleware('auth')->name('showLimitedColumns');


// utility to create storage link
Route::get('/createStorageLink', function () {
    Artisan::call('storage:link');
});

// helper routes
Route::get('/helper/schedule/run' , function(){
    ignore_user_abort(true);
    Artisan::call('schedule:run');
    return 'OK';
});


Route::get('/liturgyCache', function() {
    //\App\Liturgy::getDayInfo(\App\Day::find(144));
   dd(\Illuminate\Support\Facades\Cache::get('liturgicalDays'));
});