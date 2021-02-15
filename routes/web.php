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

use Inertia\Inertia;

Route::resource('cities', 'CityController')->middleware('auth');
Route::resource('locations', 'LocationController')->middleware('auth');
Route::resource('days', 'DayController')->middleware('auth');


Route::resource('users', 'UserController')->middleware('auth');
Route::get('user/profile', ['as' => 'user.profile', 'uses' => 'UserController@profile'])->middleware('auth');
Route::patch('user/profile', ['as' => 'user.profile.save', 'uses' => 'UserController@profileSave'])->middleware('auth');
Route::post('user/{user}/join', ['as' => 'user.join', 'uses' => 'UserController@join'])->middleware('auth');
Route::post('users/join', ['as' => 'users.join', 'uses' => 'UserController@doJoin'])->middleware('auth');
Route::get('user/{user}/services', ['as' => 'user.services', 'uses' => 'UserController@services'])->middleware('auth');
Route::get('user/switch/{user}', ['as' => 'user.switch', 'uses' => 'UserController@switch'])->middleware('auth');

Route::resource('roles', 'RoleController')->middleware('auth');
Route::resource('comments', 'CommentController')->middleware('auth');

Route::resource('services', 'ServiceController')->middleware('auth');
Route::get('services/{service}/edit/{tab?}', ['as' => 'services.edit', 'uses' => 'ServiceController@edit']);
Route::get('services/{service}/ical', ['as' => 'services.ical', 'uses' => 'ServiceController@ical']);
Route::get('/service/{service}/songsheet', 'ServiceController@songsheet')->name('service.songsheet');
Route::get('/services/{city}/streaming/next', 'PublicController@nextStream')->name('service.nextstream');

Route::resource('absences', 'AbsenceController')->middleware('auth');
Route::get('absences/{year?}/{month?}', ['as' => 'absences.index', 'uses' => 'AbsenceController@index']);
Route::get('absences/create/{year}/{month}/{user}', ['as' => 'absences.create', 'uses' => 'AbsenceController@create']);
Route::get('absence/{absence}/approve', 'AbsenceController@approve')->name('absence.approve');
Route::get('absence/{absence}/reject', 'AbsenceController@approve')->name('absence.reject');

Route::resource('tags', 'TagController')->middleware('auth');
Route::resource('parishes', 'ParishController')->middleware('auth');

Route::resource('seatingSection', 'SeatingSectionController');
Route::resource('seatingRow', 'SeatingRowController');
Route::resource('booking', 'BookingController');
Route::get('services/{service}/bookings', 'BookingController@index')->name('service.bookings');
Route::get('seatfinder/{service}/{number?}', 'BookingController@findSeat')->name('seatfinder');
Route::get('services/{service}/bookingList', 'BookingController@finalize')->name('booking.finalize');

Route::get('qr/{city}', 'CityController@qr')->name('qr');


Route::resource('calendarConnection', 'CalendarConnectionController');
Route::post('calendarConnection/configure', 'CalendarConnectionController@configure')->name('calendarConnection.configure');

// embed in web site:
Route::get(
    'services/embed/locations/{ids}/{limit?}',
    ['as' => 'embed.table-locations', 'uses' => 'EmbedController@embedByLocations']
);
Route::get(
    'services/embed/baptismalServices/{ids}/{limit?}/{maxBaptisms?}',
    ['as' => 'embed.table-baptismalservices', 'uses' => 'EmbedController@embedByBaptismalServices']
);
Route::get(
    'services/embed/cities/{ids}/{limit?}',
    ['as' => 'embed.table-cities', 'uses' => 'EmbedController@embedByCities']
);
Route::get(
    'services/embed/cc/cities/{ids}/{limit?}',
    ['as' => 'embed.table-cc', 'uses' => 'EmbedController@embedCCByCities']
);
Route::get(
    'user/embed/vacations/{user}',
    ['as' => 'embed.user.vacations', 'uses' => 'EmbedController@embedUserVacations']
);

Route::get(
    'services/embed/{report}',
    ['as' => 'embed.report', 'uses' => 'EmbedController@embedReport']
);


Route::get('/days/add/{year}/{month}', ['uses' => 'DayController@add'])->name('days.add');
Route::get('/services/add/{date}/{city}', ['uses' => 'ServiceController@add'])->name('services.add');
//Route::get('/calendar/{year?}/{month?}', ['uses' => 'CalendarController@month'])->name('calendar');


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

Route::get('download/{storage}/{code}/{prettyName?}', ['as' => 'download', 'uses' => 'DownloadController@download']);
Route::get('attachment/{attachment}', 'DownloadController@attachment')->name('attachment');
Route::get('files/{path}/{prettyName?}', 'DownloadController@storage')->name('storage');
Route::get('image/{path}/{prettyName?}', 'DownloadController@image')->name('image');


