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
    <admin-layout title="Trauung hinzufügen">
        <template slot="navbar-left">
            <button class="btn btn-primary" @click.prevent="createWedding"
                    :disabled="!(wedding.city && wedding.location && wedding.date && wedding.spouse1_name && wedding.spouse2_name)">Erstellen
            </button>
        </template>
        <form-group label="Datum">
            <date-picker :config="myDatePickerConfig" v-model="wedding.date"/>
        </form-group>
        <form-selectize v-model="wedding.city" :options="cities" name="city"
                        id-key="id" title-key="name"
                        label="Kirchengemeinde"/>
        <location-select v-model="wedding.location" :locations="locations" name="location"
                         label="Ort" @set-location="setLocation"/>
        <form-input label="1. Ehepartner:in" placeholder="Nachname, Vorname" name="spouse1_name"
                    v-model="wedding.spouse1_name"/>
        <form-input label="2. Ehepartner:in" placeholder="Nachname, Vorname" name="spouse2_name"
                    v-model="wedding.spouse2_name"/>
        <form-group label="Pfarrer*in">
            <people-select :people="people" v-model="wedding.pastor"/>
        </form-group>
    </admin-layout>
</template>

<script>
import FormSelectize from "../../components/Ui/forms/FormSelectize";
import FormGroup from "../../components/Ui/forms/FormGroup";
import LocationSelect from "../../components/Ui/elements/LocationSelect";
import FormInput from "../../components/Ui/forms/FormInput";
import PeopleSelect from "../../components/Ui/elements/PeopleSelect";

export default {
    name: "WeddingWizard",
    components: {PeopleSelect, FormInput, LocationSelect, FormGroup, FormSelectize},
    props: ['cities', 'locations', 'people', 'user'],
    data() {
        return {
            myDatePickerConfig: {
                format: 'DD.MM.YYYY HH:mm',
                locale: 'de',
            },
            wedding: {
                date: null,
                city: null,
                location: null,
                spouse1_name: null,
                spouse2_name: null,
                pastor: [this.user.id],
            },
        }
    },
    methods: {
        setLocation(e) {
            this.wedding.location = e;
        },
        createWedding() {
            this.$inertia.post(route('weddings.wizard.save'), this.wedding);
        }
    }
}
</script>

<style scoped>

</style>
