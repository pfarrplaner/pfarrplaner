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

use App\Booking;
use App\City;
use App\Location;
use App\SeatingRow;
use App\Service;
use DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator;


Breadcrumbs::for(
    'absences.index',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('home');
        $trail->push('Urlaub', route('absences.index'));
    }
);

Breadcrumbs::for(
    'absences.create',
    function (BreadcrumbsGenerator $trail, $year, $month, $user) {
        $trail->parent('absences.index');
        if (count($user->approvers) > 0) {
            $trail->push('Urlaubsantrag stellen', route('absences.create', compact('year', 'month', 'user')));
        } else {
            $trail->push('Urlaub eintragen', route('absences.create', compact('year', 'month', 'user')));
        }
    }
);

Breadcrumbs::for(
    'absences.edit',
    function (BreadcrumbsGenerator $trail, $absence) {
        if (!is_numeric($absence)) {
            $absence = $absence->id;
        }
        $trail->parent('absences.index');
        $trail->push('Urlaub bearbeiten', route('absences.edit', $absence));
    }
);

Breadcrumbs::for(
    'about',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('home');
        $trail->push('Über Pfarrplaner', route('about'));
    }
);


Breadcrumbs::for(
    'admin',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('home');
        $trail->push('Administration');
    }
);

Breadcrumbs::for(
    'apitoken',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('home');
        $trail->push('API Token', route('apitoken'));
    }
);

Breadcrumbs::for('bookings.create', function (BreadcrumbsGenerator $trail){
    $trail->push('Neue Reservierung');
});

Breadcrumbs::for('seatfinder', function (BreadcrumbsGenerator $trail){
    $trail->push('Neue Reservierung');
});

Breadcrumbs::for(
    'approvals.index',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('absences.index');
        $trail->push('Urlaubsanträge', route('approvals.index'));
    }
);

Breadcrumbs::for('checkin.create', function (BreadcrumbsGenerator $trail, $location){
    if (is_numeric($location)) $location = Location::find($location);
    $trail->push($location->name);
    $trail->push('Check-In', route('checkin.create', $location));
});

Breadcrumbs::for('checkin.store', function (BreadcrumbsGenerator $trail, $service){
    if (is_numeric($service)) $service = Service::find($service);
    $trail->push($service->location->name);
    $trail->push('Check-In', route('checkin.create', $service->location));
});

Breadcrumbs::for(
    'days.edit',
    function (BreadcrumbsGenerator $trail, $day) {
        $trail->parent('calendar');
        $trail->push('Tag bearbeiten', route('days.edit', $day));
    }
);

Breadcrumbs::for(
    'days.create',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('calendar');
        $trail->push('Tag hinzufügen', route('days.create'));
    }
);

Breadcrumbs::for(
    'days.add',
    function (BreadcrumbsGenerator $trail, $year, $month) {
        $trail->parent('calendar');
        $trail->push('Tage im Kalender', route('days.add', compact('year', 'month')));
    }
);

Breadcrumbs::for(
    'baptism.add',
    function (BreadcrumbsGenerator $trail, $service) {
        $trail->parent('services.edit', $service);
        $trail->push('Neue Taufe', route('baptism.add', $service));
    }
);

Breadcrumbs::for(
    'baptisms.create',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('home');
        $trail->push('Neue Taufe', route('baptisms.create'));
    }
);

Breadcrumbs::for(
    'baptisms.edit',
    function (BreadcrumbsGenerator $trail, $baptism) {
        if (!is_object($baptism)) {
            $baptism = Baptism::find($baptism);
        }
        if (is_object($baptism->service)) {
            $trail->parent('services.edit', $baptism->service->id);
        } else {
            $trail->parent('home');
            $trail->push('Taufanfragen');
        }
        $trail->push('Taufe von ' . $baptism->candidate_name, route('baptisms.edit', $baptism));
    }
);

Breadcrumbs::for('service.bookings', function (BreadcrumbsGenerator $trail, Service $service){
    $trail->parent('services.edit', $service);
    $trail->push('Anmeldungen', route('service.bookings', $service));
});

Breadcrumbs::for('booking.edit', function (BreadcrumbsGenerator $trail, Booking $booking){
    if (!is_object($booking)) $booking = \App\Booking::findOrFail($booking);
    $trail->parent('service.bookings', $booking->service);
    $trail->push('#'.$booking->id, route('booking.edit', $booking));
});



