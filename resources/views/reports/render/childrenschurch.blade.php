<html>
<head>
    <style>
        *, body {
            font-family: 'DejaVuSansCondensed';
        }
    </style>
</head>
<body>
<h1><img src="{{ env('APP_URL') }}img/cc.png" /> Kinderkirche {{ $city->name }}</h1>
<b>Plan vom {{ $start->format('d.m.Y') }} bis {{ $end->format('d.m.Y') }}</b>
<hr/>
<?php $ct = 0; ?>
<table cellspacing="0" cellpadding="3">
    <thead></thead>
    <tbody>
    @foreach ($services as $dayServices)
        @foreach ($dayServices as $service)
            <?php $ct++; ?>
        <tr @if($ct % 2 == 0) style="background-color: lightgray;" @endif;>
            <td valign="top" width="10%">{{ $service->day->date->format('d.m.Y') }}</td>
            <td valign="top" width="10%">{{ strftime('%H:%M', strtotime($service->time)) }} Uhr</td>
            <td valign="top" width="20%" @if($service->hasNonStandardCCLocation()) style="color: red;" @endif>{{ $service->cc_location }}</td>
            <td valign="top" width="30%">{{ $service->cc_lesson }}</td>
            <td valign="top" width="30%">{{ $service->cc_staff }} </td>
        </tr>
        @endforeach
    @endforeach
    </tbody>
</table>
<br />
<p style="font-size: 0.6em; color: gray;">Erstellt mit {{ env('APP_NAME') }} am {{strftime('%A, %d. %B %Y um %H:%M Uhr')}} von {{ \Illuminate\Support\Facades\Auth::user()->name }}</p>
</body>
</html>
