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
    <admin-layout title="Urlaub bearbeiten">
        <template slot="navbar-left">
            <button @click="saveAbsence" class="btn btn-primary"  title="Urlaubseintrag speichern">
                <span class="d-inline d-md-none fa fa-save"></span>
                <span class="d-none d-md-inline">Speichern</span>
            </button>
            <button @click="deleteAbsence" class="btn btn-danger ml-1" title="Urlaubseintrag löschen">
                <span class="d-inline d-md-none fa fa-trash"></span>
                <span class="d-none d-md-inline">Löschen</span>
            </button>
            <button @click="requestForm" class="btn btn-light ml-1" title="Urlaubsantrag öffnen">
                Antragsformular
            </button>
        </template>
        <card>
            <card-header>Informationen zum Urlaub</card-header>
            <card-body>
                <date-range-input label="Zeitraum" :from="absence.from" :to="absence.to"
                                  @input="setDateRange"
                />
                <form-input label="Beschreibung" help="Grund der Abwesenheit" name="reason"
                            placeholder="z.B. Urlaub" v-model="absence.reason"/>
                <fake-table :columns="[5,6,1]" :headers="['Vertreter:in', 'Zeitraum', '']"
                            collapsed-header="Vertreter:innen" class="mt-3" :key="myAbsence.replacements.length">
                    <div class="row py-1 fake-table-row"
                         v-for="(replacement, replacementKey, replacementIndex) in myAbsence.replacements">
                        <div class="col-md-5">
                            <people-select :people="users" v-model="replacement.users"/>
                        </div>
                        <div class="col-md-6">
                            <date-range-input :from="replacement.from" :to="replacement.to" @input="setReplacementDateRange(replacement, $event)"/>
                        </div>
                        <div class="col-md-1 text-right">
                            <button class="btn btn-danger" @click.prevent="deleteReplacement(replacementKey)"
                                    title="Vertretung entfernen">
                                <span class="fa fa-trash"></span>
                            </button>
                        </div>
                    </div>
                </fake-table>
                <div>
                    <button class="btn btn-light" @click.prevent="addReplacement">Vertretung hinzufügen</button>
                </div>
                <hr />
                <form-textarea label="Notizen für die Vertretung" v-model="absence.replacement_notes" name="replacement_notes"/>
            </card-body>
        </card>
    </admin-layout>
</template>

<script>
import CardHeader from "../../components/Ui/cards/cardHeader";
import Card from "../../components/Ui/cards/card";
import FormInput from "../../components/Ui/forms/FormInput";
import CardBody from "../../components/Ui/cards/cardBody";
import DateRangeInput from "../../components/Ui/elements/DateRangeInput";
import FakeTable from "../../components/Ui/FakeTable";
import PeopleSelect from "../../components/Ui/elements/PeopleSelect";
import FormTextarea from "../../components/Ui/forms/FormTextarea";

export default {
    name: "AbsenceEditor",
    components: {FormTextarea, PeopleSelect, FakeTable, DateRangeInput, CardBody, FormInput, Card, CardHeader},
    props: ['absence', 'month', 'year', 'users'],
    data() {
        return {
            myAbsence: this.absence,
        }
    },
    methods: {
        setDateRange(e) {
            this.myAbsence.from = moment(e[0]).format('YYYY-MM-DD HH:mm:ss');
            this.myAbsence.to = moment(e[1]).format('YYYY-MM-DD HH:mm:ss');
        },
        setReplacementDateRange(replacement, e) {
            console.log('setReplacementDateRange', replacement, e);
            replacement.from = moment(e[0]).format('YYYY-MM-DD HH:mm:ss');
            replacement.to = moment(e[1]).format('YYYY-MM-DD HH:mm:ss');
        },
        addReplacement() {
            this.myAbsence.replacements.push({users: [], from: this.myAbsence.from, to: this.myAbsence.to});
        },
        deleteReplacement(index) {
            this.myAbsence.replacements.splice(index, 1);
        },
        saveAbsence() {
            var record = this.myAbsence;
            record.from = moment(this.myAbsence.from).format('DD.MM.YYYY');
            record.to = moment(this.myAbsence.to).format('DD.MM.YYYY');
            record.replacements.forEach(replacement => {
                replacement.from = moment(replacement.from).format('DD.MM.YYYY');
                replacement.to = moment(replacement.to).format('DD.MM.YYYY');
            });
            this.$inertia.patch(route('absences.update', {absence: this.absence.id}), record);
        },
        deleteAbsence() {
            this.$inertia.delete(route('absences.destroy', {absence: this.myAbsence.id}, {
                year: this.year, month: this.month
            }));
        },
        requestForm() {
            window.location.href = route('reports.render.get', {report: 'leaveRequestForm', absence: this.myAbsence.id});
        }
    }
}
</script>

<style scoped>
.row.fake-table-row {
    padding-left: 0;
    padding-right: 0;
}

.row.fake-table-row div {
    padding-left: 0;
}

.row.fake-table-row .col-md-1:last-child {
    padding-right: 0;
}
</style>
