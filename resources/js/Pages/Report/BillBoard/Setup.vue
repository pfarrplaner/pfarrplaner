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
    <admin-layout title="Kirchzettel erstellen">
        <template v-slot:navbar-left>
            <save-button label="Erstellen" title="Kirchzettel erstellen" @click="renderReport" />
        </template>
        <form method="post" :action="route('reports.render', {report: 'billBoard'})" ref="myForm">
            <form-csrf-token />
            <form-selectize name="city" label="Kirchzettel für folgende Kirchengemeinde erstellen" v-model="myCity" :options="cities" />
            <form-date-picker name="start" label="Gottesdienste von" v-model="myStart" iso-date />
            <form-check name="mixOutlook" label="Veranstaltungen aus dem Outlook-Kalender mit aufnehmen." />
            <form-check name="mixOP" label="Veranstaltungen aus dem Online Planer mit aufnehmen." />
        </form>
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
        let myStart = moment().startOf('isoWeek').add(6, 'days');

        return {
            myCity: this.cities.length ? this.cities[0].id : null,
            myStart,
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