// RITES (Kasualien)
Route::resource('baptisms', 'BaptismController')->middleware('auth');
Route::get('/baptism/add/{service}', ['as' => 'baptism.add', 'uses' => 'BaptismController@create'])->middleware('auth');
Route::get('/baptism/destroy/{baptism}', ['as' => 'baptism.destroy', 'uses' => 'BaptismController@destroy']);
Route::get(
    '/baptism/{baptism}/appointment/ical',
    ['as' => 'baptism.appointment.ical', 'uses' => 'BaptismController@appointmentIcal']
);
Route::post('/baptism/done/{baptism}', ['as' => 'baptism.done', 'uses' => 'BaptismController@done']);


Route::resource('funerals', 'FuneralController')->middleware('auth');
Route::get('/funeral/add/{service}', ['as' => 'funeral.add', 'uses' => 'FuneralController@create'])->middleware('auth');
Route::get('/funeral/destroy/{funeral}', ['as' => 'funeral.destroy', 'uses' => 'FuneralController@destroy']);
Route::get('/funeral/{funeral}/Formular KRA.pdf', ['as' => 'funeral.form', 'uses' => 'FuneralController@pdfForm']);
Route::get('/funeral/wizard', ['as' => 'funerals.wizard', 'uses' => 'FuneralController@wizardStep1']);
Route::post('/funeral/wizard/step2', ['as' => 'funerals.wizard.step2', 'uses' => 'FuneralController@wizardStep2']);
Route::post('/funeral/wizard/step3', ['as' => 'funerals.wizard.step3', 'uses' => 'FuneralController@wizardStep3']);
Route::post('/funeral/done/{funeral}', ['as' => 'funeral.done', 'uses' => 'FuneralController@done']);
Route::get(
    '/funeral/{funeral}/appointment/ical',
    ['as' => 'funeral.appointment.ical', 'uses' => 'FuneralController@appointmentIcal']
);

Route::get('/wedding/wizard', ['as' => 'weddings.wizard', 'uses' => 'WeddingController@wizardStep1']);
Route::post('/wedding/wizard/step2', ['as' => 'weddings.wizard.step2', 'uses' => 'WeddingController@wizardStep2']);
Route::post('/wedding/wizard/step3', ['as' => 'weddings.wizard.step3', 'uses' => 'WeddingController@wizardStep3']);
Route::post('/wedding/done/{wedding}', ['as' => 'wedding.done', 'uses' => 'WeddingController@done']);


Route::resource('weddings', 'WeddingController')->middleware('auth');
Route::get('/wedding/add/{service}', ['as' => 'wedding.add', 'uses' => 'WeddingController@create'])->middleware('auth');
Route::get('/wedding/destroy/{wedding}', ['as' => 'wedding.destroy', 'uses' => 'WeddingController@destroy']);

// Home routes
Route::get('/home', ['as' => 'home', 'uses' => 'HomeController@index']);
Route::get(    '/', ['as' => 'root', 'uses' => 'HomeController@root']);

Route::get('/password/change', 'HomeController@showChangePassword')->name('password.edit');
Route::post('/password/change', 'HomeController@changePassword')->name('password.change');

Auth::routes();
Route::get('/logout', 'UserController@logout')->name('logout');

Route::get('/ical/private/{name}/{token}', ['uses' => 'ICalController@private'])->name('ical.private');
Route::get('/ical/gemeinden/{locationIds}/{token}', ['uses' => 'ICalController@byLocation'])->name('ical.byLocation');
Route::get('/ical/urlaub/{user}/{token}', ['uses' => 'ICalController@absences'])->name('ical.absences');


Route::get('/ical/connect', ['uses' => 'ICalController@connect'])->name('ical.connect');
Route::get('/ical/setup/{key}', ['uses' => 'ICalController@setup'])->name('ical.setup');
Route::get('/ical/link/{key}', ['uses' => 'ICalController@link'])->name('ical.link');
Route::post('/ical/link/{key}', ['uses' => 'ICalController@link'])->name('ical.link');
Route::get('/ical/export/{user}/{token}/{key}', ['uses' => 'ICalController@export'])->name('ical.export');


Route::get('/whatsnew', ['as' => 'whatsnew', 'uses' => 'HomeController@whatsnew'])->middleware('auth');


Route::get('/kinderkirche/{city}/pdf', ['as' => 'cc-public-pdf', 'uses' => 'PublicController@childrensChurch']);
Route::get('/kinderkirche/{city}', ['as' => 'cc-public', 'uses' => 'PublicController@childrensChurch']);

Route::post('/showLimitedColumns/{switch}', 'CalendarController@showLimitedColumns')
    ->middleware('auth')
    ->name('showLimitedColumns');
Route::get('/showLimitedColumns/{switch}', 'CalendarController@showLimitedColumns')
    ->middleware('auth')
    ->name('showLimitedColumns');

// last service updated (timestamp)
Route::get('/lastUpdate', ['as' => 'lastUpdate', 'uses' => 'ServiceController@lastUpdate']);


// counters
Route::get('/counter/{counter}', ['as' => 'counter', 'uses' => 'HomeController@counters']);

// revisions
Route::resource('revisions', 'RevisionController');
Route::post('revisions', 'RevisionController@index')->name('revisions.index.post');
Route::post('revisions/revert', 'RevisionController@revert')->name('revisions.revert');


// new features
Route::get('features', 'HomeController@features');

