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
    <admin-layout title="Planungstabelle konfigurieren">
        <template slot="navbar-left">
            <nav-button type="primary" title="Planungstabelle anzeigen" icon="mdi mdi-table"
                        :disabled="setup.ministries.length == 0"
                        :title="setup.ministries.length ? 'Planungstabelle erstellen und anzeigen' : 'Du musst mindestens einen Dienst auswählen, um eine Planungstabelle erstellen zu können.'"
                        @click="showTable">Planungstabelle anzeigen</nav-button>
        </template>
        <form-selectize label="Planungstabelle für folgende Kirchengemeinden erstellen" :options="cities"
                        v-model="setup.cities" name="cities" multiple />
        <form-selectize label="Auf folgende Orte beschränken" placeholder="Leer lassen für alle Orte"
                        v-model="setup.locations" :options="locations" multiple />
        <form-group name="from" label="Gottesdienste anzeigen ab">
            <date-picker v-model="setup.from" :config="myDatePickerConfig"/>
        </form-group>
        <form-group name="to" label="Gottesdienste anzeigen bis">
            <date-picker v-model="setup.to" :config="myDatePickerConfig" />
        </form-group>
        <form-selectize name="ministries" label="Folgende Dienste anzeigen"
                        :help="setup.ministries.length ? '' : 'Bitte wähle mindestens einen Dienst aus.'"
                        :options="myMinistries" v-model="setup.ministries" multiple/>
    </admin-layout>
</template>

<script>
import FormGroup from "../../../components/Ui/forms/FormGroup";
import FormSelectize from "../../../components/Ui/forms/FormSelectize";
import NavButton from "../../../components/Ui/buttons/NavButton";
import LocationSelect from "../../../components/Ui/elements/LocationSelect";
export default {
    name: "PlanningInputSetup",
    components: {LocationSelect, NavButton, FormSelectize, FormGroup},
    props: ['cities', 'ministries', 'locations'],
    data() {
        var myMinistries = [];
        for (const ministryKey in this.ministries) {
            myMinistries.push({ id: ministryKey, name: this.ministries[ministryKey]});
        }
        return {
            myDatePickerConfig: {
                locale: 'de',
                format: 'DD.MM.YYYY',
                showClear: true,
            },
            myMinistries: myMinistries,
            setup: {
                from: moment().format('DD.MM.YYYY'),
                to: moment().add(1, 'months').format('DD.MM.YYYY'),
                cities: this.cities.length ? [this.cities[0].id] : null,
                locations: [],
                ministries: ['P'],
            }
        }
    },
    methods: {
        showTable() {
            this.$inertia.post(route('inputs.input', 'planning'), this.setup);
        }
    }
}
</script>

<style scoped>

</style>
