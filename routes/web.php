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

use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BaptismController;
use App\Http\Controllers\BibleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalController;
use App\Http\Controllers\CalendarConnectionController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DayController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\EmbedController;
use App\Http\Controllers\FuneralController;
use App\Http\Controllers\GoogleApiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ICalController;
use App\Http\Controllers\InputController;
use App\Http\Controllers\LiturgicalTextsController;
use App\Http\Controllers\LiturgyBlockController;
use App\Http\Controllers\LiturgyEditorController;
use App\Http\Controllers\LiturgyItemController;
use App\Http\Controllers\LiveChatController;
use App\Http\Controllers\ManualController;
use App\Http\Controllers\PatchController;
use App\Http\Controllers\PodcastController;
use App\Http\Controllers\PsalmController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\RevisionController;
use App\Http\Controllers\SermonController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\StreamingTroubleshooterController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WeddingController;

Auth::routes(['logout' => false, 'register' => false]);

Route::resource('absences', 'AbsenceController')->except(['index', 'create'])->middleware('auth');
Route::get('planner/users', [AbsenceController::class, 'users'])->name('planner.users');
Route::get('planner/days/{date}/{user}', [AbsenceController::class, 'days'])->name('planner.days');
Route::get('absences/{year?}/{month?}', [AbsenceController::class, 'index'])->name('absences.index');
Route::get('absences/create/{year}/{month}/{user}/{day?}', [AbsenceController::class, 'create'])->name('absences.create');
Route::get('absence/{absence}/approve', [AbsenceController::class, 'approve'])->name('absence.approve');
Route::get('absence/{absence}/reject', [AbsenceController::class, 'approve'])->name('absence.reject');

// agenda
AgendaController::defaultRoutes('liturgy');

Route::get('apiToken', [ApiTokenController::class, 'update'])->name('apitoken');

Route::resource('approvals', 'ApprovalController');

Route::resource('baptisms', 'BaptismController');
Route::get('/baptism/add/{service}', [BaptismController::class, 'create'])->name('baptism.add');
Route::get('/baptism/destroy/{baptism}', [BaptismController::class, 'destroy'])->name('baptism.destroy');
Route::get('/baptism/{baptism}/appointment/ical', [BaptismController::class, 'appointmentIcal'])->name('baptism.appointment.ical');
Route::post('/baptism/done/{baptism}', [BaptismController::class, 'done'])->name('baptism.done');
Route::post('baptisms/{baptism}/attachment', [BaptismController::class, 'attach'])->name('baptism.attach');
Route::delete('baptisms/{baptism}/attachment/{attachment}', [BaptismController::class, 'detach'])->name('baptism.detach');

// bible texts
Route::get('/bible/text/{reference}/{version?}', [BibleController::class, 'text'])->name('bible.text');

Route::resource('booking', 'BookingController');
Route::post('booking/{booking}/pin', [BookingController::class, 'pin'])->name('booking.pin');
Route::get('services/{service}/bookings', [BookingController::class, 'index'])->name('service.bookings');
Route::get('seatfinder/{service}/{number?}', [BookingController::class, 'findSeat'])->name('seatfinder');
Route::get('services/{service}/bookingList', [BookingController::class, 'finalize'])->name('booking.finalize');

// inertia calendar
Route::get('/calendar/{date?}/{month?}', [CalController::class, 'index'])->name('calendar');
Route::get('/cal/city/{date}/{city}', [CalController::class, 'city'])->name('cal.city');
Route::get('/cal/day/{day}/{city}', [CalController::class, 'day'])->name('cal.day');
Route::get('/calendar-day/{day}/{city}', [CalController::class, 'singleDay'])->name('calendar.day');

Route::resource('calendarConnection', 'CalendarConnectionController');
Route::post('exchangeCalendars', [CalendarConnectionController::class, 'exchangeCalendars'])->name('calendarConnection.exchangeCalendars');

Route::match(['GET', 'POST'], '/showLimitedColumns/{switch}', [CalendarController::class, 'showLimitedColumns'])->name('showLimitedColumns');

//checkIn
Route::get('/checkin/{location}', [CheckInController::class, 'create'])->name('checkin.create');
Route::post('/checkin/{service}', [CheckInController::class, 'store'])->name('checkin.store');
Route::get('/checkin/{location}/qr', [CheckInController::class, 'qr'])->name('checkin.qr');

