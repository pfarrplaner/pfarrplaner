<html>
<head>
    <style>
        body, * {
            font-family: 'helveticacondensed', sans-serif;
        }
    </style>
</head>
<body>
<h1>Gottesdienste für @foreach($highlight as $user){{ $user->lastName(true) }}@if($loop->last) @else, @endif @endforeach</h1>
<b>Von {{ $start }} bis {{ $end }}</b>
<hr/>
<table>
    <thead></thead>
    <tbody>
    @foreach ($services as $service)
        <tr>
            <td width="10%">{{ $service->day->date->format('d.m.Y') }}</td>
            <td width="10%">{{ strftime('%H:%M', strtotime($service->time)) }} Uhr</td>
            <td width="20%">@if (!is_object($service->location)){{ $service->special_location }}@else{{$service->location->name}}@endif </td>
            <td width="20%"> P: {{ $service->participantsText('P') }} </td>
            <td width="20%"> O: {{ $service->participantsText('O') }} </td>
            <td width="20%"> M: {{ $service->participantsText('M') }} </td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
