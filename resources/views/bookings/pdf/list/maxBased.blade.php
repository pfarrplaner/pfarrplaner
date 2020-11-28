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

        table {
            font-size: 1.2em;
        }

        th {
            text-align: left;
        }

        tr.even {
            background-color: lightgray;
        }

    </style>
</head>
<body>
<div style="padding-top: 17mm; padding-left: 12mm; width: 97mm; height: 57mm;">
    <b>Besucherliste für den Gottesdienst<br/>{{ $service->day->date->format('d.m.Y') }} um {{ $service->timeText() }}
        <br/>{{ $service->locationText() }}</b><br/><br/>
    Nach §6 Abs. 2 CoronaVO<br/>
    verwahren bis einschl. <br/>
    <span style="color: red; font-weight: bold;">{{ $service->day->date->clone()->addWeek(4)->format('d.m.Y') }}</span>
</div>
<h1>Besucherliste für den Gottesdienst<br/>{{ $service->day->date->format('d.m.Y') }} um {{ $service->timeText() }}
    , {{ $service->locationText() }}</h1>
@php $i = 1; @endphp
@if (count($list))
    <h2>Angemeldete Besucher</h2>
    <table width="100%">
        <thead>
        <tr>
            <th>Nr.</th>
            <th></th>
            <th>Name</th>
            <th>Anzahl</th>
            <th>Kontakt</th>
        </tr>
        </thead>
        <tbody>
        @foreach($list as $place)
            <tr @if ($loop->index % 2 == 0)class="even" @endif>
                <td width="1cm">{{ $i }}@if($place['booking']->number > 1)
                        -{{ $i+=$place['booking']->number-1 }}@endif</td>
                <td style="font-family: dejavusans; width: 0.5cm;">&#9634</td>
                <td>
                    {{ $place['booking']->name }}@if($place['booking']->first_name )
                        , {{ $place['booking']->first_name }}@endif
                </td>
                <td>
                    {{ $place['booking']->number }}
                </td>
                <td>
                    <small>{{ str_replace("\n", '; ', $place['booking']->contact) }}</small>
                </td>
            </tr>
            @php $i++ @endphp
        @endforeach
        </tbody>
    </table>
    <p>Zahl der angemeldeten Personen: {{ $i-1 }}</p>
@endif
@if($service->registration_max)
    @if (count($list))
        <h2>Freie Plätze</h2>
        <p><b>Bitte nur ausfüllen, wenn das für diesen Gottesdienst erlaubt ist.</b></p>
    @endif
    <table width="100%">
        <thead>
        <tr>
            <th>Nr.</th>
            <th>Name</th>
            <th>Kontakt</th>
        </tr>
        </thead>
        <tbody>
        @php $k=0; @endphp
        @for($j=$i; $j<=$service->registration_max; $j++)
            <tr @if ($k % 2 == 0)class="even" @endif>
                <td width="1cm;">
                    {{ $j }}
                </td>
                <td>
                    <br/><br/><br/><br/>
                </td>
                <td>
                </td>
            </tr>
            @php $k++; @endphp
        @endfor
        </tbody>
    </table>

@endif


<htmlpagefooter name="page-footer">
    <table width="100%" style="font-size: 0.7em;">
        <tr>
            <td>Erstellt
                am {{ Carbon\Carbon::now()->setTimeZone('Europe/Berlin')->formatLocalized('%A, %d. %B %Y um %H:%M')  }}
                Uhr von {{ \Illuminate\Support\Facades\Auth::user()->name }}</td>
            <td style="text-align: right;">Seite {PAGENO} / {nbpg}</td>
        </tr>
    </table>
</htmlpagefooter>

</body>
</html>
