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
    <admin-layout title="Opferplan bearbeiten">
        <template slot="navbar-left">
            <nav-button type="primary" title="Opferplan anzeigen" icon="mdi mdi-table"
                        @click="showTable">Plan anzeigen</nav-button>
        </template>
        <form-selectize label="Opferplan für folgende Kirchengemeinden bearbeiten" :options="cities"
                        v-model="setup.cities" name="cities" multiple />
        <form-selectize label="Auf folgende Orte beschränken" placeholder="Leer lassen für alle Orte"
                        v-model="setup.locations" :options="locations" multiple />
        <form-group name="from" label="Gottesdienste anzeigen ab">
            <date-picker v-model="setup.from" :config="myDatePickerConfig"/>
        </form-group>
        <form-group name="to" label="Gottesdienste anzeigen bis">
            <date-picker v-model="setup.to" :config="myDatePickerConfig" />
        </form-group>
    </admin-layout>
</template>

<script>
import FormGroup from "../../../components/Ui/forms/FormGroup";
import FormSelectize from "../../../components/Ui/forms/FormSelectize";
import NavButton from "../../../components/Ui/buttons/NavButton";
import LocationSelect from "../../../components/Ui/elements/LocationSelect";
export default {
    name: "Setup",
    components: {LocationSelect, NavButton, FormSelectize, FormGroup},
    props: ['cities', 'locations'],
    data() {
        return {
            myDatePickerConfig: {
                locale: 'de',
                format: 'DD.MM.YYYY',
                showClear: true,
            },
            setup: {
                from: moment().startOf('year').format('DD.MM.YYYY'),
                to: moment().endOf('year').format('DD.MM.YYYY'),
                cities: this.cities.length ? [this.cities[0].id] : null,
                locations: [],
            }
        }
    },
    methods: {
        showTable() {
            this.$inertia.post(route('inputs.input', 'offerings'), this.setup);
        }
    }
}
</script>

<style scoped>

</style>
