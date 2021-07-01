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
    <admin-layout title="Bestattung hinzufügen">
        <template slot="navbar-left">
            <button class="btn btn-primary" @click.prevent="createFuneral"
                    :disabled="!(funeral.city && funeral.location && funeral.date && funeral.name)">Erstellen</button>
        </template>
        <card>
            <card-body>
                <form-group label="Datum">
                    <date-picker :config="myDatePickerConfig" v-model="funeral.date" />
                </form-group>
                <form-selectize v-model="funeral.city" :options="cities" name="city"
                                id-key="id" title-key="name"
                                label="Kirchengemeinde" />
                <location-select v-model="funeral.location" :locations="locations" name="location"
                                label="Ort" @set-location="setLocation" />
                <form-input label="Verstorbene:r" placeholder="Nachname, Vorname" name="name"
                            v-model="funeral.name" />
            </card-body>
        </card>
    </admin-layout>
</template>

<script>
import Card from "../../components/Ui/cards/card";
import CardBody from "../../components/Ui/cards/cardBody";
import FormSelectize from "../../components/Ui/forms/FormSelectize";
import FormGroup from "../../components/Ui/forms/FormGroup";
import LocationSelect from "../../components/Ui/elements/LocationSelect";
import FormInput from "../../components/Ui/forms/FormInput";
export default {
    name: "FuneralWizard",
    components: {FormInput, LocationSelect, FormGroup, FormSelectize, CardBody, Card},
    props: ['cities', 'locations'],
    data() {
        return {
            myDatePickerConfig: {
                format: 'DD.MM.YYYY HH:mm',
                locale: 'de',
            },
            funeral: {
                date: null,
                city: null,
                location: null,
                name: null,
            }
        }
    },
    methods: {
        setLocation(e) {
            this.funeral.location = e;
        },
        createFuneral() {
            this.$inertia.post(route('funerals.wizard.save'), this.funeral);
        }
    }
}
</script>

<style scoped>

</style>