Breadcrumbs::for(
    'calendar',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('home');
        $trail->push('Gottesdienste', route('calendar'));
    }
);

Breadcrumbs::for(
    'calendarConnection.create',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('user.profile');
        $trail->push('Kalender verbinden', route('calendarConnection.create'));
    }
);

Breadcrumbs::for(
    'cc-public',
    function (BreadcrumbsGenerator $trail, $city) {
        $trail->parent('home');
        $trail->push('Kinderkirche', route('cc-public', $city));
    }
);


Breadcrumbs::for(
    'funeral.add',
    function (BreadcrumbsGenerator $trail, $service) {
        $trail->parent('services.edit', $service);
        $trail->push('Neue Bestattung', route('funeral.add', $service));
    }
);

Breadcrumbs::for(
    'funerals.edit',
    function (BreadcrumbsGenerator $trail, $funeral) {
        $trail->parent('services.edit', $funeral->service->id);
        $trail->push('Beerdigung von ' . $funeral->buried_name, route('funerals.edit', $funeral));
    }
);

Breadcrumbs::for(
    'funerals.wizard',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('home');
        $trail->push('Neue Bestattung', route('funerals.wizard'));
    }
);

Breadcrumbs::for(
    'funerals.wizard.step2',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('funerals.wizard');
        $trail->push('Ortsangaben', route('funerals.wizard.step2'));
    }
);


Breadcrumbs::for(
    'locations.index',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('admin');
        $trail->push('Kirchen und Gottesdienstorte', route('locations.index'));
    }
);

Breadcrumbs::for(
    'location.create',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('locations.index');
        $trail->push('Neuer Ort', route('location.create'));
    }
);

Breadcrumbs::for(
    'location.edit',
    function (BreadcrumbsGenerator $trail, $location) {
        $location = \App\Location::find($location);
        $trail->parent('locations.index');
        $trail->push($location->name, route('location.edit', $location));
    }
);

Breadcrumbs::for(
    'home',
    function (BreadcrumbsGenerator $trail) {
        $trail->push('<i class="fa fa-home" title="Zurück zur Übersicht"></i>', route('home'));
    }
);


Breadcrumbs::for(
    'ical.connect',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('home');
        $trail->push('Outlook-Export', route('ical.connect'));
    }
);


Breadcrumbs::for(
    'ical.setup',
    function (BreadcrumbsGenerator $trail, $key) {
        $class = 'App\\CalendarLinks\\' . ucfirst($key) . 'CalendarLink';
        if (class_exists($class)) {
            $object = new $class();
        }
        $trail->parent('ical.connect');
        $trail->push($object->getTitle());
        $trail->push('Einrichten');
    }
);

Breadcrumbs::for(
    'ical.link',
    function (BreadcrumbsGenerator $trail, $key) {
        $class = 'App\\CalendarLinks\\' . ucfirst($key) . 'CalendarLink';
        if (class_exists($class)) {
            $object = new $class();
        }
        $trail->parent('ical.connect');
        $trail->push($object->getTitle());
        $trail->push('Verlinken');
    }
);

Breadcrumbs::for(
    'inputs.setup',
    function (BreadcrumbsGenerator $trail, $input) {
        /** @var \App\Inputs\AbstractInput $input */
        $inputObject = \App\Inputs\Inputs::get($input);
        $trail->parent('home');
        $trail->push('Sammeleingaben');
        $trail->push($inputObject->title, route('inputs.setup', $input));
    }
);

Breadcrumbs::for(
    'inputs.input',
    function (BreadcrumbsGenerator $trail, $input) {
        $trail->parent('inputs.setup', $input);
        $trail->push('Eingabe', route('inputs.input', $input));
    }
);

Breadcrumbs::for(
    'cities.index',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('admin');
        $trail->push('Kirchengemeinden', route('cities.index'));
    }
);

Breadcrumbs::for(
    'cities.create',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('cities.index');
        $trail->push('Neue Kirchengemeinde', route('cities.create'));
    }
);

Breadcrumbs::for(
    'city.edit',
    function (BreadcrumbsGenerator $trail, $city) {
        if (is_numeric($city)) $city = \App\City::find($city);
        $trail->parent('cities.index');
        $trail->push($city->name, route('city.edit', $city));
    }
);

Breadcrumbs::for(
    'login',
    function (BreadcrumbsGenerator $trail) {
        $trail->push('Anmelden');
    }
);

Breadcrumbs::for(
    'parishes.index',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('admin');
        $trail->push('Pfarrämter', route('parishes.index'));
    }
);

