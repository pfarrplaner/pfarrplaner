<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
  - @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
  - @link https://github.com/pfarrplaner/pfarrplaner
  - @version git: $Id: 12187c866f72c68ef3d63946ac26a5ffdd5fc292 $
  -
  - Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
  -
  - Pfarrplaner is based on the Laravel framework (https://laravel.com).
  - This file may contain code created by Laravel's scaffolding functions.
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU General Public License as published by
  - the Free Software Foundation, either version 3 of the License, or
  - (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU General Public License for more details.
  -
  - You should have received a copy of the GNU General Public License
  - along with this program.  If not, see <http://www.gnu.org/licenses/>.
  -->

<template>
    <admin-layout :title="getTitle()">
        <template slot="navbar-left">
            <button class="btn btn-primary" title="Speichern" @click="saveCity">
                <span class="d-inline d-md-none mdi mdi-content-save"></span> <span class="d-none d-md-inline">Speichern</span>
            </button>
        </template>
        <template slot="tab-headers">
            <tab-headers>
                <tab-header id="home" title="Allgemeines" :active-tab="activeTab"/>
                <tab-header id="offerings" title="Opfer" :active-tab="activeTab"/>
                <tab-header id="calendars" title="Externe Kalender" :active-tab="activeTab"/>
                <tab-header id="streaming" title="Streaming" :active-tab="activeTab"/>
                <tab-header id="podcast" title="Podcast" :active-tab="activeTab"/>
                <tab-header id="integrations" title="Weitere Integrationen" :active-tab="activeTab"/>
            </tab-headers>
        </template>
        <tabs>
            <tab id="home" :active-tab="activeTab">
                <form-input name="name" label="Name der Kirchengemeinde" v-model="myCity.name" autofocus/>
                <form-input name="offical_name" label="Offizielle Bezeichnung" v-model="myCity.official_name"/>
                <form-input name="homepage" label="Homepage der Kirchengemeinde" v-model="myCity.homepage"/>
                <form-image-attacher v-model="myCity.logo" label="Logo der Kirchengemeinde"
                                     :attach-route="route('city.attach', {city: myCity.id, field: 'logo'})"
                                     :detach-route="route('city.detach', {city: myCity.id, field: 'logo'})"/>

            </tab>
            <tab id="offerings" :active-tab="activeTab">
                <form-input name="default_offering_goal" label="Opferzweck, wenn nicht angegeben"
                            v-model="myCity.default_offering_goal"/>
                <form-input name="default_offering_description" label="Opferbeschreibung bei leerem Opferzweck"
                            v-model="myCity.default_offering_description"/>
                <form-input name="default_funeral_offering_goal" label="Opferzweck für Beerdigungen"
                            v-model="myCity.default_funeral_offering_goal"/>
                <form-input name="default_funeral_offering_description" label="Opferbeschreibung bei Beerdigungen"
                            v-model="myCity.default_funeral_offering_description"/>
                <form-input name="default_wedding_offering_goal" label="Opferzweck für Trauungen"
                            v-model="myCity.default_wedding_offering_goal"/>
                <form-input name="default_wedding_offering_description" label="Opferbeschreibung bei Trauungen"
                            v-model="myCity.default_wedding_offering_description"/>
                <form-input name="default_offering_url" label="Allgemeine Spendenseite"
                            v-model="myCity.default_offering_url"/>
            </tab>
            <tab id="calendars" :active-tab="activeTab">
                <form-input name="public_events_calendar_url" label="URL für einen öffentlichen Kalender auf elkw.de"
                            v-model="myCity.public_events_calendar_url"/>
                <form-input name="op_domain" label="Domain für den Online-Planer"
                            v-model="myCity.op_domain"/>
                <form-input name="op_customer_key" label="Kundenschlüssel (customer key) für den Online-Planer"
                            v-model="myCity.op_customer_key"/>
                <form-input name="op_customer_token" label="Token (customer token) für den Online-Planer"
                            v-model="myCity.op_customer_token"/>
            </tab>
            <tab id="streaming" :active-tab="activeTab">
                <form-input name="youtube_channel_url" label="URL für den YouTube-Kanal"
                            v-model="myCity.youtube_channel_url"/>
                <form-selectize name="youtube_active_stream_id" v-model="myCity.youtube_active_stream_id"
                                label="Streamschlüssel für die aktive Sendung" :options="streamOptions"/>
                <form-selectize name="youtube_passive_stream_id" v-model="myCity.youtube_passive_stream_id"
                                label="Streamschlüssel für inaktive Sendungen" :options="streamOptions"/>
                <form-check name="youtube_auto_startstop" v-model="myCity.youtube_auto_startstop"
                            label="Sendungen automatisch starten und stoppen "/>
                <form-check name="youtube_self_declared_for_children"
                            v-model="myCity.youtube_self_declared_for_children"
                            label="Sendungen als Kindersendungen markieren"/>
                <form-input type="number" name="youtube_cutoff_days" v-model="myCity.youtube_cutoff_days"
                            label="Aufzeichnungen auf Youtube nach __ Tagen automatisch auf privat schalten"/>
            </tab>
            <tab id="podcast" :active-tab="activeTab">
                <form-input name="podcast_title" label="Titel des Podcasts" v-model="myCity.podcast_title"/>
                <div class="row">
                    <div class="col-md-6">
                        <form-image-attacher v-model="myCity.podcast_logo" label="Logo des Podcasts"
                                             :attach-route="route('city.attach', {city: myCity.id, field: 'podcast_logo'})"
                                             :detach-route="route('city.detach', {city: myCity.id, field: 'podcast_logo'})"/>
                    </div>
                    <div class="col-md-6">
                        <form-image-attacher v-model="myCity.sermon_default_image"
                                             label="Standard-Titelbild zur Predigt"
                                             :attach-route="route('city.attach', {city: myCity.id, field: 'sermon_default_image'})"
                                             :detach-route="route('city.detach', {city: myCity.id, field: 'sermon_default_image'})"/>
                    </div>
                </div>
                <form-input name="podcast_owner_name" label="Herausgeber des Podcasts"
                            v-model="myCity.podcast_owner_name"/>
                <form-input name="podcast_owner_email" label="E-Mailadresse für den Herausgeber des Podcasts"
                            v-model="myCity.podcast_owner_email"/>
            </tab>
            <tab id="integrations" :active-tab="activeTab">
                <div class="accordion" id="integrationsAccordion">
                    <div class="card">
                        <div class="card-header" id="headingKonfiApp">
                            <h2 class="mb-0">
                                <button class="btn btn-block text-left pl-0 accordion-button collapsed" type="button" data-toggle="collapse" data-target="#collapseKonfiApp" aria-expanded="false" aria-controls="collapseKonfiApp">
                                    <img class="mx-2" style="max-height: 2em" src="https://www.pfarrplaner.de/img/external/konfiapp.png?v=2">KonfiApp

                                </button>
                            </h2>
                        </div>

                        <div id="collapseKonfiApp" class="collapse" aria-labelledby="headingKonfiApp" data-parent="#integrationsAccordion">
                            <div class="card-body">
                                <p>Die <a href="https://konfiapp.de" target="_blank">KonfiApp</a> von Philipp Dormann bietet
                                    viele Möglichkeiten, mit Konfis in Kontakt zu bleiben.</p>
                                <h5>Der Pfarrplaner bietet aktuell folgende Integrationsmöglichkeiten:</h5>
                                <ul>
                                    <li>Im Pfarrplaner angelegte Gottesdienste können einem Veranstaltungstyp in der
                                        KonfiApp
                                        zugewiesen werden. Beim Speichern wird dann automatisch ein passender QR-Code in der
                                        KonfiApp angelegt.
                                    </li>
                                </ul>
                                <p>Für die Integration der KonfiApp ist ein API-Schlüssel erforderlich. Dieser kann im
                                    Verwaltungsbereich der KonfiApp über folgenden Link angelegt werden:
                                    <a href="https://verwaltung.konfiapp.de/administration/api-tokens/" target="_blank">https://verwaltung.konfiapp.de/administration/api-tokens/</a>.
                                    Der dort erstellte Schlüssel muss in das untenstehende Eingabefeld kopiert werden. In
                                    der
                                    anschließenden Übersicht in der KonfiApp können für den Schlüssel
                                    sogenannte "Scopes" aktiviert werden. Folgende Scopes sind für das Funktionieren der
                                    Integration erforderlich:</p>
                                <p>
                                    <span class="badge badge-secondary">veranstaltungen.read</span>
                                    <span class="badge badge-secondary">veranstaltungen.qr.read</span>
                                    <span class="badge badge-secondary">veranstaltungen.qr.create</span>
                                    <span class="badge badge-secondary">veranstaltungen.qr.delete</span>
                                </p>
                                <form-input name="konfiapp_apikey" label="API-Schlüssel für die KonfiApp"
                                            v-model="myCity.konfiapp_apikey"/>
                                <konfi-app-event-type-select v-if="myCity.konfiapp_apikey" name="konfiapp_event_type"
                                                             label="Veranstaltungsart in der KonfiApp"
                                                             :city="myCity" v-model="myCity.konfiapp_default_type"/>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="headingCommuniApp">
                            <h2 class="mb-0">
                                <button class="btn btn-block text-left pl-0 accordion-button collapsed" type="button" data-toggle="collapse" data-target="#collapseCommuniApp" aria-expanded="false" aria-controls="collapseCommuniApp">
                                    <img class="mx-2" style="max-height: 2em" src="/img/external/communiapp.png">CommuniApp
                                </button>
                            </h2>
                        </div>

                        <div id="collapseCommuniApp" class="collapse" aria-labelledby="headingCommuniApp" data-parent="#integrationsAccordion">
                            <div class="card-body">
                                <p>Die <a href="https://www.communiapp.de" target="_blank">CommuniApp</a> bietet viele
                                    Möglichkeiten, als Gemeinde in Kontakt zu bleiben.</p>
                                <h5>Der Pfarrplaner bietet aktuell folgende Integrationsmöglichkeiten:</h5>
                                <ul>
                                    <li>Im Pfarrplaner angelegte Gottesdienste können automatisch in der CommuniApp angelegt
                                        werden. Bei dieser Integration können auch weitere Termine aus Outlook bzw. aus dem
                                        OnlinePlaner verwendet werden.
                                    </li>
                                </ul>
                                <p>Für die Integration der CommuniApp ist ein API-Schlüssel erforderlich. Dieser kann im
                                    Verwaltungsbereich der CommuniApp unter Admin > Integrationen > Rest-Api angelegt werden.
                                    Der dort erstellte Schlüssel muss in das untenstehende Eingabefeld kopiert werden. </p>
                                <form-textarea name="communiapp_token" v-model="myCity.communiapp_token"
                                               label="Zugangstoken für die CommuniApp"/>
                                <form-input name="communiapp_default_group_id" v-model="myCity.communiapp_default_group_id"
                                            label="Gruppen-ID der Hauptgruppe"/>
                                <form-input name="communiapp_url" v-model="myCity.communiapp_url" label="URL der App"/>
                            </div>
                        </div>
                    </div>
                </div>
            </tab>
        </tabs>
    </admin-layout>
</template>

<script>
import Card from "../../../components/Ui/cards/card";
import CardHeader from "../../../components/Ui/cards/cardHeader";
import CardBody from "../../../components/Ui/cards/cardBody";
import TabHeaders from "../../../components/Ui/tabs/tabHeaders";
import TabHeader from "../../../components/Ui/tabs/tabHeader";
import Tabs from "../../../components/Ui/tabs/tabs";
import Tab from "../../../components/Ui/tabs/tab";
import FormInput from "../../../components/Ui/forms/FormInput";
import FormSelectize from "../../../components/Ui/forms/FormSelectize";
import FormCheck from "../../../components/Ui/forms/FormCheck";
import FormImageAttacher from "../../../components/Ui/forms/FormImageAttacher";
import FormTextarea from "../../../components/Ui/forms/FormTextarea";
import KonfiAppEventTypeSelect from "../../../components/Ui/elements/KonfiAppEventTypeSelect";

export default {
    name: "CityEditor",
    components: {
        KonfiAppEventTypeSelect,
        FormTextarea,
        FormImageAttacher,
        FormCheck, FormSelectize, FormInput, Tab, Tabs, TabHeader, TabHeaders, CardBody, CardHeader, Card
    },
    props: ['city', 'streams'],
    data() {
        var streamOptions = [];
        for (var streamKey in this.streams) {
            streamOptions.push({id: streamKey, name: this.streams[streamKey]});
        }

        return {
            myCity: this.city,
            activeTab: 'home',
            streamOptions: streamOptions,
        }
    },
    methods: {
        getTitle() {
            return 'Kirchengemeinde "' + this.city.name + '" bearbeiten';
        },
        saveCity() {
            this.$inertia.patch(route('city.update', {city: this.city.name}), this.myCity);
        }
    }
}
</script>

<style scoped>

</style>
