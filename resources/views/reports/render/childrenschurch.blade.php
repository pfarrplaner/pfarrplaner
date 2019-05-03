<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>Kinderkirche {{ $city->name }} :: Dienstplan Online</title>
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
<h1>Kinderkirche {{ $city->name }}</h1>
<b>Von {{ $start->format('d.m.Y') }} bis {{ $end->format('d.m.Y') }}</b>
<hr/>

@if($count)

    <table class="table table-fluid table-striped" cellspacing="0" border="0">
        <thead></thead>
        <tbody>
        <?php $ct = 0 ?>
        @foreach ($services as $dayServices)
            @foreach ($dayServices as $service)
                <?php $ct++; ?>
                <tr @if($ct %2 == 1)style="background-color: lightgray;"@endif>
                    <td valign="top" width="10%">{{ $service->day->date->format('d.m.Y') }}</td>
                    <td valign="top" width="10%">{{ strftime('%H:%M', strtotime($service->time)) }} Uhr</td>
                    <td valign="top" width="20%"
                        @if($service->hasNonStandardCCLocation()) style="color: red;" @endif>{{ $service->cc_location }}</td>
                    <td valign="top" width="30%">{{ $service->cc_lesson }}</td>
                    <td valign="top" width="30%">{{ $service->cc_staff }} </td>
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