Breadcrumbs::for(
    'parishes.create',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('parishes.index');
        $trail->push('Neues Pfarramt', route('parishes.create'));
    }
);

Breadcrumbs::for(
    'parishes.edit',
    function (BreadcrumbsGenerator $trail, \App\Parish $parish) {
        $trail->parent('parishes.index');
        $trail->push($parish->name, route('parishes.edit', $parish));
    }
);

Breadcrumbs::for(
    'password.edit',
    function (BreadcrumbsGenerator $trail) {
        $trail->push('Passwort ändern');
    }
);

Breadcrumbs::for(
    'password.request',
    function (BreadcrumbsGenerator $trail) {
        $trail->push('Passwort vergessen');
    }
);

Breadcrumbs::for(
    'password.reset',
    function (BreadcrumbsGenerator $trail) {
        $trail->push('Passwort zurücksetzen');
    }
);

Breadcrumbs::for(
    'qr',
    function (BreadcrumbsGenerator  $trail, $city) {
        $city = City::where('name', 'like', '%' . $city . '%')->first();
        $trail->parent('home');
        $trail->push('QR-Codes', route('qr', $city));
    }
);

Breadcrumbs::for(
    'reports.list',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('home');
        $trail->push('Ausgabeformate', route('reports.list'));
    }
);

Breadcrumbs::for(
    'reports.setup',
    function (BreadcrumbsGenerator $trail, $report) {
        $reportClass = 'App\\Reports\\' . ucfirst($report) . 'Report';
        if (class_exists($reportClass)) {
            /** @var AbstractReport $report */
            $report = new $reportClass();
        }
        $trail->parent('reports.list');
        $trail->push($report->title, route('reports.setup', $report->getKey()));
    }
);

Breadcrumbs::for(
    'report.step',
    function (BreadcrumbsGenerator $trail, $report, $step) {
        $trail->parent('reports.setup', $report);
        $trail->push('Konfigurieren', route('report.step', ['report' => $report, 'step' => $step]));
    }
);

Breadcrumbs::for(
    'reports.render',
    function (BreadcrumbsGenerator $trail, $report) {
        $trail->parent('reports.setup', $report);
        $trail->push('Einbetten', route('reports.render', ['report' => $report]));
    }
);

Breadcrumbs::for(
    'roles.index',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('admin');
        $trail->push('Benutzerrollen', route('roles.index'));
    }
);

Breadcrumbs::for(
    'roles.create',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('roles.index');
        $trail->push('Neue Benutzerrolle', route('roles.create'));
    }
);

Breadcrumbs::for(
    'roles.edit',
    function (BreadcrumbsGenerator $trail, \Spatie\Permission\Models\Role $role) {
        $trail->parent('roles.index');
        $trail->push($role->name, route('roles.edit', $role));
    }
);

Breadcrumbs::for(
    'seatingSection.create',
    function (BreadcrumbsGenerator $trail) {
        $location = request()->get('location');
        $trail->parent('location.edit', $location);
        $trail->push('Neue Zone', route('seatingSection.create'));
    }
);

Breadcrumbs::for(
    'seatingSection.edit',
    function (BreadcrumbsGenerator $trail, $seatingSection) {
        if (!is_object($seatingSection)) $seatingSection = \App\SeatingSection::find($seatingSection);
        $location = request()->get('location');
        $trail->parent('location.edit', $seatingSection->location_id);
        $trail->push($seatingSection->title, route('seatingSection.edit', $seatingSection));
    }
);

Breadcrumbs::for(
    'seatingRow.create',
    function (BreadcrumbsGenerator $trail) {
        $seatingSection = request()->get('seatingSection');
        $trail->parent('seatingSection.edit', $seatingSection);
        $trail->push('Neue Reihe', route('seatingRow.create'));
    }
);

Breadcrumbs::for(
    'seatingRow.edit',
    function (BreadcrumbsGenerator $trail, SeatingRow $seatingRow) {
        $seatingSection = request()->get('seatingSection');
        $trail->parent('seatingSection.edit', $seatingRow->seatingSection);
        $trail->push($seatingRow->title, route('seatingRow.edit', $seatingRow));
    }
);





Breadcrumbs::for(
    'services.add',
    function (BreadcrumbsGenerator $trail, $date, $city) {
        $trail->parent('calendar');
        $trail->push('Neuer Gottesdienst', route('services.add', compact('date', 'city')));
    }
);

