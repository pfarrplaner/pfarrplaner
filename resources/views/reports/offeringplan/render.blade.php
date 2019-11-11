<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>Opferplan {{ $year }} für {{ $city->name }}</title>
    <style>
        body, * {
            font-family: helveticacondensed;
        }
        .table {
            border-collapse: collapse;
        }
        .table td {
            margin: 0;
            border: 0;
            padding: 3px;
        }
    </style>
</head>
<body>
<h1>Opferplan {{ $year }} für {{ $city->name }}</h1>
<hr/>

@if($count)

    <table class="table table-fluid table-striped" cellspacing="0" border="0">
        <thead>
        <tr>
            <th>Gottesdienst</th>
            <th>Opferzweck</th>
            <th>Typ</th>
            <th>Anmerkungen</th>
            <th>Zähler 1</th>
            <th>Zähler 2</th>
        </tr>
        </thead>

        <tbody>
        <?php $ct = 0 ?>
        @foreach ($services as $dayServices)
            @foreach ($dayServices as $service)
                <?php $ct++; ?>
                <tr @if($ct %2 == 1)style="background-color: lightgray;"@endif>
                    <td valign="top" style="max-width: 3.5cm; width: 3.5cm;"><small><b>{{ $service->day->date->format('d.m.Y') }}, {{ $service->timeText(true) }}
                            <br />{{ $service->locationText() }}</b>
                        @if ($service->descriptionText() != '')<br />{{ $service->descriptionText }}</small>@endif
                    </td>
                    <td valign="top">{{ $service->offering_goal }}</td>
                    <td valign="top" style="width: 1.3cm;"><small>@if($service->offering_type == 'PO')Pflicht @else @if($service->offering_type == 'EO')empf. @else eig.@endif @endif</small>
                    </td>
                    <td valign="top">{{ $service->offering_description }}</td>
                    <td valign="top"><small>{{ $service->offerings_counter1 }}</small></td>
                    <td valign="top"><small>{{ $service->offerings_counter2 }}</small></td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
    <br />
    <p style="font-size: 0.6em; color: gray;">Erstellt mit {{ env('APP_NAME') }} am {{strftime('%A, %d. %B %Y um %H:%M Uhr')}} @auth von {{ Auth::user()->name }} @endauth</p>

@else
    <p>In dem angegebenen Zeitraum wurden keine Termine für die Kinderkirche gefunden.</p>
@endif

</body>
</html>
