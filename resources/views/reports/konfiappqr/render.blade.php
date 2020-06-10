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
            <h2>{{ $service->title ?? 'Gottesdienst' }}<br /> am {{ $service->day->date->format('d.m.Y') }} um {{ $service->timeText() }}<br />{{ $service->locationText }}</h2>
            <p>Du bist Konfi und willst Punkte für diesen Gottesdienst? Scanne den folgenden Code mit einer Konfi-App:</p>
            <div style="align:center; width: 100%;"><barcode code="{{ $service->konfiapp_event_qr }}" size="4" type="QR" error="M" class="barcode" disableborder="1"/></div>
            <p style="font-size: 0.8em; font-style: italic;">Bitte beachte: Dieser Code funktioniert nur am {{ $service->day->date->format('d.m.Y') }}. Gelegenheit verpasst? Dann musst du den Gottesdienst von deinem Pfarrer in der App nachtragen lassen.</p>
        </div>
    @endforeach
@endforeach
</body>
</html>