Route::resource('cities', 'CityController')->middleware('auth');
Route::get('qr/{city}', [CityController::class, 'qr'])->name('qr');

Route::resource('comments', 'CommentController')->middleware('auth');

Route::resource('days', 'DayController')->middleware('auth');
Route::get('days/list/{city}', [DayController::class, 'list'])->name('days.list');
Route::get('/days/add/{year}/{month}', [DayController::class, 'add'])->name('days.add');

Route::get('download/{storage}/{code}/{prettyName?}', [DownloadController::class, 'download'])->name('download');
Route::get('attachment/{attachment}', [DownloadController::class, 'attachment'])->name('attachment');
Route::get('files/{path}/{prettyName?}', [DownloadController::class, 'storage'])->name('storage');
Route::get('image/{path}/{prettyName?}', [DownloadController::class, 'image'])->name('image');

Route::get('services/embed/locations/{ids}/{limit?}', [EmbedController::class, 'embedByLocations'])->name('embed.table-locations');
Route::get('services/embed/baptismalServices/{ids}/{limit?}/{maxBaptisms?}', [EmbedController::class, 'embedByBaptismalServices'])->name('embed.table-baptismalservices');
Route::get('services/embed/cities/{ids}/{limit?}', [EmbedController::class, 'embedByCities'])->name('embed.table-cities');
Route::get('services/embed/cc/cities/{ids}/{limit?}', [EmbedController::class, 'embedCCByCities'])->name('embed.table-cc');
Route::get('user/embed/vacations/{user}', [EmbedController::class, 'embedUserVacations'])->name('embed.user.vacations');
Route::get('services/embed/{report}', [EmbedController::class, 'embedReport'])->name('embed.report');

Route::get('funerals/create/{service}', [FuneralController::class, 'create'])->name('funerals.create');
Route::get('funerals/{funeral}', [FuneralController::class, 'edit'])->name('funerals.edit');
Route::patch('funerals/{funeral}', [FuneralController::class, 'update'])->name('funerals.update');
Route::delete('funerals/{funeral}', [FuneralController::class, 'destroy'])->name('funerals.destroy');
Route::post('funerals/{funeral}/attachment', [FuneralController::class, 'attach'])->name('funeral.attach');
Route::delete('funerals/{funeral}/attachment/{attachment}', [FuneralController::class, 'detach'])->name('funeral.detach');
Route::get('/funeral/add/{service}', [FuneralController::class, 'create'])->name('funeral.add');
Route::get('/funeral/destroy/{funeral}', [FuneralController::class, 'destroy'])->name('funeral.destroy');
Route::get('/funeral/{funeral}/Formular KRA.pdf', [FuneralController::class, 'pdfForm'])->name('funeral.form');
Route::get('/funeral/wizard', [FuneralController::class, 'wizard'])->name('funerals.wizard');
Route::post('/funeral/wizard', [FuneralController::class, 'wizardSave'])->name('funerals.wizard.save');
Route::get('/funeral/{funeral}/appointment/ical', [FuneralController::class, 'appointmentIcal'])->name('funeral.appointment.ical');

// google api
Route::get('/google/auth/city', [GoogleApiController::class, 'auth'])->name('google-auth');
Route::get('/google/youtube/createServiceBroadcast/{service}', [GoogleApiController::class, 'createBroadcast'])->name('broadcast.create');
Route::get('/google/youtube/serviceBroadcast/{service}/refresh', [GoogleApiController::class, 'refreshBroadcast'])->name('broadcast.refresh');
Route::delete('/google/youtube/serviceBroadcast/{service}', [GoogleApiController::class, 'deleteBroadcast'])->name('broadcast.delete');

// Home routes
Route::get('/', [HomeController::class, 'root'])->name('root');
Route::get('/home/{activeTab?}', [HomeController::class, 'index'])->name('home');
Route::get('/tabs/{tabIndex}', [HomeController::class, 'tab'])->name('tab');
Route::get('/password/change', [HomeController::class, 'showChangePassword'])->name('password.edit');
Route::post('/password/change', [HomeController::class, 'changePassword'])->name('password.change');
Route::get('/counter/{counter}', [HomeController::class, 'counters'])->name('counter');
Route::get('/whatsnew', [HomeController::class, 'whatsnew'])->name('whatsnew')->middleware('auth');
Route::get('features', [HomeController::class, 'features']);
Route::get('/about', [HomeController::class, 'about'])->name('about');

