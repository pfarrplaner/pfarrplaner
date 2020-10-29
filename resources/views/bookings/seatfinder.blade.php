@extends('layouts.app')

@section('title', 'Neue Reservierung anlegen')

@section('content')
    @component('components.container')
        <form method="post" action="{{ route('booking.store') }}">
            @csrf
            @component('components.ui.card')
                @slot('cardHeader')
                    Reservierung für: {{ $service->titleText() }} am {{ $service->day->date->format('d.m.Y') }}, {{ $service->timeText() }}, {{ $service->locationText() }}
                @endslot
                @slot('cardFooter')
                    <button type="submit" class="btn btn-primary">Buchen</button>
                @endslot
                @hidden(['name' => 'service_id', 'value' => $service->id])
                @input(['label' => 'Nachname', 'name' => 'name'])
                @input(['label' => 'Vorname', 'name' => 'first_name'])
                @textarea(['label' => 'Kontaktdaten', 'name' => 'contact'])
                @input(['label' => 'Anzahl Personen', 'name' => 'number', 'type' => 'number', 'value' => 1])
            @endcomponent
        </form>
    @endcomponent
@endsection
