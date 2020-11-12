@extends('layouts.app')

@section('title', 'Kirchengemeinde hinzufügen')

@section('content')
    <form method="post" action="{{ route('cities.store') }}" enctype="multipart/form-data">
        @component('components.ui.card')
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary">Hinzufügen</button>
            @endslot
        @csrf
        @tabheaders
                @tabheader(['id' => 'home', 'title' => 'Allgemeines', 'active' => true]) @endtabheader
                @tabheader(['id' => 'offerings', 'title' => 'Opfer']) @endtabheader
                @tabheader(['id' => 'calendars', 'title' => 'Externe Kalender']) @endtabheader
                @tabheader(['id' => 'streaming', 'title' => 'Streaming']) @endtabheader
                @tabheader(['id' => 'podcast', 'title' => 'Podcast']) @endtabheader
                @tabheader(['id' => 'integrations', 'title' => 'Weitere Integrationen']) @endtabheader
        @endtabheaders
        @tabs
            @tab(['id' => 'home', 'active' => true])
                @input(['name' => 'name', 'label' => 'Ort'])
            @endtab
            @tab(['id' => 'offerings'])
                @input(['name' => 'default_offering_goal', 'label' => 'Opferzweck, wenn nicht angegeben'])
                @input(['name' => 'default_offering_description', 'label' => 'Opferbeschreibung bei leerem Opferzweck'])
                @input(['name' => 'default_funeral_offering_goal', 'label' => 'Opferzweck für Beerdigungen'])
                @input(['name' => 'default_funeral_offering_description', 'label' => 'Opferbeschreibung für Beerdigungen'])
                @input(['name' => 'default_wedding_offering_goal', 'label' => 'Opferzweck für Trauungen'])
                @input(['name' => 'default_wedding_offering_description', 'label' => 'Opferbeschreibung für Trauungen'])
            @endtab
            @tab(['id' => 'calendars'])
                @input(['name' => 'public_events_calendar_url', 'label' => 'URL für einen öffentlichen Kalender auf elkw.de'])
                @input(['name' => 'op_domain', 'label' => 'Domain für den Online-Planer'])
                @input(['name' => 'op_customer_key', 'label' => 'Kundenschlüssel (customer key) für den Online-Planer'])
                @input(['name' => 'op_customer_token', 'label' => 'Token (customer token) für den Online-Planer'])
            @endtab
                @tab(['id' => 'streaming'])
                @input(['name' => 'youtube_channel_url', 'label' => 'URL für den Youtube-Kanal', 'value' => old('youtube_channel_url'), 'enabled' => Auth::user()->can('ort-bearbeiten')])
                @select(['name' => 'youtube_active_stream_id', 'label' => 'Streamschlüssel für die aktive Sendung', 'value' => old('youtube_active_stream_id'), 'items' => $streams, 'empty' => 1, 'useArrayKey' => 1])
                @select(['name' => 'youtube_passive_stream_id', 'label' => 'Streamschlüssel für inaktive Sendungen', 'value' => old('youtube_passive_stream_id'), 'items' => $streams, 'empty' => 1, 'useArrayKey' => 1])
                @checkbox(['name' => 'youtube_auto_startstop', 'label' => 'Sendungen automatisch starten und stoppen', 'value' => old('youtube_auto_startstop')])
                @endtab
                @tab(['id' => 'podcast'])
                @input(['name' => 'podcast_title', 'label' => 'Titel des Podcasts', 'enabled' => Auth::user()->can('ort-bearbeiten')])
                @upload(['name' => 'podcast_logo', 'label' => 'Logo für den Podcast', 'accept' => '.jpg,.jpeg'])
                @upload(['name' => 'sermon_default_image', 'label' => 'Standard-Titelbild zur Predigt', 'accept' => '.jpg,.jpeg'])
                @input(['name' => 'podcast_owner_name', 'label' => 'Herausgeber des Podcasts', 'enabled' => Auth::user()->can('ort-bearbeiten')])
                @input(['name' => 'podcast_owner_email', 'label' => 'E-Mailadresse für den Herausgeber des Podcasts', 'enabled' => Auth::user()->can('ort-bearbeiten')])
                @input(['name' => 'homepage', 'label' => 'Homepage der Kirchengemeinde', 'enabled' => Auth::user()->can('ort-bearbeiten')])
            @endtab
            @tab(['id' => 'integrations'])
                @input(['name' => 'konfiapp_apikey', 'label' => 'API-Schlüssel für die KonfiApp', 'enabled' => Auth::user()->can('ort-bearbeiten')])
            @endtab
        @endtabs
        @endcomponent
    </form>
@endsection