Route::get('/ical/private/{name}/{token}', [ICalController::class, 'private'])->name('ical.private');
Route::get('/ical/gemeinden/{locationIds}/{token}', [ICalController::class, 'byLocation'])->name('ical.byLocation');
Route::get('/ical/urlaub/{user}/{token}', [ICalController::class, 'absences'])->name('ical.absences');
Route::get('/ical/connect', [ICalController::class, 'connect'])->name('ical.connect');
Route::get('/ical/setup/{key}', [ICalController::class, 'setup'])->name('ical.setup');
Route::match(['GET', 'POST'], '/ical/link/{key}', [ICalController::class, 'link'])->name('ical.link');
Route::get('/ical/export/{user}/{token}/{key}', [ICalController::class, 'export'])->name('ical.export');

Route::get('/input/{input}', [InputController::class, 'setup'])->name('inputs.setup');
Route::post('/input/collect/{input}', [InputController::class, 'input'])->name('inputs.input');
Route::post('/input/save/{input}', [InputController::class, 'save'])->name('inputs.save');

// liturgy blocks
Route::post('/liturgy/{service}/block', [LiturgyBlockController::class, 'store'])->name('liturgy.block.store');
Route::post('/liturgy/{service}/block/{block}', [LiturgyBlockController::class, 'sync'])->name('liturgy.block.sync');
Route::patch('/liturgy/{service}/block/{block}', [LiturgyBlockController::class, 'update'])->name('liturgy.block.update');
Route::delete('/liturgy/{service}/block/{block}', [LiturgyBlockController::class, 'destroy'])->name('liturgy.block.destroy');

// liturgical texts
Route::get('/liturgy/texts', [LiturgicalTextsController::class, 'index'])->name('liturgy.text.index');
Route::get('/liturgy/texts/list', [LiturgicalTextsController::class, 'list'])->name('liturgy.text.list');
Route::post('/liturgy/texts', [LiturgicalTextsController::class, 'store'])->name('liturgy.text.store');
Route::post('/liturgy/texts/import', [LiturgicalTextsController::class, 'import'])->name('liturgy.text.import');
Route::patch('/liturgy/texts/{text}', [LiturgicalTextsController::class, 'update'])->name('liturgy.text.update');

//manual
Route::get('/manual/{routeName}', [ManualController::class, 'page'])->name('manual');

// liturgy editor
Route::get('/services/{service}/liturgy', [LiturgyEditorController::class, 'editor'])->name('services.liturgy.editor');
Route::get('/services/{service}/liturgy/configure/{key}', [LiturgyEditorController::class, 'configureLiturgySheet']
)->name('services.liturgy.configure');
Route::get('/services/{service}/liturgy/download/{key}', [LiturgyEditorController::class, 'download'])->name('services.liturgy.download');
Route::post('/services/{service}/liturgy/download/{key}', [LiturgyEditorController::class, 'download'])->name('services.liturgy.download.post');
Route::post('/services/{service}/liturgy', [LiturgyEditorController::class, 'save'])->name('services.liturgy.save');
Route::get('/services/{service}/liturgy/sources', [LiturgyEditorController::class, 'sources'])->name('services.liturgy.sources');
Route::get('/services/{service}/liturgy/sermons', [LiturgyEditorController::class, 'sermons'])->name('services.liturgy.sermons');
Route::post('/services/{service}/liturgy/import/{source}', [LiturgyEditorController::class, 'import'])->name('services.liturgy.import');

// liturgy items
Route::post('/liturgy/{service}/block/{block}/item', [LiturgyItemController::class, 'store'])->name('liturgy.item.store');
Route::patch('/liturgy/{service}/block/{block}/item/{item}', [LiturgyItemController::class, 'update'])->name('liturgy.item.update');
Route::delete('/liturgy/{service}/block/{block}/item/{item}', [LiturgyItemController::class, 'destroy'])->name('liturgy.item.destroy');
Route::post('/liturgy/{service}/block/{block}/item/{item}/roster', [LiturgyItemController::class, 'roster'])->name('liturgy.item.roster');

