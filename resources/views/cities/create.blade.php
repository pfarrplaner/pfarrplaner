@extends('layouts.app')

@section('title', 'Kirchengemeinde hinzufügen')

@section('content')
    <form method="post" action="{{ route('cities.store') }}">
        @component('components.ui.card')
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary">Hinzufügen</button>
            @endslot
        @csrf
        @tabheaders
                @tabheader(['id' => 'home', 'title' => 'Allgemeines', 'active' => true]) @endtabheader
                @tabheader(['id' => 'offerings', 'title' => 'Opfer']) @endtabheader
                @tabheader(['id' => 'calendars', 'title' => 'Externe Kalender']) @endtabheader
        @endtabheaders
        @tabs
                @tab(['id' => 'home', 'active' => true])
                        @input(['name' => 'name', 'label' => 'Ort']) @endinput
                @endtab
                @tab(['id' => 'offerings'])
                        @input(['name' => 'default_offering_goal', 'label' => 'Opferzweck, wenn nicht angegeben']) @endinput
                        @input(['name' => 'default_offering_description', 'label' => 'Opferbeschreibung bei leerem Opferzweck']) @endinput
                        @input(['name' => 'default_funeral_offering_goal', 'label' => 'Opferzweck für Beerdigungen']) @endinput
                        @input(['name' => 'default_funeral_offering_description', 'label' => 'Opferbeschreibung für Beerdigungen']) @endinput
                        @input(['name' => 'default_wedding_offering_goal', 'label' => 'Opferzweck für Trauungen']) @endinput
                        @input(['name' => 'default_wedding_offering_description', 'label' => 'Opferbeschreibung für Trauungen']) @endinput
                @endtab
                @tab(['id' => 'calendars'])
                        @input(['name' => 'public_events_calendar_url', 'label' => 'URL für einen öffentlichen Kalender auf elkw.de']) @endinput
                        @input(['name' => 'op_domain', 'label' => 'Domain für den Online-Planer']) @endinput
                        @input(['name' => 'op_customer_key', 'label' => 'Kundenschlüssel (customer key) für den Online-Planer']) @endinput
                        @input(['name' => 'op_customer_token', 'label' => 'Token (customer token) für den Online-Planer']) @endinput
                @endtab
        @endtabs
        @endcomponent
    </form>
@endsection