// api token
Route::get('apiToken', 'ApiTokenController@update')->name('apitoken');

// approvals
Route::resource('approvals', 'ApprovalController');

// podcast
Route::get('/podcasts/{cityName}.xml', 'PodcastController@podcast')->name('podcast');

// google api
Route::get('/google/auth/city', 'GoogleApiController@auth')->name('google-auth');
Route::get('/google/youtube/createServiceBroadcast/{service}', 'GoogleApiController@createBroadcast')
    ->name('broadcast.create');
Route::get('/google/youtube/serviceBroadcast/{service}/refresh', 'GoogleApiController@refreshBroadcast')
    ->name('broadcast.refresh');
Route::delete('/google/youtube/serviceBroadcast/{service}', 'GoogleApiController@deleteBroadcast')
    ->name('broadcast.delete');

// youtube live chat
Route::get('/livechat/{service}', 'LiveChatController@liveChat')->name('service.livechat');
Route::get('/livechat/{service}/messages', 'LiveChatController@liveChatAjax')->name('service.livechat.ajax');
Route::post('/livechat/message/{service}', 'LiveChatController@liveChatPostMessage')->name(
    'service.livechat.message.post'
);

//about
Route::get('/about', 'HomeController@about')->name('about');

//checkIn
Route::get('/checkin/{location}', 'CheckInController@create')->name('checkin.create');
Route::post('/checkin/{service}', 'CheckInController@store')->name('checkin.store');
Route::get('/checkin/{location}/qr', 'CheckInController@qr')->name('checkin.qr');


// inertia testing
Route::get('/calendar/{date?}/{month?}', 'CalController@index')->name('calendar');

// liturgy editor
Route::get('/services/{service}/liturgy', 'LiturgyEditorController@editor')->name('services.liturgy.editor');
Route::get('/services/{service}/liturgy/download/{key}', 'LiturgyEditorController@download')->name('services.liturgy.download');
Route::post('/services/{service}/liturgy', 'LiturgyEditorController@save')->name('services.liturgy.save');
Route::get('/services/{service}/liturgy/sources', 'LiturgyEditorController@sources')->name('services.liturgy.sources');
Route::post('/services/{service}/liturgy/import/{source}', 'LiturgyEditorController@import')->name('services.liturgy.import');

// liturgy blocks
Route::post('/liturgy/{service}/block', 'LiturgyBlockController@store')->name('liturgy.block.store');
Route::post('/liturgy/{service}/block/{block}', 'LiturgyBlockController@sync')->name('liturgy.block.sync');
Route::patch('/liturgy/{service}/block/{block}', 'LiturgyBlockController@update')->name('liturgy.block.update');
Route::delete('/liturgy/{service}/block/{block}', 'LiturgyBlockController@destroy')->name('liturgy.block.destroy');

// liturgy items
Route::post('/liturgy/{service}/block/{block}/item', 'LiturgyItemController@store')->name('liturgy.item.store');
Route::patch('/liturgy/{service}/block/{block}/item/{item}', 'LiturgyItemController@update')->name('liturgy.item.update');
Route::delete('/liturgy/{service}/block/{block}/item/{item}', 'LiturgyItemController@destroy')->name('liturgy.item.destroy');
Route::post('/liturgy/{service}/block/{block}/item/{item}/roster', 'LiturgyItemController@roster')->name('liturgy.item.roster');

// liturgical texts
Route::get('/liturgy/texts', 'LiturgicalTextsController@index')->name('liturgy.text.index');
Route::get('/liturgy/texts/list', 'LiturgicalTextsController@list')->name('liturgy.text.list');
Route::post('/liturgy/texts', 'LiturgicalTextsController@store')->name('liturgy.text.store');
Route::post('/liturgy/texts/import', 'LiturgicalTextsController@import')->name('liturgy.text.import');
Route::patch('/liturgy/texts/{text}', 'LiturgicalTextsController@update')->name('liturgy.text.update');

// songs
Route::get('/liturgy/songs', 'SongController@index')->name('liturgy.song.index');
Route::get('/liturgy/songs/songbooks', 'SongController@songbooks')->name('liturgy.song.songbooks');
Route::post('/liturgy/songs', 'SongController@store')->name('liturgy.song.store');
Route::patch('/liturgy/songs/{song}', 'SongController@update')->name('liturgy.song.update');

// psalms
Route::get('/liturgy/psalms', 'PsalmController@index')->name('liturgy.psalm.index');
Route::post('/liturgy/psalms', 'PsalmController@store')->name('liturgy.psalm.store');
Route::patch('/liturgy/psalms/{psalm}', 'PsalmController@update')->name('liturgy.psalm.update');

// agenda
\App\Http\Controllers\AgendaController::defaultRoutes('liturgy');


// ministry request
Route::get('/anfrage/{ministry}/{user}/{services}/{sender?}', 'PublicController@ministryRequest')
    ->name('ministry.request');
Route::post('/anfrage/{ministry}/{user}/{sender?}', 'PublicController@ministryRequestFilled')
    ->name('ministry.request.fill');

// settings
Route::post('/setting/{user}/{key}', 'SettingsController@set')->name('setting.set');


