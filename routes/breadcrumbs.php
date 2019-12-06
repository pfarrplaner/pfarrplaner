<?php

Breadcrumbs::for('home', function ($trail) {
   $trail->push('<i class="fa fa-home" title="Zurück zur Übersicht"></i>', route('home'));
});

Breadcrumbs::for('inputs.setup', function($trail, $input) {
    /** @var \App\Inputs\AbstractInput $input */
    $inputObject = \App\Inputs\Inputs::get($input);
    $trail->parent('home');
    $trail->push('Sammeleingaben');
    $trail->push($inputObject->title, route('inputs.setup', $input));
});

Breadcrumbs::for('inputs.input', function($trail, $input) {
    /** @var \App\Inputs\AbstractInput $input */
    $inputObject = \App\Inputs\Inputs::get($input);
    $trail->parent('home');
    $trail->push('Sammeleingaben');
    $trail->push($inputObject->title, route('inputs.setup', $input));
    $trail->push('Eingabe', route('inputs.input', $input));
});

Breadcrumbs::for('reports.list', function($trail) {
    $trail->parent('home');
    $trail->push('Ausgabeformate', route('reports.list'));
});

Breadcrumbs::for('reports.setup', function($trail, $report) {
    $reportClass = 'App\\Reports\\' . ucfirst($report) . 'Report';
    if (class_exists($reportClass)) {
        /** @var AbstractReport $report */
        $report = new $reportClass();
    }
    $trail->parent('home');
    $trail->push('Ausgabeformate', route('reports.list'));
    $trail->push($report->title, route('reports.setup', $report->getKey()));
});

Breadcrumbs::for('report.step', function($trail, $report, $step) {
    $reportClass = 'App\\Reports\\' . ucfirst($report) . 'Report';
    if (class_exists($reportClass)) {
        /** @var AbstractReport $report */
        $report = new $reportClass();
    }
    $trail->parent('home');
    $trail->push('Ausgabeformate', route('reports.list'));
    $trail->push($report->title, route('reports.setup', $report->getKey()));
    $trail->push('Konfigurieren', route('report.step', ['report' => $report->getKey(), 'step' => $step]));
});

Breadcrumbs::for('reports.render', function($trail, $report) {
    $reportClass = 'App\\Reports\\' . ucfirst($report) . 'Report';
    if (class_exists($reportClass)) {
        /** @var AbstractReport $report */
        $report = new $reportClass();
    }
    $trail->parent('home');
    $trail->push('Ausgabeformate', route('reports.list'));
    $trail->push($report->title, route('reports.setup', $report->getKey()));
    $trail->push('Einbetten', route('reports.render', ['report' => $report->getKey()]));
});