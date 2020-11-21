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
                <hr />
                <h3>Manuelle Platzierung</h3>
                <p>Mit den folgenden Felder kannst du die automatische Platzierung dieser Person(en) verhindern und einen festen Platz zuweisen.
                    Dabei können auch die normale Platzzahl oder die automatische Aufteilung einer Reihe überschrieben werden. Lasse diese Felder
                einfach leer, um automatisch eine Position für diese Anmeldung zu finden.</p>
                @input(['label' => 'Fester Sitzplatz', 'name' => 'fixed_seat', 'placeholder' => 'z.B. 1 für ganze Reihe 1, 1A für Platz 1A'])
                @input(['label' => 'Abweichende Platzzahl an diesem Platz', 'name' => 'override_seats', 'placeholder' => ''])
                @input(['label' => 'Abweichende Aufteilung der betroffenen Reihe', 'name' => 'override_split', 'placeholder' => ''])
            @endcomponent
        </form>
    @endcomponent
@endsection
