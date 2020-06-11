<html>
<head>
    <style>
        body, * {
            font-family: 'helveticacondensed', sans-serif;
            font-size: 2em;
        }
        tr.even {
            background-color: lightgray;
        }
    </style>
</head>
<body>
@foreach ($services as $location => $localServices)
    @foreach($localServices as $service)
        <div @if(!$loop->last)style="page-break-after: always" @endif>
            <h1>Punkte für die Konfi-App</h1>
            <h2>{{ $service->title ?: 'Gottesdienst' }}<br /> am {{ $service->day->date->format('d.m.Y') }} um {{ $service->timeText() }}<br />{{ $service->locationText() }}</h2>
            <p>Du bist Konfi und willst Punkte für diesen Gottesdienst? Scanne den folgenden Code mit deiner Konfi-App:</p>
            <div style="text-align: center; width: 100%;"><barcode code="{{ $service->konfiapp_event_qr }}" size="4" type="QR" error="M" class="barcode" disableborder="1"/></div>
            <p style="font-size: 0.65em; font-style: italic;">Bitte beachte: Dieser Code funktioniert nur am {{ $service->day->date->format('d.m.Y') }} ab {{ $service->timeText() }} für 3 Stunden. Gelegenheit verpasst? Dann musst du den Gottesdienst von deinem Pfarrer in der App nachtragen lassen.</p>
            <p style="font-size: 0.65em; ">
                @foreach ($types as $type)
                    @if($type->id == $service->konfiapp_event_type)
                        Mit diesem Code erhältst du {{ $type->punktzahl }} {{ $type->punktzahl == 1 ? 'Punkt' : 'Punkte' }} in der Kategorie "{{ $type->name }}".
                    @endif
                @endforeach
            </p>
            <p style="width:100%; text-align: right; font-size: 0.2em;">
                {{ $service->konfiapp_event_qr }} | Gedruckt am {{ \Carbon\Carbon::now()->setTimezone('Europe/Berlin')->formatLocalized('%d.%m.%Y um %H:%M Uhr') }} von {{ \Illuminate\Support\Facades\Auth::user()->name }}.
            </p>
        </div>
    @endforeach
@endforeach
</body>
</html>