// youtube live chat
Route::get('/livechat/{service}', [LiveChatController::class, 'liveChat'])->name('service.livechat');
Route::get('/livechat/{service}/messages', [LiveChatController::class, 'liveChatAjax'])->name('service.livechat.ajax');
Route::post('/livechat/message/{service}', [LiveChatController::class, 'liveChatPostMessage'])->name('service.livechat.message.post');

Route::resource('locations', 'LocationController')->middleware('auth');

// csrf token: keep alive
Route::get('/csrf-token', [LoginController::class, 'keepTokenAlive'])->name('csrf.keepalive');

Route::resource('parishes', 'ParishController')->middleware('auth');

// patch
Route::get('/patch/{patch}', [PatchController::class, 'patch']);

Route::get('/podcasts/{cityName}.xml', [PodcastController::class, 'podcast'])->name('podcast');

// psalms
Route::get('/liturgy/psalms', [PsalmController::class, 'index'])->name('liturgy.psalm.index');
Route::post('/liturgy/psalms', [PsalmController::class, 'store'])->name('liturgy.psalm.store');
Route::patch('/liturgy/psalms/{psalm}', [PsalmController::class, 'update'])->name('liturgy.psalm.update');

// children's church
Route::get('/kinderkirche/{city}/pdf', [PublicController::class, 'childrensChurch'])->name('cc-public-pdf');
Route::get('/kinderkirche/{city}', [PublicController::class, 'childrensChurch'])->name('cc-public');
// dimissorial
Route::get('/dimissoriale/{type}/{id}', [PublicController::class, 'showDimissorial'])->name('dimissorial.show');
Route::post('/dimissoriale/{type}/{id}', [PublicController::class, 'grantDimissorial'])->name('dimissorial.grant');
// ministry request
Route::get('/anfrage/{ministry}/{user}/{services}/{sender?}', [PublicController::class, 'ministryRequest'])->name('ministry.request');
Route::post('/anfrage/{ministry}/{user}/{sender?}', [PublicController::class, 'ministryRequestFilled'])->name('ministry.request.fill');
Route::get('/dienste/{cityName}/{ministry}', [PublicController::class, 'ministryPlan'])->name('ministry.plan');


Route::get('/reports', [ReportsController::class, 'list'])->name('reports.list');
Route::post('/reports/render/{report}', [ReportsController::class, 'render'])->name('reports.render');
Route::get('/reports/render/{report}', [ReportsController::class, 'render'])->name('reports.render.get');
Route::get('/report/{report}', [ReportsController::class, 'setup'])->name('reports.setup');
Route::get('/report/{report}/embed', [ReportsController::class, 'embed'])->name('report.embed')->middleware('cors');
Route::match(['GET', 'POST'], '/report/{report}/{step}', [ReportsController::class, 'step'])->name('report.step');

Route::resource('revisions', 'RevisionController');
Route::post('revisions', [RevisionController::class, 'index'])->name('revisions.index.post');
Route::post('revisions/revert', [RevisionController::class, 'revert'])->name('revisions.revert');

Route::resource('roles', 'RoleController')->middleware('auth');

Route::resource('seatingRow', 'SeatingRowController');
Route::resource('seatingSection', 'SeatingSectionController');

// sermons
Route::get('/services/{service}/sermon', [SermonController::class, 'editorByService'])->name('services.sermon.editor');
Route::post('/services/{service}/sermon', [SermonController::class, 'store'])->name('services.sermon.store');
Route::delete('/services/{service}/sermon', [SermonController::class, 'uncouple'])->name('services.sermon.uncouple');
Route::get('/sermons/{sermon}', [SermonController::class, 'editor'])->name('sermon.editor');
Route::patch('/sermons/{sermon}', [SermonController::class, 'update'])->name('sermon.update');
Route::post('/sermons/{model}/image', [SermonController::class, 'attachImage'])->name('sermon.image.attach');
Route::delete('/sermons/{model}/image', [SermonController::class, 'detachImage'])->name('sermon.image.detach');

