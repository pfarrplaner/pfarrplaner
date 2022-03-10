{{ $ministry }}
<hr />
<html>
<head>
    <style>
        body, * {
            font-family: 'helveticacondensed', sans-serif;
        }
        tr.even {
            background-color: lightgray;
        }
    </style>
</head>
<body>
<h1>Dienstplan für {{ $ministry }}</h1>
<b>Von {{ $start }} bis {{ $end }}</b>
<p><small>Stand: {{ \Carbon\Carbon::now()->setTimezone('Europe/Berlin')->format('d.m.Y H:i') }} Uhr. Immer aktuell auf <a href="https://www.pfarrplaner.de">www.pfarrplaner.de</a></small></p>
<hr/>
<table style="width: 100%">
    <thead>
    <tr>
        <th>Datum</th>
        <th>Uhrzeit</th>
        <th>Ort</th>
        <th>{{ $ministry }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($services as $service)
        <tr @if($loop->index % 2 == 0)class="even" @endif>
            <td>{{ $service->date->format('d.m.Y') }}</td>
            <td>{{ $service->timeText() }}</td>
            <td>{{ $service->locationText() }}</td>
            <td valign="top"> {{ $service->participantsText($ministry) }} </td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
