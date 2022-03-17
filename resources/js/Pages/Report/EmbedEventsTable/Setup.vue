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
    <admin-layout title="HTML-Code für eine Veranstaltungstabelle erstellen">
        <template v-slot:navbar-left>
            <save-button label="Erstellen" title="HTML-Code für eine Veranstaltungstabelle erstellen"
                         @click="renderReport"/>
        </template>
        <form-selectize name="city" label="Tabelle für folgende Kirchengemeinde erstellen"
                        v-model="myForm.city" :options="cities"/>
        <form-input name="numDays" label="Anzahl der angezeigten Tage" type="number"
                    v-model="myForm.numDays" />
        <form-input name="cors-origin" label="Aufrufende Website"
                    placeholder="z.B. https://www.tailfingen-evangelisch.de"
                    v-model="myForm['cors-origin']" />
    </admin-layout>
</template>

<script>
import SaveButton from "../../../components/Ui/buttons/SaveButton";
import FormSelectize from "../../../components/Ui/forms/FormSelectize";
import FormCsrfToken from "../../../components/Ui/forms/FormCsrfToken";
import FormInput from "../../../components/Ui/forms/FormInput";
import FormDatePicker from "../../../components/Ui/forms/FormDatePicker";
import FormCheck from "../../../components/Ui/forms/FormCheck";

export default {
    name: "Setup",
    props: ['cities'],
    components: {FormCheck, FormDatePicker, FormInput, FormCsrfToken, FormSelectize, SaveButton},
    data() {
        let myStart = moment();
        let myEnd = moment().add(7, 'days');

        return {
            myForm: {
                city: this.cities.length ? this.cities[0].id : null,
                numDays: 8,
                'cors-origin': null,
            }
        }
    },
    methods: {
        renderReport() {
            this.$inertia.post(route('reports.render', 'embedEventsTable'), this.myForm);
        },
    }
}
</script>

<style scoped>

</style>
