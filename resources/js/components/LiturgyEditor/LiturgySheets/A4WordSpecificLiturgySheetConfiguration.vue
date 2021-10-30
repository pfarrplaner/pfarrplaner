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
    <liturgy-sheet-configuration-form :service="service" :sheet="sheet">
        <p>Zusätzlich zur Tabelle mit dem Ablauf können die Texte für bestimmte Benutzer mit ausgegeben werden.</p>
        <fieldset>
            <div v-if="recipients.length == 0">
                Bitte warten, die Liste wird geladen... <span class="fa fa-spin fa-spinner"></span>
            </div>
            <input type="hidden" v-for="(recipient,recipientIndex) in myConfig.recipients" name="config[recipients][]" :value="recipient" />
            <form-selectize v-if="recipients.length > 0" multiple name="config[recipients][]"
                            label="Texte für folgende Benutzer ausgeben" v-model="myConfig.recipients"
                            :options="recipients" />
        </fieldset>
        <fieldset v-if="myConfig.recipients.length > 0">
            <legend>Folgende Inhalte mit einschließen:</legend>
            <form-check label="Übersichtstabelle" v-model="myConfig.includeTable" name="config[includeTable]"/>
            <form-check label="Individuelle Texte für die genannten Personen" v-model="myConfig.includeTexts" name="config[includeTexts]"/>
            <div v-if="myConfig.includeTexts">
                <form-check label="Lieder, Psalmen usw. immer mit einschließen" v-model="myConfig.includeSongTexts" name="config[includeSongTexts]"/>
                <form-check v-if="!myConfig.includeAllTexts" label="Zwischenüberschriften für alle Elemente im Ablauf" v-model="myConfig.includeAdditionalHeaders" name="config[includeAdditionalHeaders]"/>
                <form-check label="Texte der anderen Mitwirkenden" v-model="myConfig.includeAllTexts" name="config[includeAllTexts]"/>
                <form-check label="Bei Schriftlesungen Bibeltext mit einschließen" v-model="myConfig.includeFullReadings" name="config[includeFullReadings]"/>
            </div>
        </fieldset>
    </liturgy-sheet-configuration-form>
</template>

<script>
import LiturgySheetConfigurationForm from "./LiturgySheetConfigurationForm";
import FormCheck from "../../Ui/forms/FormCheck";
import FormSelectize from "../../Ui/forms/FormSelectize";
export default {
    name: "A4WordSpecificLiturgySheetConfiguration",
    components: {FormSelectize, FormCheck, LiturgySheetConfigurationForm},
    props: ['service', 'sheet'],
    async created() {
        this.recipients = (await axios.get(route('liturgy.recipients', this.service.slug))).data.recipients;
    },
    data() {
        return {
            myConfig: this.sheet.config,
            recipients: [],
        }
    }
}
</script>

<style scoped>

</style>
