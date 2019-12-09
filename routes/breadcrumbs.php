<?php

use DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator;

Breadcrumbs::for('absences.index', function (BreadcrumbsGenerator $trail) {
    $trail->parent('home');
    $trail->push('Urlaub', route('absences.index'));
});

Breadcrumbs::for('absences.create', function (BreadcrumbsGenerator $trail, $year, $month, $user) {
    $trail->parent('absences.index');
    $trail->push('Urlaub eintragen', route('absences.create', compact('year', 'month', 'user')));
});

Breadcrumbs::for('absences.edit', function (BreadcrumbsGenerator $trail, $absence) {
    if (!is_numeric($absence)) $absence = $absence->id;
    $trail->parent('absences.index');
    $trail->push('Urlaub bearbeiten', route('absences.edit', $absence));
});


Breadcrumbs::for('admin', function (BreadcrumbsGenerator $trail) {
    $trail->parent('home');
    $trail->push('Administration');
});

Breadcrumbs::for('baptism.add', function (BreadcrumbsGenerator $trail, $service) {
    $trail->parent('services.edit', $service);
    $trail->push('Neue Taufe', route('baptism.add', $service));
});

Breadcrumbs::for('baptisms.create', function (BreadcrumbsGenerator $trail) {
    $trail->parent('home');
    $trail->push('Neue Taufe', route('baptisms.create'));
});

Breadcrumbs::for('baptisms.edit', function (BreadcrumbsGenerator $trail, $baptism) {
    $trail->parent('services.edit', $baptism->service->id);
    $trail->push('Taufe von '.$baptism->candidate_name, route('baptisms.edit', $baptism));
});

Breadcrumbs::for('calendar', function (BreadcrumbsGenerator $trail) {
    $trail->parent('home');
    $trail->push('Gottesdienste', route('calendar'));
});

Breadcrumbs::for('cc-public', function (BreadcrumbsGenerator $trail, $city) {
    $trail->parent('home');
    $trail->push('Kinderkirche', route('cc-public', $city));
});


Breadcrumbs::for('funeral.add', function (BreadcrumbsGenerator $trail, $service) {
    $trail->parent('services.edit', $service);
    $trail->push('Neue Bestattung', route('funeral.add', $service));
});

Breadcrumbs::for('funerals.edit', function (BreadcrumbsGenerator $trail, $funeral) {
    $trail->parent('services.edit', $funeral->service->id);
    $trail->push('Beerdigung von '.$funeral->buried_name, route('funerals.edit', $funeral));
});

Breadcrumbs::for('funerals.wizard', function (BreadcrumbsGenerator $trail) {
    $trail->parent('home');
    $trail->push('Neue Bestattung', route('funerals.wizard'));
});

Breadcrumbs::for('funerals.wizard.step2', function (BreadcrumbsGenerator $trail) {
    $trail->parent('funerals.wizard');
    $trail->push('Ortsangaben', route('funerals.wizard.step2'));
});


Breadcrumbs::for('locations.index', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin');
    $trail->push('Kirchen und Gottesdienstorte', route('locations.index'));
});

Breadcrumbs::for('locations.create', function (BreadcrumbsGenerator $trail) {
    $trail->parent('locations.index');
    $trail->push('Neuer Ort', route('locations.create'));
});

Breadcrumbs::for('locations.edit', function (BreadcrumbsGenerator $trail, $location) {
    $location = \App\Location::find($location);
    $trail->parent('locations.index');
    $trail->push($location->name, route('locations.edit', $location));
});

Breadcrumbs::for('home', function (BreadcrumbsGenerator $trail) {
    $trail->push('<i class="fa fa-home" title="Zurück zur Übersicht"></i>', route('home'));
});


Breadcrumbs::for('ical.connect', function (BreadcrumbsGenerator $trail) {
    $trail->parent('home');
    $trail->push('Outlook-Export', route('ical.connect'));
});


Breadcrumbs::for('ical.setup', function (BreadcrumbsGenerator $trail, $key) {
    $class = 'App\\CalendarLinks\\' . ucfirst($key) . 'CalendarLink';
    if (class_exists($class)) {
        $object = new $class();
    }
    $trail->parent('ical.connect');
    $trail->push($object->getTitle());
    $trail->push('Einrichten');
});

Breadcrumbs::for('ical.link', function (BreadcrumbsGenerator $trail, $key) {
    $class = 'App\\CalendarLinks\\' . ucfirst($key) . 'CalendarLink';
    if (class_exists($class)) {
        $object = new $class();
    }
    $trail->parent('ical.connect');
    $trail->push($object->getTitle());
    $trail->push('Verlinken');
});

Breadcrumbs::for('inputs.setup', function (BreadcrumbsGenerator $trail, $input) {
    /** @var \App\Inputs\AbstractInput $input */
    $inputObject = \App\Inputs\Inputs::get($input);
    $trail->parent('home');
    $trail->push('Sammeleingaben');
    $trail->push($inputObject->title, route('inputs.setup', $input));
});

Breadcrumbs::for('inputs.input', function (BreadcrumbsGenerator $trail, $input) {
    $trail->parent('inputs.setup', $input);
    $trail->push('Eingabe', route('inputs.input', $input));
});

Breadcrumbs::for('cities.index', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin');
    $trail->push('Kirchengemeinden', route('cities.index'));
});

Breadcrumbs::for('cities.create', function (BreadcrumbsGenerator $trail) {
    $trail->parent('cities.index');
    $trail->push('Neue Kirchengemeinde', route('cities.create'));
});

Breadcrumbs::for('cities.edit', function (BreadcrumbsGenerator $trail, $city) {
    $city = \App\City::find($city);
    $trail->parent('cities.index');
    $trail->push($city->name, route('cities.edit', $city));
});