Breadcrumbs::for(
    'services.edit',
    function (BreadcrumbsGenerator $trail, $service) {
        if (!is_numeric($service)) {
            $service = $service->id;
        }
        $trail->parent('calendar');
        $trail->push('#' . $service, route('service.edit', $service));
    }
);

Breadcrumbs::for(
    'service.livechat',
    function (BreadcrumbsGenerator $trail, Service $service) {
        if (is_numeric($service)) {
            $service = Service::find($service);
        }
        $trail->push('Live Chat');
        $trail->push(
            $service->title ?: 'Gottesdienst am ' . $service->day->date->format('d.m.Y') . ', ' . $service->timeText()
        );
    }
);


Breadcrumbs::for(
    'tags.index',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('admin');
        $trail->push('Kennzeichnungen', route('tags.index'));
    }
);

Breadcrumbs::for(
    'tags.create',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('tags.index');
        $trail->push('Neue Kennzeichnung', route('tags.create'));
    }
);

Breadcrumbs::for(
    'tags.edit',
    function (BreadcrumbsGenerator $trail, $tag) {
        $tag = \App\Tag::find($tag);
        $trail->parent('tags.index');
        $trail->push($tag->name, route('tags.edit', $tag));
    }
);

Breadcrumbs::for(
    'users.index',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('admin');
        $trail->push('Benutzer', route('users.index'));
    }
);

Breadcrumbs::for(
    'users.create',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('users.index');
        $trail->push('Neuer Benutzer', route('users.create'));
    }
);

Breadcrumbs::for(
    'user.edit',
    function (BreadcrumbsGenerator $trail, $user) {
        if (is_numeric($user)) {
            $user = \App\User::find($user);
        }
        $trail->parent('users.index');
        $trail->push($user->fullName(), route('user.edit', $user));
    }
);

Breadcrumbs::for(
    'user.join',
    function (BreadcrumbsGenerator $trail, $user) {
        $trail->parent('home');
        $trail->push('Benutzer', route('users.index'));
        $trail->push($user->fullName(), route('user.edit', $user));
        $trail->push('Zusammenführen', route('user.join', $user));
    }
);

Breadcrumbs::for(
    'user.profile',
    function (BreadcrumbsGenerator $trail) {
        $user = Auth::user();
        $trail->parent('home');
        $trail->push($user->fullName(), route('user.profile', $user));
    }
);

Breadcrumbs::for(
    'user.services',
    function (BreadcrumbsGenerator $trail, $user) {
        $trail->parent('user.edit', $user);
        $trail->push('Gottesdienste', route('user.services', $user));
    }
);

Breadcrumbs::for(
    'wedding.add',
    function (BreadcrumbsGenerator $trail, $service) {
        $trail->parent('services.edit', $service);
        $trail->push('Neue Trauung', route('wedding.add', $service));
    }
);

Breadcrumbs::for(
    'whatsnew',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('home');
        $trail->push('Neue Features', route('whatsnew'));
    }
);

Breadcrumbs::for(
    'weddings.edit',
    function (BreadcrumbsGenerator $trail, $wedding) {
        $trail->parent('services.edit', $wedding->service->id);
        $trail->push(
            'Trauung von ' . $wedding->spouse1_name . ' & ' . $wedding->spouse2_name,
            route('weddings.edit', $wedding)
        );
    }
);

Breadcrumbs::for(
    'weddings.wizard',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('home');
        $trail->push('Neue Trauung', route('weddings.wizard'));
    }
);

Breadcrumbs::for(
    'weddings.wizard.step2',
    function (BreadcrumbsGenerator $trail) {
        $trail->parent('weddings.wizard');
        $trail->push('Ortsangaben', route('weddings.wizard.step2'));
    }
);

Breadcrumbs::for(
    'liturgyBlocks.index',
    function (BreadcrumbsGenerator $trail, $service) {
        if (!is_numeric($service)) {
            $service = $service->id;
        }
        $trail->parent('calendar');
        $trail->push('#' . $service, route('service.edit', $service));
        $trail->push('Liturgie', route('liturgyBlocks.index', $service));
    }
);

Breadcrumbs::for('user.create', function (BreadcrumbsGenerator $trail) {
});



Breadcrumbs::for('ministry.request', function(BreadcrumbsGenerator $trail) {
    $trail->push('Dienstanfrage');
});
Breadcrumbs::for('ministry.request.fill', function(BreadcrumbsGenerator $trail) {
    $trail->push('Zusage');
});
