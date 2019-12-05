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
Route::post('user/{user}/join', ['as' => 'user.join', 'uses' => 'UserController@join'])->middleware('auth');
Route::post('users/join', ['as' => 'users.join', 'uses' => 'UserController@doJoin'])->middleware('auth');
Route::get('user/{user}/services', ['as' => 'user.services', 'uses' => 'UserController@services'])->middleware('auth');

Route::resource('roles', 'RoleController')->middleware('auth');
Route::resource('comments', 'CommentController')->middleware('auth');

Route::resource('services', 'ServiceController')->middleware('auth');
Route::get('services/{service}/edit/{tab?}', ['as' => 'services.edit', 'uses' => 'ServiceController@edit']);
Route::get('services/{service}/ical', ['as' => 'services.ical', 'uses' => 'ServiceController@ical']);

Route::resource('absences', 'AbsenceController')->middleware('auth');
Route::get('absences/{year?}/{month?}', ['as' => 'absences.index', 'uses' => 'AbsenceController@index']);
Route::get('absences/create/{year}/{month}/{user}', ['as' => 'absences.create', 'uses' => 'AbsenceController@create']);

Route::resource('tags', 'TagController')->middleware('auth');
Route::resource('parishes', 'ParishController')->middleware('auth');

// embed in web site:
Route::get('services/embed/locations/{ids}/{limit?}', ['as' => 'embed.table-locations', 'uses' => 'EmbedController@embedByLocations']);
Route::get('services/embed/baptismalServices/{ids}/{limit?}/{maxBaptisms?}', ['as' => 'embed.table-baptismalservices', 'uses' => 'EmbedController@embedByBaptismalServices']);
Route::get('services/embed/cities/{ids}/{limit?}', ['as' => 'embed.table-cities', 'uses' => 'EmbedController@embedByCities']);
Route::get('services/embed/cc/cities/{ids}/{limit?}', ['as' => 'embed.table-cc', 'uses' => 'EmbedController@embedCCByCities']);
Route::get('user/embed/vacations/{user}', ['as' => 'embed.user.vacations', 'uses' => 'EmbedController@embedUserVacations' ]);


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
Route::post('/reports/render/{report}', ['as' => 'reports.render', 'uses' => 'ReportsController@render']);
Route::get('/report/{report}', ['as' => 'reports.setup', 'uses' => 'ReportsController@setup']);
Route::get('/report/{report}/embed', ['as' => 'report.embed', 'uses' => 'ReportsController@embed'])->middleware('cors');
Route::post('/report/{report}/{step}', ['as' => 'report.step', 'uses' => 'ReportsController@step']);
Route::get('/report/{report}/{step}', ['as' => 'report.step', 'uses' => 'ReportsController@step']);

Route::get('/input/{input}', ['as' => 'inputs.setup', 'uses' => 'InputController@setup']);
Route::post('/input/collect/{input}', ['as' => 'inputs.input', 'uses' => 'InputController@input']);
Route::post('/input/save/{input}', ['as' => 'inputs.save', 'uses' => 'InputController@save']);

//Route::get('/vertretungen', ['as' => 'absences', 'uses' => 'PublicController@absences']);

Route::get('download/{storage}/{code}/{prettyName?}', ['as' => 'download', 'uses' => 'DownloadController@download'])->middleware('auth');


// RITES (Kasualien)
Route::resource('baptisms', 'BaptismController')->middleware('auth');
Route::get('/baptism/add/{service}', ['as' => 'baptism.add', 'uses' => 'BaptismController@create'])->middleware('auth');
Route::get('/baptism/destroy/{baptism}', ['as' => 'baptism.destroy', 'uses' => 'BaptismController@destroy']);
Route::get('/baptism/{baptism}/appointment/ical', ['as' => 'baptism.appointment.ical', 'uses' => 'BaptismController@appointmentIcal']);
Route::post('/baptism/done/{baptism}', ['as' => 'baptism.done', 'uses' => 'BaptismController@done']);


Route::resource('funerals', 'FuneralController')->middleware('auth');
Route::get('/funeral/add/{service}', ['as' => 'funeral.add', 'uses' => 'FuneralController@create'])->middleware('auth');
Route::get('/funeral/destroy/{funeral}', ['as' => 'funeral.destroy', 'uses' => 'FuneralController@destroy']);
Route::get('/funeral/{funeral}/Formular KRA.pdf', ['as' => 'funeral.form', 'uses' => 'FuneralController@pdfForm']);
Route::get('/funeral/wizard', ['as' => 'funerals.wizard', 'uses' => 'FuneralController@wizardStep1']);
Route::post('/funeral/wizard/step2', ['as' => 'funerals.wizard.step2', 'uses' => 'FuneralController@wizardStep2']);
Route::post('/funeral/wizard/step3', ['as' => 'funerals.wizard.step3', 'uses' => 'FuneralController@wizardStep3']);
Route::post('/funeral/done/{funeral}', ['as' => 'funeral.done', 'uses' => 'FuneralController@done']);
Route::get('/funeral/{funeral}/appointment/ical', ['as' => 'funeral.appointment.ical', 'uses' => 'FuneralController@appointmentIcal']);

Route::get('/wedding/wizard', ['as' => 'weddings.wizard', 'uses' => 'WeddingController@wizardStep1']);
Route::post('/wedding/wizard/step2', ['as' => 'weddings.wizard.step2', 'uses' => 'WeddingController@wizardStep2']);
Route::post('/wedding/wizard/step3', ['as' => 'weddings.wizard.step3', 'uses' => 'WeddingController@wizardStep3']);
Route::post('/wedding/done/{wedding}', ['as' => 'wedding.done', 'uses' => 'WeddingController@done']);




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
Route::get('/ical/urlaub/{user}/{token}', ['uses' => 'ICalController@absences'])->name('ical.absences');


Route::get('/ical/connect', ['uses' => 'ICalController@connect'])->name('connectWithOutlook');
Route::get('/ical/setup/{key}', ['uses' => 'ICalController@setup'])->name('ical.setup');
Route::get('/ical/link/{key}', ['uses' => 'ICalController@link'])->name('ical.link');
Route::post('/ical/link/{key}', ['uses' => 'ICalController@link'])->name('ical.link');
Route::get('/ical/export/{user}/{token}/{key}', ['uses' => 'ICalController@export'])->name('ical.export');


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

// last service updated (timestamp)
Route::get('/lastUpdate', ['as' => 'lastUpdate', 'uses' => 'ServiceController@lastUpdate']);


// counters
Route::get('/counter/{counter}', ['as' => 'counter', 'uses' => 'HomeController@counters']);


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


// current tests
Route::get('test', function(\Illuminate\Http\Request $request){
    Mail::raw('Test', function($message){
        $message->from('noreply@pfarrplaner.de');
        $message->to('chris@toph.de')->subject('Testing');
    });
});

// tests with vue
Route::get('vue', function(){
   return response()->view('vue/app');
});

// revisions
Route::resource('revisions', 'RevisionController');
Route::post('revisions', 'RevisionController@index')->name('revisions.index.post');
Route::post('revisions/revert', 'RevisionController@revert')->name('revisions.revert');