Breadcrumbs::for('login', function (BreadcrumbsGenerator $trail) {
    $trail->push('Anmelden');
});

Breadcrumbs::for('parishes.index', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin');
    $trail->push('Pfarrämter', route('parishes.index'));
});

Breadcrumbs::for('parishes.create', function (BreadcrumbsGenerator $trail) {
    $trail->parent('parishes.index');
    $trail->push('Neues Pfarramt', route('parishes.create'));
});

Breadcrumbs::for('parishes.edit', function (BreadcrumbsGenerator $trail, $parish) {
    $parish = \App\Parish::find($parish)->first();
    $trail->parent('parishes.index');
    $trail->push($parish->name, route('parishes.edit', $parish));
});

Breadcrumbs::for('reports.list', function (BreadcrumbsGenerator $trail) {
    $trail->parent('home');
    $trail->push('Ausgabeformate', route('reports.list'));
});

Breadcrumbs::for('reports.setup', function (BreadcrumbsGenerator $trail, $report) {
    $reportClass = 'App\\Reports\\' . ucfirst($report) . 'Report';
    if (class_exists($reportClass)) {
        /** @var AbstractReport $report */
        $report = new $reportClass();
    }
    $trail->parent('reports.list');
    $trail->push($report->title, route('reports.setup', $report->getKey()));
});

Breadcrumbs::for('report.step', function (BreadcrumbsGenerator $trail, $report, $step) {
    $trail->parent('reports.setup', $report);
    $trail->push('Konfigurieren', route('report.step', ['report' => $report, 'step' => $step]));
});

Breadcrumbs::for('reports.render', function (BreadcrumbsGenerator $trail, $report) {
    $trail->parent('reports.setup', $report);
    $trail->push('Einbetten', route('reports.render', ['report' => $report->getKey()]));
});

Breadcrumbs::for('roles.index', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin');
    $trail->push('Benutzerrollen', route('roles.index'));
});

Breadcrumbs::for('roles.create', function (BreadcrumbsGenerator $trail) {
    $trail->parent('roles.index');
    $trail->push('Neue Benutzerrolle', route('roles.create'));
});

Breadcrumbs::for('roles.edit', function (BreadcrumbsGenerator $trail, \Spatie\Permission\Models\Role $role) {
    $trail->parent('roles.index');
    $trail->push($role->name, route('roles.edit', $role));
});

Breadcrumbs::for('services.add', function (BreadcrumbsGenerator $trail, $date, $city) {
    $trail->parent('calendar');
    $trail->push('Neuer Gottesdienst', route('services.add', compact('date', 'city')));
});

Breadcrumbs::for('services.edit', function (BreadcrumbsGenerator $trail, $service) {
    if (!is_numeric($service)) $service = $service->id;
    $trail->parent('calendar');
    $trail->push('#'.$service, route('services.edit', $service));
});


Breadcrumbs::for('tags.index', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin');
    $trail->push('Kennzeichnungen', route('tags.index'));
});

Breadcrumbs::for('tags.create', function (BreadcrumbsGenerator $trail) {
    $trail->parent('tags.index');
    $trail->push('Neue Kennzeichnung', route('tags.create'));
});

Breadcrumbs::for('tags.edit', function (BreadcrumbsGenerator $trail, $tag) {
    $tag = \App\Tag::find($tag)->first();
    $trail->parent('tags.index');
    $trail->push($tag->name, route('tags.edit', $tag));
});

Breadcrumbs::for('users.index', function (BreadcrumbsGenerator $trail) {
    $trail->parent('admin');
    $trail->push('Benutzer', route('users.index'));
});

Breadcrumbs::for('users.create', function (BreadcrumbsGenerator $trail) {
    $trail->parent('users.index');
    $trail->push('Neuer Benutzer', route('users.create'));
});

Breadcrumbs::for('users.edit', function (BreadcrumbsGenerator $trail, $user) {
    $user = \App\User::find($user)->first();
    $trail->parent('users.index');
    $trail->push($user->fullName(), route('users.edit', $user));
});

Breadcrumbs::for('user.profile', function (BreadcrumbsGenerator $trail) {
    $user = Auth::user();
    $trail->parent('home');
    $trail->push($user->fullName(), route('user.profile', $user));
});

Breadcrumbs::for('user.services', function (BreadcrumbsGenerator $trail, $user) {
    $trail->parent('users.edit', $user);
    $trail->push('Gottesdienste', route('user.services', $user));
});

Breadcrumbs::for('wedding.add', function (BreadcrumbsGenerator $trail, $service) {
    $trail->parent('services.edit', $service);
    $trail->push('Neue Trauung', route('wedding.add', $service));
});

Breadcrumbs::for('whatsnew', function (BreadcrumbsGenerator $trail) {
    $trail->parent('home');
    $trail->push('Neue Features', route('whatsnew'));
});

Breadcrumbs::for('weddings.edit', function (BreadcrumbsGenerator $trail, $wedding) {
    $trail->parent('services.edit', $wedding->service->id);
    $trail->push('Trauung von '.$wedding->spouse1_name.' & '.$wedding->spouse2_name, route('weddings.edit', $wedding));
});

Breadcrumbs::for('weddings.wizard', function (BreadcrumbsGenerator $trail) {
    $trail->parent('home');
    $trail->push('Neue Trauung', route('weddings.wizard'));
});

Breadcrumbs::for('weddings.wizard.step2', function (BreadcrumbsGenerator $trail) {
    $trail->parent('weddings.wizard');
    $trail->push('Ortsangaben', route('weddings.wizard.step2'));
});

