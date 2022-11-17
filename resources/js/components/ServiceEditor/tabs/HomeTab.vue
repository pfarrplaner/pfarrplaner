<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
  - @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
  - @link https://github.com/pfarrplaner/pfarrplaner
  - @version git: $Id$
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
    <div class="home-tab">
        <div class="row">
            <div class="col-md-4">
                <form-date-picker name="date" label="Datum und Uhrzeit" v-model="myService.date" :config="myDateTimePickerConfig" iso-date/>
            </div>
            <div class="col-md-4">
                <location-select name="location_id" label="Ort" :value="myLocation" v-if="!locationUpdating"
                                 :locations="locations" @set-location="setLocation" return-object
                                 :key="typeof myLocation == 'object' ? myLocation.id : myLocation   "
                />
            </div>
            <div class="col-md-4">
                <form-date-picker name="alt_liturgy_date" label="Datum für Liturgische Informationen " v-model="service.alt_liturgy_date"
                                  help="Zeigt liturgische Information für ein abweichendes Datum an."/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <form-selectize name="controlled_access" :options="controlledAccessOptions"
                                label="Zugangsbeschränkung"
                                v-model="myService.controlled_access" />
            </div>
            <div v-if="myService.city.konfiapp_apikey" class="col-md-4">
                <konfi-app-event-type-select name="konfiapp_event_type" label="Veranstaltungsart in der KonfiApp"
                    :city="myService.city" v-model="service.konfiapp_event_type"
                                             :help="service.konfiapp_event_qr ? 'Ein QR-Code mit der ID '+service.konfiapp_event_qr+' wurde angelegt.' : 'Es wurde noch kein QR-Code angelegt.'"
                />
            </div>
            <div v-if="myService.city.communiapp_token" class="col-md-4">
                <form-group label="In der CommuniApp anzeigen ab" help="Leer lassen für den Standard (8 Tage vor Beginn)">
                    <date-picker v-model="myService.communiapp_listing_start" :config="myDatePickerConfig" />
                </form-group>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label>Sakramente</label>
                <form-check name="baptism" label="Dies ist ein Taufgottesdienst." v-model="service.baptism"/>
                <form-check name="eucharist" label="Dies ist ein Abendmahlsgottesdienst." v-model="service.eucharist"/>
            </div>
            <div class="col-md-4">
                <form-selectize name="related_cities[]" label="Auch in folgenden Kirchengemeinden anzeigen" v-model="service.related_cities"
                                :options="cities" multiple />
            </div>
        </div>
        <hr/>
        <form-check name="hidden" label="Diesen Gottesdienst in öffentlichen Listen nicht anzeigen."
                    v-model="service.hidden" />
        <div class="row">
            <div class="col-md-6">
                <form-input name="title" label="Abweichender Titel" v-model="service.title"
                            help="z.B. für öffentliche Listen"/>
            </div>
            <div class="col-md-6">
                <form-input name="description" label="Kurzbeschreibung" v-model="service.description"
                            help="Wird als Beschreibung in öffentlichen Listen angezeigt"/>
            </div>
        </div>
        <form-textarea name="internal_remarks" label="Interne Anmerkungen" v-model="service.internal_remarks"
                       help="Diese Anmerkungen werden nirgends veröffentlicht, sondern sind nur hier zu sehen."
                       pre-label="eye-slash"/>
        <div class="row">
            <div class="col-md-6">
                <tag-select name="tags" label="Kennzeichnungen" v-model="service.tags"
                                help="Kennzeichnungen z.B. für den Gemeindebrief" :tags="tags" />
            </div>
            <div class="col-md-6">
                <service-group-select name="service_groups" label="Dieser Gottesdienst gehört zu folgenden Gruppen"
                                v-model="service.service_groups" help="Gruppen z.B. für den Gemeindebrief"
                                :service-groups="serviceGroups" />
            </div>
        </div>
        <hr/>
        <div v-if="hasAnnouncements" class="alert alert-warning"><b>Bitte beachte:</b> Diesem Gottesdienst wurde eine Datei namens "Bekanntgaben" angehängt.
            Diese überschreibt die automatisch erstellten Bekanntmachungen. Änderungen an diesem Feld werden daher möglicherweise nicht berücksichtigt.</div>
        <form-textarea name="announcements" label="Zusätzliche Bekanntgaben" v-model="service.announcements"
                       help="Bekanntgaben, die über die automatisch erstellte Terminliste hinausgehen."/>
    </div>
