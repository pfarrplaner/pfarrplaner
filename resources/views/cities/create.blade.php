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
                @input(['name' => 'default_offering_url', 'label' => 'Allgemeine Spendenseite'])
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
                @checkbox(['name' => 'youtube_self_declared_for_children', 'label' => 'Sendungen als "für Kinder" markieren'])
                @input(['name' => 'youtube_cutoff_days', 'label' => 'Aufzeichnungen auf Youtube nach __ Tagen automatisch auf privat schalten', old('youtube_cutoff_days'), 'enabled' => Auth::user()->can('ort-bearbeiten'), 'placeholder' => '0 um diese Funktion zu deaktivieren', 'type' => 'number'])
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
                <div class="row">
                    <div class="col-sm-1">
                        <img class="img-fluid img-responsive" src="{{ asset('img/external/konfiapp.png') }}" />
                    </div>
                    <div class="col-sm-11">
                        <h4>KonfiApp</h4
                        <p>Die <a href="https://konfiapp.de" target="_blank">KonfiApp</a> von Philipp Dormann bietet viele Möglichkeiten, mit Konfis in Kontakt zu bleiben.</p>
                        <h5>Der Pfarrplaner bietet aktuell folgende Integrationsmöglichkeiten:</h5>
                        <ul>
                            <li>Im Pfarrplaner angelegte Gottesdienste können einem Veranstaltungstyp in der KonfiApp zugewiesen werden. Beim Speichern wird dann automatisch ein passender QR-Code in der KonfiApp angelegt. </li>
                        </ul>
                        <p>Für die Integration der KonfiApp ist ein API-Schlüssel erforderlich. Dieser kann im Verwaltungsbereich der KonfiApp über folgenden Link angelegt werden:
                            <a href="https://verwaltung.konfiapp.de/administration/api-tokens/" target="_blank">https://verwaltung.konfiapp.de/administration/api-tokens/</a>.
                            Der dort erstellte Schlüssel muss in das untenstehende Eingabefeld kopiert werden. In der anschließenden Übersicht in der KonfiApp können für den Schlüssel
                            sogenannte "Scopes" aktiviert werden. Folgende Scopes sind für das Funktionieren der Integration erforderlich:</p>
                        <p>
                            <span class="badge badge-secondary">veranstaltungen_list</span>
                            <span class="badge badge-secondary">qr_list</span>
                            <span class="badge badge-secondary">qr_create</span>
                            <span class="badge badge-secondary">qr_delete</span>
                        </p>
                        @input(['name' => 'konfiapp_apikey', 'label' => 'API-Schlüssel für die KonfiApp', 'enabled' => Auth::user()->can('ort-bearbeiten')])
                    </div>
                </div>
                <hr />
                <div class="row">
                    <div class="col-sm-1">
                        <img class="img-fluid img-responsive" src="{{ asset('img/external/communiapp.png') }}" />
                    </div>
                    <div class="col-sm-11">
                        <h4>CommuniApp</h4>
                        <p>Die <a href="https://www.communiapp.de" target="_blank">CommuniApp</a> bietet viele Möglichkeiten, als Gemeinde in Kontakt zu bleiben.</p>
                        <h5>Der Pfarrplaner bietet aktuell folgende Integrationsmöglichkeiten:</h5>
                        <ul>
                            <li>Im Pfarrplaner angelegte Gottesdienste können automatisch in der CommuniApp angelegt werden. Bei dieser Integration können auch weitere Termine aus Outlook bzw. aus dem OnlinePlaner verwendet werden. </li>
                        </ul>
                        <p>Für die Integration der CommuniApp ist ein API-Schlüssel erforderlich. Dieser kann im Verwaltungsbereich der CommuniApp unter Admin > Integrationen > Rest-Api angelegt werden.
                            Der dort erstellte Schlüssel muss in das untenstehende Eingabefeld kopiert werden. </p>
                        @textarea(['name' => 'communiapp_token', 'label' => 'Zugangstoken für die CommuniApp', 'enabled' => Auth::user()->can('ort-bearbeiten')])
                        @input(['name' => 'communiapp_default_group_id', 'label' => 'Gruppen-ID der Hauptgruppe', 'enabled' => Auth::user()->can('ort-bearbeiten')])
                        @input(['name' => 'communiapp_url', 'label' => 'URL zur App', 'enabled' => Auth::user()->can('ort-bearbeiten')])
                        @checkbox(['name' => 'communiapp_use_outlook', 'label' => 'Termine aus Outlook mit einbeziehen', 'enabled' => Auth::user()->can('ort-bearbeiten')])
                        @checkbox(['name' => 'communiapp_use_op', 'label' => 'Termine aus dem Online-Planer mit einbeziehen', 'enabled' => Auth::user()->can('ort-bearbeiten')])
                    </div>
                </div>
            @endtab
        @endtabs
        @endcomponent
    </form>
@endsection
