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
Route::resource('roles', 'RoleController');
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


// RITES (Kasualien)
Route::resource('baptisms', 'BaptismController')->middleware('auth');
Route::get('/baptism/add/{service}', ['as' => 'baptism.add', 'uses' => 'BaptismController@create'])->middleware('auth');
Route::get('/baptism/destroy/{baptism}', ['as' => 'baptism.destroy', 'uses' => 'BaptismController@destroy']);
Route::resource('funerals', 'FuneralController')->middleware('auth');
Route::get('/funeral/add/{service}', ['as' => 'funeral.add', 'uses' => 'FuneralController@create'])->middleware('auth');
Route::get('/funeral/destroy/{funeral}', ['as' => 'funeral.destroy', 'uses' => 'FuneralController@destroy']);
Route::get('/funeral/{funeral}/Formular KRA.pdf', ['as' => 'funeral.form', 'uses' => 'FuneralController@pdfForm']);
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


Route::get('/whatsnew', function(){
    return view('whatsnew');
})->name('whatsnew');


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


// major migration
Route::get('/serviceUserMigration', function(){
    return redirect('/serviceUserMigration/2018/01');
});

Route::get('/serviceUserMigration/{year}/{month}', function($year, $month){
    $start = \Carbon\Carbon::createFromFormat('Y-m-d', $year.'-'.$month.'-01')->setTime(0,0,0);
    $end = (clone $start)->addMonth(1)->subSecond(1);

    $services = \App\Service::whereHas('day', function($query) use ($start, $end) {
        $query->where('date', '>=', $start)
            ->where('date', '<=', $end);
    })->get();

    foreach ($services as $service) {
        echo 'Service on '.$service->day->date->format('Y-m-d').' at '.$service->time.' in '.$service->locationText().'<br />';
        foreach ([
            'P' => $service->pastor,
            'O' => $service->organist,
            'M' => $service->sacristan,
            'A' => $service->others,
                 ] as $category => $p) {
            $participants = [];
            $p = strtr($p, ['/' => '|', '?' => '']);
            if ($p != '') {
                $names = explode('|', $p);
                foreach ($names as $name) {
                    $name = trim($name);
                    $user = \App\User::where('name', 'like', '%' . $name . '%')->first();
                    if (!$user) {
                        $user = new \App\User([
                            'name' => $name,
                            'office' => '',
                            'phone' => '',
                            'address' => '',
                            'preference_cities' => '',
                            'first_name' => '',
                            'last_name' => $name,
                            'title' => '',
                        ]);
                        $user->save();
                        echo 'Added user "' . $name . '".<br />';
                    }
                    $participants[] = $user->id;
                }
            }
            $service->syncParticipantsByCategory($category, $participants);
            echo $category.': '.join(', ', $participants).'<br />';
        }
        echo '<hr />';
    }
    $start->addMonth(1);
    if ($start->year<2021) return redirect('/serviceUserMigration/'.$start->format('Y/m'));
});