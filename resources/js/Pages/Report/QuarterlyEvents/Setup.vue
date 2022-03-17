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
    <admin-layout title="Quartalsprogramm erstellen">
        <template v-slot:navbar-left>
            <save-button label="Erstellen" title="Quartalsprogramm erstellen" @click="renderReport" />
        </template>
        <form method="post" :action="route('reports.render', {report: 'quarterlyEvents'})" ref="myForm">
            <form-csrf-token />
            <form-input name="title" label="Titel" v-model="myTitle" />
            <form-selectize name="location" label="Liste für folgenden Veranstaltungsort" :options="locations" v-model="myLocation"/>
            <form-selectize name="quarter" label="Quartal" :options="quarters" v-model="myQuarter" />
            <div class="form-group"> <!-- Radio group !-->
                <label class="control-label">Folgende Informationen mit einbeziehen:</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="includePastor" value="1" checked >
                    <label class="form-check-label" for="includePastor">Pfarrer*in</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="includeOrganist" value="1" checked >
                    <label class="form-check-label" for="includeOrganist">Organist*in</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="includeSacristan" value="1" checked >
                    <label class="form-check-label" for="includeSacristan">Mesner*in</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="includeDescription" value="1" checked >
                    <label class="form-check-label" for="includeDescription">Besonderheiten</label>
                </div>
            </div>
            <form-textarea name="notes1" label="Hinweise vor der Übersicht der Gottesdienste" v-model="myNotes1" />
            <form-textarea name="notes2" label="Hinweise nach der Übersicht der Gottesdienste" v-model="myNotes2" />
            <form-check name="includeContact" v-model="myIncludeContact" label="Meine Kontaktdaten für weitere Informationen mit aufnehmen" />
        </form>
    </admin-layout>
</template>

<script>
import SaveButton from "../../../components/Ui/buttons/SaveButton";
import FormSelectize from "../../../components/Ui/forms/FormSelectize";
import FormCsrfToken from "../../../components/Ui/forms/FormCsrfToken";
import FormInput from "../../../components/Ui/forms/FormInput";
import FormDatePicker from "../../../components/Ui/forms/FormDatePicker";
import FormTextarea from "../../../components/Ui/forms/FormTextarea";
import FormCheck from "../../../components/Ui/forms/FormCheck";
export default {
    name: "Setup",
    props: ['locations'],
    components: {FormCheck, FormTextarea, FormDatePicker, FormInput, FormCsrfToken, FormSelectize, SaveButton},
    data() {
        let quarters = [];
        for (let year=moment().year(); year<=moment().year()+4; year++) {
            for (let q = 1; q <= 4; q++) {
                let qStart = String(year) + '-' + String((q - 1) * 3 + 1).padStart(2,'0') + '-01'
                if (moment(qStart) >= moment()) quarters.push({
                    id: qStart,
                    name: String(year) + ' / ' + String(q),
                });
            }
        }

        return {
            myTitle: this.$page.props.settings.quarterly_events_report_title || '',
            myLocation: this.locations.length ? this.locations[0].id : null,
            myUser: this.$page.props.currentUser.data.id,
            myStart: moment(),
            myEnd: moment().endOf('year'),
            quarters,
            myQuarter: quarters[0].id,
            myNotes1: this.$page.props.settings.quarterly_events_report_notes1 || '',
            myNotes2: this.$page.props.settings.quarterly_events_report_notes2 || '',
            myIncludeContact: true,
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
