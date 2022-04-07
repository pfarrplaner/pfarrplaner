<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2022 Christoph Fischer, https://christoph-fischer.org
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
    <admin-layout :title="'Neue Anmeldung für: '+service.titleText+' am '+moment(service.date).locale('de').format('DD.MM.YYYY')+' um '+moment(service.date).locale('de').format('HH:mm')+' Uhr'">
        <template v-slot:navbar-left>
            <save-button @click="register" />
        </template>
        <form-input name="name" label="Nachname" v-model="record.name" required/>
        <form-input name="first_name" label="Vorname" v-model="record.first_name" />
        <form-textarea name="contact" label="Kontaktdaten" v-model="record.contact" required />
        <form-input name="email" label="E-Mailadresse" v-model="record.email" />
        <form-input name="number" label="Anzahl Personen" v-model="record.number" type="number" />
        <hr />
        <h3>Manuelle Platzierung</h3>
        <p>Mit den folgenden Felder kannst du die automatische Platzierung dieser Person(en) verhindern und einen festen Platz zuweisen.
            Dabei können auch die normale Platzzahl oder die automatische Aufteilung einer Reihe überschrieben werden. Lasse diese Felder
            einfach leer, um automatisch eine Position für diese Anmeldung zu finden.</p>
        <form-input name="fixed_seat" label="Fester Sitzplatz" v-model="record.fixed_seat"
                    placeholder="z.B. 1 für ganze Reihe 1, 1A für Platz 1A" />
        <form-input name="override_seats" label="Abweichende Platzzahl an diesem Platz" v-model="record.override_seats" type="number" />
        <form-input name="override_split" label="Abweichende Aufteilung der betroffenen Reihe" v-model="record.override_split" />
    </admin-layout>
</template>

<script>
import SaveButton from "../../../components/Ui/buttons/SaveButton";
import FormInput from "../../../components/Ui/forms/FormInput";
import FormTextarea from "../../../components/Ui/forms/FormTextarea";
export default {
    name: "BookingEditor",
    components: {FormTextarea, FormInput, SaveButton},
    props: ['service', 'booking'],
    data() {
        return {
            record: this.booking,
        }
    },
    methods: {
        register() {
            if (this.record.id) {
                this.$inertia.patch(route('booking.update', this.record.id), this.record);
            } else {
                this.$inertia.post(route('booking.store'), this.record);
            }
        }
    }
}
</script>

<style scoped>

</style>
