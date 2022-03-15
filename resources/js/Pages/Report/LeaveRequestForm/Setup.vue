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
    <admin-layout title="Urlaubsantrag erstellen">
        <template v-slot:navbar-left>
            <save-button label="Erstellen" title="Urlaubsantrag erstellen" @click="renderReport" />
        </template>
        <form method="post" :action="route('reports.render', {report: 'leaveRequestForm'})" ref="myForm">
            <form-csrf-token />
            <form-selectize name="absence" label="Abwesenheitseintrag" v-model="myAbsence" :options="myAbsences" />
            <input type="hidden" name="absence" v-model="myAbsence" />
        </form>
    </admin-layout>
</template>

<script>
import SaveButton from "../../../components/Ui/buttons/SaveButton";
import FormSelectize from "../../../components/Ui/forms/FormSelectize";
import FormCsrfToken from "../../../components/Ui/forms/FormCsrfToken";
export default {
    name: "Setup",
    props: ['absences'],
    components: {FormCsrfToken, FormSelectize, SaveButton},
    data() {
        let myAbsences = this.absences;

        for (let key in myAbsences) {
            myAbsences[key].name = myAbsences[key].durationText + ' ' + myAbsences[key].reason;
        }

        return {
            myAbsence: myAbsences.length ? myAbsences[0].id : null,
            myAbsences,
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
