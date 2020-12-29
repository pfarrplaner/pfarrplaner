@extends('layouts.app')

@section('title', 'Reservierung bearbeiten')

@section('content')
    @component('components.container')
        <form method="post" action="{{ route('booking.update', $booking) }}">
            @csrf
            @method('PATCH')
            @component('components.ui.card')
                @slot('cardHeader')
                    Reservierung für: {{ $booking->service->titleText() }} am {{ $booking->service->day->date->format('d.m.Y') }}, {{ $booking->service->timeText() }}, {{ $booking->service->locationText() }}
                @endslot
                @slot('cardFooter')
                    <button type="submit" class="btn btn-primary">Buchung ändern</button>
                @endslot
                @hidden(['name' => 'service_id', 'value' => $booking->service->id])
                @hidden(['name' => 'booking_id', 'value' => $booking->id])
                @input(['label' => 'Nachname', 'name' => 'name', 'value' => $booking->name])
                @input(['label' => 'Vorname', 'name' => 'first_name', 'value' => $booking->first_name])
                @textarea(['label' => 'Kontaktdaten', 'name' => 'contact', 'value' => $booking->contact])
                @input(['label' => 'E-Mailadresse', 'name' => 'email', 'value' => $booking->email])
                @input(['label' => 'Anzahl Personen', 'name' => 'number', 'type' => 'number', 'value' => $booking->number])
                <hr />
                <h3>Manuelle Platzierung</h3>
                <p>Mit den folgenden Felder kannst du die automatische Platzierung dieser Person(en) verhindern und einen festen Platz zuweisen.
                    Dabei können auch die normale Platzzahl oder die automatische Aufteilung einer Reihe überschrieben werden. Lasse diese Felder
                einfach leer, um automatisch eine Position für diese Anmeldung zu finden.</p>
                @input(['label' => 'Fester Sitzplatz', 'name' => 'fixed_seat', 'placeholder' => 'z.B. 1 für ganze Reihe 1, 1A für Platz 1A', 'value' => $booking->fixed_seat])
                @input(['label' => 'Abweichende Platzzahl an diesem Platz', 'name' => 'override_seats', 'placeholder' => '', 'value' => $booking->override_seats])
                @input(['label' => 'Abweichende Aufteilung der betroffenen Reihe', 'name' => 'override_split', 'placeholder' => '', 'value' => $booking->override_split])
            @endcomponent
        </form>
    @endcomponent
@endsection
