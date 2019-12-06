<?php

use DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator;

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

Breadcrumbs::for('admin', function (BreadcrumbsGenerator $trail) {
    $trail->parent('home');
    $trail->push('Administration');
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

Breadcrumbs::for('user.services', function (BreadcrumbsGenerator $trail, $user) {
    $trail->parent('users.edit', $user);
    $trail->push('Gottesdienste', route('user.services', $user));
});