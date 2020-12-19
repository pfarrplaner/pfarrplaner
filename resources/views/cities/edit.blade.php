@extends('layouts.app')

@section('title', 'Kirchengemeinde bearbeiten')

@section('navbar-left')
    @if ($city->google_auth_code == '')
        <a class="btn btn-default" href="{{ route('google-auth', ['city' => $city, 'nextStep' => route('cities.edit', $city)]) }}">Mit Youtube verbinden</a>
    @else
        <a class="btn btn-default" href="{{ route('google-auth', ['city' => $city, 'nextStep' => route('cities.edit', $city)]) }}">Mit Youtube verbunden</a>
    @endif
@endsection

@section('content')
    <form method="post" action="{{ route('cities.update', $city->id) }}" id="frm" enctype="multipart/form-data">
        @method('PATCH')
        @csrf
        @component('components.ui.card')
            @slot('cardFooter')
                <button id="btnSubmit" type="submit" class="btn btn-primary">Speichern</button>
            @endslot
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
                    @input(['name' => 'name', 'label' => 'Ort', 'value' => $city->name, 'enabled' => Auth::user()->can('ort-bearbeiten')])
                @endtab
                @tab(['id' => 'offerings'])
                    @input(['name' => 'default_offering_goal', 'label' => 'Opferzweck, wenn nicht angegeben', 'value' => $city->default_offering_goal])
                    @input(['name' => 'default_offering_description', 'label' => 'Opferbeschreibung bei leerem Opferzweck', 'value' => $city->default_offering_description])
                    @input(['name' => 'default_funeral_offering_goal', 'label' => 'Opferzweck für Beerdigungen', 'value' => $city->default_funeral_offering_goal])
                    @input(['name' => 'default_funeral_offering_description', 'label' => 'Opferbeschreibung für Beerdigungen', 'value' => $city->default_funeral_offering_description])
                    @input(['name' => 'default_wedding_offering_goal', 'label' => 'Opferzweck für Trauungen', 'value' => $city->default_wedding_offering_goal])
                    @input(['name' => 'default_wedding_offering_description', 'label' => 'Opferbeschreibung für Trauungen', 'value' => $city->default_wedding_offering_description])
                    @input(['name' => 'default_offering_url', 'label' => 'Allgemeine Spendenseite', 'value' => $city->default_offering_url])
                @endtab
                @tab(['id' => 'calendars'])
                    @input(['name' => 'public_events_calendar_url', 'label' => 'URL für einen öffentlichen Kalender auf elkw.de', 'value' => $city->public_events_calendar_url, 'enabled' => Auth::user()->can('ort-bearbeiten')])
                    @input(['name' => 'op_domain', 'label' => 'Domain für den Online-Planer', 'value' => $city->op_domain, 'enabled' => Auth::user()->can('ort-bearbeiten')])
                    @input(['name' => 'op_customer_key', 'label' => 'Kundenschlüssel (customer key) für den Online-Planer', 'value' => $city->op_customer_key, 'enabled' => Auth::user()->can('ort-bearbeiten')])
                    @input(['name' => 'op_customer_token', 'label' => 'Token (customer token) für den Online-Planer', 'value' => $city->op_customer_token, 'enabled' => Auth::user()->can('ort-bearbeiten')])
                @endtab
                @tab(['id' => 'streaming'])
                    @input(['name' => 'youtube_channel_url', 'label' => 'URL für den Youtube-Kanal', 'value' => $city->youtube_channel_url, 'enabled' => Auth::user()->can('ort-bearbeiten')])
                    @select(['name' => 'youtube_active_stream_id', 'label' => 'Streamschlüssel für die aktive Sendung', 'value' => $city->youtube_active_stream_id, 'items' => $streams, 'empty' => 1, 'useArrayKey' => 1])
                    @select(['name' => 'youtube_passive_stream_id', 'label' => 'Streamschlüssel für inaktive Sendungen', 'value' => $city->youtube_passive_stream_id, 'items' => $streams, 'empty' => 1, 'useArrayKey' => 1])
                    @checkbox(['name' => 'youtube_auto_startstop', 'label' => 'Sendungen automatisch starten und stoppen', 'value' => $city->youtube_auto_startstop])
                    @input(['name' => 'youtube_cutoff_days', 'label' => 'Aufzeichnungen auf Youtube nach __ Tagen automatisch auf privat schalten', 'value' => $city->youtube_cutoff_days, 'enabled' => Auth::user()->can('ort-bearbeiten'), 'placeholder' => '0 um diese Funktion zu deaktivieren', 'type' => 'number'])
                @endtab
                @tab(['id' => 'podcast'])
                    @input(['name' => 'podcast_title', 'label' => 'Titel des Podcasts', 'value' => $city->podcast_title, 'enabled' => Auth::user()->can('ort-bearbeiten')])
                    @upload(['name' => 'podcast_logo', 'label' => 'Logo für den Podcast', 'value' => $city->podcast_logo, 'prettyName' => $city->name.'-Podcast-Logo', 'accept' => '.jpg,.jpeg'])
                    @upload(['name' => 'sermon_default_image', 'label' => 'Standard-Titelbild zur Predigt', 'value' => $city->sermon_default_image, 'prettyName' => $city->name.'-Standard-Predigtbild', 'accept' => '.jpg,.jpeg'])
                    @input(['name' => 'podcast_owner_name', 'label' => 'Herausgeber des Podcasts', 'value' => $city->podcast_owner_name, 'enabled' => Auth::user()->can('ort-bearbeiten')])
                    @input(['name' => 'podcast_owner_email', 'label' => 'E-Mailadresse für den Herausgeber des Podcasts', 'value' => $city->podcast_owner_email, 'enabled' => Auth::user()->can('ort-bearbeiten')])
                    @input(['name' => 'homepage', 'label' => 'Homepage der Kirchengemeinde', 'value' => $city->homepage, 'enabled' => Auth::user()->can('ort-bearbeiten')])
                @endtab
                @tab(['id' => 'integrations'])
                    <div class="row">
                        <div class="col-sm-2">
                            <img class="img-fluid" src="{{ asset('img/external/konfiapp.png') }}" />
                        </div>
                        <div class="col-sm-10">
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
                        </div>
                    </div>
                    @input(['name' => 'konfiapp_apikey', 'label' => 'API-Schlüssel für die KonfiApp', 'value' => $city->konfiapp_apikey, 'enabled' => Auth::user()->can('ort-bearbeiten')])
                @endtab
            @endtabs
    @endcomponent
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#btnSubmit').click(function (e) {
                $('input').removeAttr('disabled');
            });
        });
    </script>
@endsection
