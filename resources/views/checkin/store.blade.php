@extends('layouts.app', ['noNav' => 1])

@section('title', 'Eingecheckt '.($service->location->at_text ?? $service->location->name))

@section('content')
    <div class="text-center" style="width: 100%;">
        <span class="badge badge-success" style="font-size: 10em;"><span class="fa fa-check-circle"></span></span>
        <div style="font-size: 1.5em; font-weight: bold">
            {{ $service->locationText() }}<br/>
            {{ $service->dateTime()->formatLocalized('%d.%m.%Y, %H:%M Uhr') }}<br />
            {{ strtoupper($booking->name) }} [{{ $booking->number }}]
        </div>
    </div>
    <hr/>
    <p>Du bist für den Gottesdienst am {{ $service->dateTime()->formatLocalized('%d.%m.%Y um %H:%M Uhr') }}
        eingecheckt. Bitte zeige diese Nachricht am Eingang vor.</p>
    <small><b>Bitte beachte: </b>Es handelt sich nicht um eine Sitzplatzreservierung. Ein Check-In garantiert nicht,
        dass ein Sitzplatz für dich zur Verfügung steht.</small>
    <hr />
@endsection

@section('scripts')
@endsection
