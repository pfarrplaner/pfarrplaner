<html>
<head>
    <style>
        @page {
            margin: 0;
        }

        body, * {
            font-family: 'helveticacondensed', sans-serif;
            text-align: center;
        }

        div.container {
            float: left;
            border: solid 1px white;
            width: 61.3mm !important;
            max-width: 61.3mm !important;
            height: 92mm;
            max-height: 92mm;
            overflow: hidden;
            padding: 6mm;
        }

        tr.even {
            background-color: lightgray;
        }
    </style>
</head>
<body>
@foreach ($services as $location => $localServices)
    @foreach($localServices as $service)
        @for($i=0; $i<$copies; $i++)
            <div class="container" @if($loop->last) style="page-break-after: always;" @endif>
                <p style="font-weight: bold">{{ $service->title ?: 'Gottesdienst' }}<br/>
                    am {{ $service->day->date->format('d.m.Y') }} um {{ $service->timeText() }}
                    <br/>{{ $service->locationText() }}</p>
                <p>
                    <barcode code="{{ $service->konfiapp_event_qr }}" size="2" type="QR" error="M" class="barcode"
                             disableborder="1"/>
                    <br/>
                    @foreach ($types as $type)
                        @if($type->id == $service->konfiapp_event_type)
                            {{ $type->punktzahl }} {{ $type->punktzahl == 1 ? 'Punkt' : 'Punkte' }} in der Kategorie
                            "{{ $type->name }}"
                        @endif
                    @endforeach
                    <span style="font-size: 0.65em; font-style: italic;">gültig am {{ $service->day->date->format('d.m.Y') }} ab {{ $service->timeText() }} für 3 Stunden. </span>
                </p>

                <p style="font-size: 0.4em;">
                    {{ $service->konfiapp_event_qr }} | Gedruckt
                    am {{ \Carbon\Carbon::now()->setTimezone('Europe/Berlin')->formatLocalized('%d.%m.%Y um %H:%M Uhr') }}
                    von {{ \Illuminate\Support\Facades\Auth::user()->name }}.
                </p>
            </div>
        @endfor
    @endforeach
@endforeach
</body>
</html>
