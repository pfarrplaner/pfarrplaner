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
    <admin-layout title="Gottesdienstliste für den Gemeindebrief erstellen">
        <template v-slot:navbar-left>
            <save-button label="Erstellen" title="Gottesdienstliste für den Gemeindebrief erstellen" @click="renderReport" />
        </template>
        <form method="post" :action="route('reports.render', {report: 'bulletin'})" ref="myForm">
            <form-csrf-token />
            <form-selectize name="includeCities[]" label="Folgende Kirchengemeinden mit einbeziehen"
                            v-model="myCities" :options="cities" multiple/>
            <form-date-picker name="start" label="Gottesdienste von" v-model="myStart" iso-date />
            <form-date-picker name="end" label="Bis" v-model="myEnd" iso-date />
            <div class="row">
                <div class="col-6" v-for="format in formats">
                    <label>
                        <input type="radio" name="format" :value="format" v-model="myFormat" />
                        Format: <b>{{ format }}</b><br/>
                    </label><br />
                    <img class="img-fluid" :src="'/img/bulletin/'+format+'.jpg'"/>
                </div>
            </div>
        </form>
    </admin-layout>
</template>

<script>
import SaveButton from "../../../components/Ui/buttons/SaveButton";
import FormSelectize from "../../../components/Ui/forms/FormSelectize";
import FormCsrfToken from "../../../components/Ui/forms/FormCsrfToken";
import FormInput from "../../../components/Ui/forms/FormInput";
import FormDatePicker from "../../../components/Ui/forms/FormDatePicker";
export default {
    name: "Setup",
    props: ['cities', 'formats'],
    components: {FormDatePicker, FormInput, FormCsrfToken, FormSelectize, SaveButton},
    data() {
        return {
            myCities: this.cities.length ? [this.cities[0].id] : null,
            myStart: moment().date(1).add(1, 'months'),
            myEnd: moment().date(1).add(4, 'months').subtract(1, 'day'),
            myFormat: this.formats.length ? this.formats[0] : null,
        }
    },
    methods: {
        renderReport() {
            this.$refs.myForm.submit();
        },
    }
}
</script>

<style scoped>

</style>
