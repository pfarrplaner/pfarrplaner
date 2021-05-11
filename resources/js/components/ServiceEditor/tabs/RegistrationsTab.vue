<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
  - @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
  - @link https://github.com/potofcoffee/pfarrplaner
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
    <div class="reservations-tab">
        <div v-if="!myService.registration_active" class="alert alert-warning">
            Die Anmeldung für diesen Gottesdienst ist momentan nicht aktiv.
        </div>
        <div class="row">
            <div class="col-md-6">
                <form-check name="needs_reservations" label="Für diesen Gottesdienst ist eine Anmeldung notwendig."
                            v-model="myService.needs_reservations"/>
                <form-check name="registration_active" label="Online-Anmeldung aktiv"
                            v-model="myService.registration_active"/>
            </div>
            <div class="col-md-6">
                <form-input name="registration_phone" label="Nummer für die telefonische Anmeldung"
                            v-model="myService.registration_phone"/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <form-group label="Anmeldung online ab">
                    <date-picker type="datetime" v-model="myService.registration_online_start"
                                 :config="myDatePickerConfig"/>
                </form-group>
            </div>
            <div class="col-md-6">
                <form-group label="Anmeldung online bis">
                    <date-picker type="datetime" v-model="myService.registration_online_end"
                                 :config="myDatePickerConfig"/>
                </form-group>
            </div>
        </div>
        <form-input name="registration_max" v-model="myService.registration_max" type="number"
                    label="Anmeldungen begrenzen auf maximal"
                    help="Leer lassen, um die Anmeldungen nicht zu begrenzen"/>
        <div class="row" v-if="myService.location">
            <div class="col-md-4">
                <form-selectize name="exclude_sections" v-model="myService.exclude_sections"
                                :items="myService.location.seating_sections"
                                id-key="title" title-key="title" multiple
                                label="Folgende Bereiche sperren"/>
            </div>
            <div class="col-md-4">
                <seat-select name="exclude_places" v-model="myService.exclude_places"
                             :location="myService.location"
                             :exclude-sections="myService.exclude_sections"
                             multiple
                             label="Folgende Plätze sperren"/>
            </div>
            <div class="col-md-4">
                <seat-select name="reserved_places" v-model="myService.reserved_places"
                             :location="myService.location"
                             :exclude-sections="myService.exclude_sections"
                             multiple
                             label="Folgende Plätze zurückhalten" help="z.B. für Mesner, Ordner, usw."/>
            </div>
        </div>
        <hr/>
        <a class="btn btn-success" :href="route('seatfinder', myService.id)">Neue Anmeldung</a>
        <a class="btn btn-light" :href="route('booking.finalize', myService.id)"><span class="fa fa-file-pdf"></span>
            Anmeldeliste</a>
        <div class="bookings mt-3">
            <div class="alert alert-info" v-if="service.bookings.length == 0">
                Für diesen Gottesdienst gibt es noch keine Anmeldungen.
            </div>
            <div v-else>
                <p v-if="myService.seating.count">
                    <b>Zahl der angemeldeten Personen:</b> {{ myService.seating.count }}  (Raumnutzung: {{ Math.round(myService.seating.load) }}%)
                </p>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Anzahl Personen</th>
                        <th>Kontakt</th>
                        <th>Zeit der Anmeldung</th>
                        <th>Platz*</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(booking,bookingKey,bookingIndex) in service.bookings" :key="bookingKey">
                        <td>{{ booking.name }}<span v-if="booking.first_name">, {{ booking.first_name }}</span></td>
                        <td>{{ booking.number }}</td>
                        <td>
                            <div v-html="booking.contact.replace('\n', '<br />')"/>
                            {{ booking.email }}
                        </td>
                        <td>{{ moment(booking.created_at).locale('de-DE').format('LLL') }}</td>
                        <td>
                            <seat :seat="service.seating.grid[service.seating.list[booking.code]]" :booking="booking"/>
                            <button v-if="!booking.fixed_seat" class="btn btn-sm btn-light" title="Platz festlegen"
                                    @click.prevent="pinToSeat(booking)">
                                <span class="fa fa-thumbtack"></span>
                            </button>
                        </td>
                        <td>
                            <a class="btn btn-sm btn-light" title="Buchung bearbeiten"
                               :href="route('booking.edit', booking.id)">
                                <span class="fa fa-edit"></span>
                            </a>
                            <button class="btn btn-sm btn-danger" title="Buchung löschen"
                                    @click.prevent="deleteBooking(booking, bookingKey)">
                                <span class="fa fa-trash"></span>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <small><b>*</b> <i>Kursiv</i> angegebene Plätze sind vorläufig und können sich noch ändern.</small>
            </div>
        </div>
    </div>
</template>

<script>
import FormCheck from "../../Ui/forms/FormCheck";
import FormInput from "../../Ui/forms/FormInput";
import FormGroup from "../../Ui/forms/FormGroup";
import DatePickerConfig from "../../Ui/config/DatePickerConfig.js";
import FormSelectize from "../../Ui/forms/FormSelectize";
import SeatSelect from "../../Ui/elements/SeatSelect";
import Seat from "../../Ui/elements/seating/Seat";

export default {
    name: "RegistrationsTab",
    components: {Seat, SeatSelect, FormSelectize, FormGroup, FormInput, FormCheck},
    props: ['service'],
    data() {
        DatePickerConfig['format'] = 'DD.MM.YYYY HH:mm';
        return {
            myService: this.service,
            myDatePickerConfig: DatePickerConfig,
        }
    },
    methods: {
        pinToSeat(booking) {
            axios.post(route('booking.pin', booking.id), {
                service_id: this.service.id,
                booking_id: booking.id,
                fixed_seat: this.service.seating.list[booking.code],
                override_seats: '',
                override_split: '',
            }).then(response => {
                booking.fixed_seat = this.service.seating.list[booking.code];
            });
        },
        deleteBooking(booking, bookingKey) {
            if (confirm('Soll diese Anmeldung wirklich gelöscht werden?')) {
                axios.delete(route('booking.destroy', booking.id),)
                    .then(res => {
                        this.myService.bookings.splice(bookingKey, 1);
                    })
            }
        }
    }
}
</script>

<style scoped>

</style>
