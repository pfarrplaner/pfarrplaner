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
<h1>Gottesdienste für @foreach($highlight as $user){{ $user->fullName(true) }}@if($loop->last) @else, @endif @endforeach</h1>
<b>Von {{ $start }} bis {{ $end }}</b>
<hr/>
<table>
    <thead></thead>
    <tbody>
    @foreach ($services as $service)
        <tr @if($loop->index % 2 == 0)class="even" @endif>
            <td valign="top" width="20%">{{ $service->date->format('d.m.Y') }}<br />{{ $service->timeText() }}</td>
            <td valign="top" width="20%">{{ $service->titleText(false) }}<br />{{ $service->locationText() }}</td>
            <td valign="top" width="15%"> P: {{ $service->participantsText('P') }} </td>
            <td valign="top" width="15%"> O: {{ $service->participantsText('O') }} </td>
            <td valign="top" width="15%"> M: {{ $service->participantsText('M') }} </td>
            <td valign="top" width="15%"> A: {{ $service->participantsText('A') }} </td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