Route::patch('/services/{service}', [ServiceController::class, 'update2'])->name('services.update');
Route::resource('services', 'ServiceController')->except(['edit'])->middleware('auth');
Route::get('services/{service}/edit/{tab?}', ['as' => 'services.edit', 'uses' => 'ServiceController@edit']);
Route::get('services/{service}/ical', [ServiceController::class, 'ical'])->name('services.ical');
Route::get('/service/{service}/songsheet', [ServiceController::class, 'songsheet'])->name('service.songsheet');
Route::get('/services/{city}/streaming/next', [PublicController::class, 'nextStream'])->name('service.nextstream');
Route::get('services/{service}', [ServiceController::class, 'editor'])->name('services.editor');
Route::post('services/{service}/attachment', [ServiceController::class, 'attach'])->name('service.attach');
Route::delete('services/{service}/attachment/{attachment}', [ServiceController::class, 'detach'])->name('service.detach');
Route::get('/services/add/{date}/{city}', [ServiceController::class, 'add'])->name('services.add');
Route::get('/lastUpdate', [ServiceController::class, 'lastUpdate'])->name('lastUpdate');

// settings
Route::post('/setting/{user}/{key}', [SettingsController::class, 'set'])->name('setting.set');

// songs
Route::get('/liturgy/songs', [SongController::class, 'index'])->name('liturgy.song.index');
Route::get('/liturgy/songs/songbooks', [SongController::class, 'songbooks'])->name('liturgy.song.songbooks');
Route::post('/liturgy/songs', [SongController::class, 'store'])->name('liturgy.song.store');
Route::patch('/liturgy/songs/{song}', [SongController::class, 'update'])->name('liturgy.song.update');

// streaming troubleshooter
Route::get('/streaming/{city}', [StreamingTroubleshooterController::class, 'index'])->name('streaming.troubleshooter');
Route::post('/streaming/activateService/{service}', [StreamingTroubleshooterController::class, 'activateService'])->name('streaming.troubleshooter.activateService');
Route::post('/streaming/resetService/{service}', [StreamingTroubleshooterController::class, 'resetService'])->name('streaming.troubleshooter.resetService');
Route::post('/streaming/{city}/activateBroadcast/{broadcast}', [StreamingTroubleshooterController::class, 'activateBroadcast'])->name('streaming.troubleshooter.activateBroadcast');

Route::resource('tags', 'TagController')->middleware('auth');

// api tokens (Sanctum)
Route::get('/user/tokens', [TokenController::class, 'index'])->name('tokens.index');
Route::post('/user/tokens/create', [TokenController::class, 'create'])->name('tokens.create');
Route::delete('/user/tokens/{token}', [TokenController::class, 'destroy'])->name('tokens.destroy');

Route::resource('users', 'UserController');
Route::get('user/profile', [UserController::class, 'profile'])->name('user.profile');
Route::patch('user/profile', [UserController::class, 'profileSave'])->name('user.profile.save');
Route::post('user/{user}/join', [UserController::class, 'join'])->name('user.join');
Route::post('users/join', [UserController::class, 'doJoin'])->name('users.join');
Route::get('user/{user}/services', [UserController::class, 'services'])->name('user.services');
Route::get('user/switch/{user}', [UserController::class, 'switch'])->name('user.switch');
Route::post('users/add', [UserController::class, 'add'])->name('users.add');
Route::get('/logout', [UserController::class, 'logout'])->name('logout');

Route::resource('weddings', 'WeddingController')->middleware('auth');
Route::get('/wedding/wizard', [WeddingController::class, 'wizardStep1'])->name('weddings.wizard');
Route::post('/wedding/wizard/step2', [WeddingController::class, 'wizardStep2'])->name('weddings.wizard.step2');
Route::post('/wedding/wizard/step3', [WeddingController::class, 'wizardStep3'])->name('weddings.wizard.step3');
Route::post('/wedding/done/{wedding}', [WeddingController::class, 'done'])->name('wedding.done');
Route::post('weddings/{wedding}/attachment', [WeddingController::class, 'attach'])->name('wedding.attach');
Route::delete('weddings/{wedding}/attachment/{attachment}', [WeddingController::class, 'detach'])->name('wedding.detach');
Route::get('/wedding/add/{service}', [WeddingController::class, 'create'])->name('wedding.add')->middleware('auth');
Route::get('/wedding/destroy/{wedding}', [WeddingController::class, 'destroy'])->name('wedding.destroy');

