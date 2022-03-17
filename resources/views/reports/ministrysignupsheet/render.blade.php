<html>
<head>
    <style>
        body, * {
            font-family: 'helveticacondensed', sans-serif;
        }
        tr.even {
            background-color: lightgray;
        }
        h1 {
            margin-bottom: 3px;
            padding-bottom: 0;
        }
        th {
            text-align: left;
        }
    </style>
</head>
<body>
<h1>Dienstplan für {{ join(', ', $ministries) }}</h1>
<b>Von {{ $start->format('d.m.Y') }} bis {{ $end->format('d.m.Y') }}</b>
<hr/>
<table>
    <thead>
        <tr>
            <th>
                Gottesdienst
            </th>
            @foreach($ministries as $ministry)
                <th>{{ \App\Ministry::title($ministry) }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
    @foreach ($services as $service)
        <tr @if($loop->index % 2 == 0)class="even" @endif>
            <td valign="top" width="20%"><small>
                <b>{{ $service->date->format('d.m.Y') }}, {{ $service->timeText() }}</b><br />
                {{ $service->locationText() }}<br />
                {{ $service->titleText(false) }}<br />
                    P: {{ $service->participantsText('P') }}
                    O: {{ $service->participantsText('O') }}
                    M: {{ $service->participantsText('M') }}
                </small>
            </td>
            @foreach($ministries as $ministry)
                @php
                    if ($ministry == 'Pfarrer*in') $ministry = 'P';
                    if ($ministry == 'Organist*in') $ministry = 'O';
                    if ($ministry == 'Mesner*in') $ministry = 'M';
                @endphp
                <td valign="top"><small>{{ $service->participantsText($ministry)}}</small></td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
