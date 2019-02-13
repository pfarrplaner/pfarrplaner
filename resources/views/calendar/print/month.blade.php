<html>
<head>
    <style>
        @page {
            margin: 0.5cm;
            footer: page-footer;
        }

        *, body {
            font-family: 'HelveticaCondensed', 'Helvetica Condensed', 'Helvetica-Condensed', 'Arial Narrow', 'Arial', sans-serif;
            font-size: 9pt;
        }

        h1 {
            font-size: 13pt;
        }

        h2 {
            font-size: 12pt;
        }

        th {
            text-align: left;
        }

        th, td {
            vertical-align: top;
        }

        .day-name {
            font-weight: normal;
        }

        .day-description {
            font-weight: normal;
            font-style: italic;
        }


        td {
            border-left: dotted 2px darkgray;
            padding: 4px 0 0.4cm 4px;
        }

        tr.even th, tr.even td {
            background-color: #d8d8d8;
        }

        tr.header th {
            background-color: black;
            color: white;
        }

        .service-entry {
            border-radius: 5px;
            margin-bottom: 3px;
        }

        .service-special-time {
            color: red;
            font-weight: bold;
        }

        .service-pastor {
            font-weight: bold;
        }

        .designation {
            font-weight: normal;
        }

        .service-team, .service-description {
            font-size: 0.8em;
        }

        .service-description {
            font-style: italic;
        }

        .highlight {
            background-color: yellow;
        }

        .footer {
            font-size: 7px;
        }
    </style>
</head>
<body>
<?php $ctr = 0; ?>
<div class="calendar-month">
    <h1 class="print-only">{{ strftime('%B %Y', $days->first()->date->getTimestamp()) }}</h1>
    <table width="100%" cellspacing="0" cellpadding="0">
        <tr class="header">
            <th>Kirchengemeinde</th>
            @foreach ($days as $day)
                <th width="{{$tableRatio}}%">
                    @if ($day->date->dayOfWeek > 0) <span class="special-weekday">{{ strftime('%a', $day->date->getTimestamp()) }}.</span>, @endif
                    {{ $day->date->format('d.m.Y') }}
                        @if ($day->name)<div class="day-name">{{ $day->name }}</div> @endif
                        @if ($day->description)<div class="day-description">{{ $day->description }}</div> @endif
                </th>
            @endforeach
        </tr>
        @foreach ($cities as $city)
            <tr class="@if(++$ctr %2 == 0) even @else odd @endif">
                <th>{{$city->name}}</th>
                @foreach ($days as $day)
                    <td>
                        @foreach ($services[$city->id][$day->id] as $service)
                            <div class="service-entry">
                                <div class="service-time @if($service->time !== $service->location->default_time) service-special-time @endif">
                                    {{ strftime('%H:%M', strtotime($service->time)) }} Uhr</div>
                                <div class="service-location">{{ $service->location->name }}</div>
                                <div class="service-team service-pastor">
                                    <span class="designation">P: </span>
                                    <span class="name @if($highlight == $service->pastor) highlight @endif">
                                        {{ $service->pastor }}</span>
                                </div>
                                <div class="service-team service-organist">
                                    <span class="designation">O: </span>
                                    <span class="name @if($highlight == $service->organist) highlight @endif">
                                        {{ $service->organist }}</span>
                                </div>
                                <div class="service-team service-sacristan">
                                    <span class="designation">M: </span>
                                    <span class="name @if($highlight == $service->sacristan) highlight @endif">
                                        {{ $service->sacristan }}</span>
                                </div>
                                <div class="service-description">{{ $service->description }}</div>
                            </div>
                            @if ($service->id != $services[$city->id][$day->id]->last()->id) <br /> @endif
                        @endforeach
                    </td>
                @endforeach
            </tr>
        @endforeach
    </table>
</div>
<htmlpagefooter name="page-footer">
    <span class="footer">Quelle: Dienstplan online | Erstellt am {{ strftime('%A, %d. %B %Y') }} von {{ Auth::user()->name }}</span>
</htmlpagefooter>
</body>
</html>
