<!DOCTYPE html>
<html>
<head>
    <style>

        @page {
            footer: page-footer;
        }

        body {
            font-family: helveticacondensed;
        }

        table { font-size: 1.2em;}
        th { text-align: left; }
        tr.even {
            background-color: lightgray;
        }

    </style>
</head>
<body>
<div style="padding-top: 17mm; padding-left: 12mm; width: 97mm; height: 57mm;">
    <b>Besucherliste für den Gottesdienst<br />{{ $service->day->date->format('d.m.Y') }} um {{ $service->timeText() }}<br />{{ $service->locationText() }}</b><br /><br />
    Nach §6 Abs. 2 CoronaVO<br />
    verwahren bis einschl. <br />
    <span style="color: red; font-weight: bold;">{{ $service->day->date->clone()->addWeek(4)->format('d.m.Y') }}</span>
</div>
<h1>Besucherliste für den Gottesdienst<br />{{ $service->day->date->format('d.m.Y') }} um {{ $service->timeText() }}, {{ $service->locationText() }}</h1>
@if (count($list))
<h2>Angemeldete Besucher</h2>
<table width="100%">
    <thead>
    <tr>
        <th></th>
        <th>Name</th>
        <th>Anzahl</th>
        <th>Kontakt</th>
        <th>Zone</th>
        <th>Platz</th>
    </tr>
    </thead>
    <tbody>
    @foreach($list as $place)
        <tr @if ($loop->index % 2 == 0)class="even" @endif>
            <td style="font-family: dejavusans;">&#9634</td>
            <td>
                {{ $place['booking']->name }}@if($place['booking']->first_name ), {{ $place['booking']->first_name }}@endif
            </td>
            <td>
                {{ $place['booking']->number }}
            </td>
            <td>
                <small>{{ str_replace("\n", '; ', $place['booking']->contact) }}</small>
            </td>
            <td style="{{ $place['row']->getCSS() }}">
                {{ $place['row']->seatingSection->title }}
            </td>
            <td style="{{ $place['row']->getCSS() }}">
                {{ $place['row']->title }}
            </td>
        </tr>

    @endforeach
    </tbody>
</table>
<p>Zahl der angemeldeten Personen: {{ $number }}</p>
@if(($service->exclude_sections != '') || ($service->exclude_places != ''))
    <h2>Reservierte/Gesperrte Bereiche</h2>
    @if((trim($service->exclude_sections) != ''))
        <p><b>Folgende Zonen sind für diesen Gottesdienst reserviert/gesperrt:</b><br /> {{ str_replace(',', ', ', $service->exclude_sections) }}</p>
    @endif
    @if(($service->exclude_places != ''))
        <p><b>Folgende Sitzplätze/Reihen sind für diesen Gottesdienst reserviert/gesperrt:</b><br /> {{ str_replace(',', ', ', $service->exclude_places) }}</p>
    @endif
@endif
<h2>Freie Plätze</h2>
<p><b>Bitte nur ausfüllen, wenn das für diesen Gottesdienst erlaubt ist.</b></p>
@endif
@if(count($empty))
<p><span style="text-decoration: underline;">Hinweis:</span> Die Datenerhebung ist nach §6 Abs. 1 CoronaVO gestattet. Nach §6 Abs. 4-5 CoronaVO ist der Zutritt zum Gottesdienst nur nach vollständiger und korrekter Erhebung dieser Kontaktdaten gestattet.</p>
<p>Wird eine teilbare Reihe ganz belegt, bitte unter Platz A eintragen und alle weiteren Plätze streichen.</p>
<hr />
<table width="100%">
    <thead>
    <tr>
        <th>Zone</th>
        <th>Platz</th>
        <th>Name</th>
        <th>Anzahl</th>
        <th>Kontakt</th>
    </tr>
    </thead>
    <tbody>
    @foreach($empty as $key => $place)
        <tr @if ($loop->index % 2 == 0)class="even" @endif>
            <td width="4cm;" valign="top">
                {{ $place['row']->seatingSection->title }}
            </td>
            <td width="1cm;" valign="top" style="{{ $place['row']->getCSS() }}">
                {{ $key }}<span style="font-size: 8px;"><br />max. {{ $place['row']->seats }}
                @if(!is_numeric($key))<br />Reihe: {{ $service->getSeatFinder()->getFullRow($key)->seats }}@endif
                </span>
            </td>
            <td>
                <br /><br /><br /><br />
            </td>
            <td width="1cm;">
            </td>
            <td>
            </td>
        </tr>

    @endforeach
    </tbody>
</table>
@endif
@if(count($participants))
    <h2>Mitwirkende</h2>
    <p>Die Kontaktdaten der folgenden Mitwirkenden sind bekannt und können vom Gemeindebüro gegebenenfalls nachgetragen werden:</p>
    <table width="100%">
        <thead>
        <tr>
            <th>Name</th>
            <th>Kontakt</th>
        </tr>
        </thead>
        <tbody>
        @foreach($participants as $participant)
            <tr @if ($loop->index % 2 == 0)class="even" @endif>
                <td style="width: 8cm; height: 2em;">{{ $participant }}</td>
                <td></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif


<htmlpagefooter name="page-footer">
    <table width="100%" style="font-size: 0.7em;">
        <tr>
            <td>Erstellt am {{ Carbon\Carbon::now()->setTimeZone('Europe/Berlin')->formatLocalized('%A, %d. %B %Y um %H:%M')  }} Uhr von {{ \Illuminate\Support\Facades\Auth::user()->name }}</td>
            <td style="text-align: right;">Seite {PAGENO} / {nbpg}</td>
        </tr>
    </table>
</htmlpagefooter>

</body>
</html>