</template>

<script>
import FormInput from "../../Ui/forms/FormInput";
import LocationSelect from "../../Ui/elements/LocationSelect";
import DaySelect from "../../Ui/elements/DaySelect";
import PeopleSelect from "../../Ui/elements/PeopleSelect";
import FormCheck from "../../Ui/forms/FormCheck";
import MinistryRow from "../../Ui/elements/MinistryRow";
import FormTextarea from "../../Ui/forms/FormTextarea";
import FormSelectize from "../../Ui/forms/FormSelectize";
import KonfiAppEventTypeSelect from "../../Ui/elements/KonfiAppEventTypeSelect";
import FormGroup from "../../Ui/forms/FormGroup";
import DatePickerConfig from "../../Ui/config/DatePickerConfig.js";
import TagSelect from "../../Ui/elements/TagSelect";
import ServiceGroupSelect from "../../Ui/elements/ServiceGroupSelect";
import FormDatePicker from "../../Ui/forms/FormDatePicker";

export default {
    name: "HomeTab",
    components: {
        FormDatePicker,
        ServiceGroupSelect,
        TagSelect,
        FormGroup,
        KonfiAppEventTypeSelect,
        MinistryRow,
        PeopleSelect,
        DaySelect,
        LocationSelect,
        FormInput,
        FormCheck,
        FormSelectize,
        FormTextarea,
    },
    props: {
        service: Object,
        locations: Array,
        tags: Array,
        serviceGroups: Array,
        cities: Array,
    },
    computed: {
        hasAnnouncements() {
            let found = false;
            this.service.attachments.forEach(attachment => {
                found = found || (attachment.title == 'Bekanntgaben');
            });
            return found;
        },
    },
    data() {
        let myDatePickerConfig =  {
            format: 'DD.MM.YYYY',
            locale: 'de',
            showClear: true,
        }
        let myService = this.service;
        myService.communiapp_listing_start = moment(this.service.communiapp_listing_start).format('DD.MM.YYYY');
        return {
            myService: this.service,
            myLocation: this.service.location || this.service.special_location,
            myDatePickerConfig: myDatePickerConfig,
            locationUpdating: false,
            controlledAccessOptions: [
                { id: 0, name: 'keine Zugangsbeschränkung'},
                { id: 1, name: '3G'},
                { id: 2, name: '2G'},
                { id: 3, name: '2G+'},
                { id: 4, name: 'Schnelltest für alle Besucher'},
                { id: 5, name: 'Schnelltest empfohlen'},
                { id: 6, name: 'Geschlossene Gruppe'},
            ],
            myDateTimePickerConfig: {
                locale: 'de',
                format: 'DD.MM.YYYY HH:mm',
                showClear: true,
                sideBySide: true,
            },
        }
    },
    methods: {
        setLocation(location) {
            this.locationUpdating = true;
            if(typeof location == 'object') {
                this.myLocation = location;
                this.myService.location_id = location.id;
                this.myService.location = location;
                this.myService.special_location = null;
            } else {
                this.myLocation = location;
                this.myService.special_location = location;
                this.myService.location_id = 0;
                this.myService.location = null;
            }
            this.locationUpdating = false;
        },
        setCommuniappListingStart(e) {
            this.service.communiapp_listing_start = e;
        }
    }
}
</script>

<style scoped>

</style>
