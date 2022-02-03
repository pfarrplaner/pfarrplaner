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
            <button v-if="role == 'editor'" @click="saveAbsence" class="btn btn-primary"  title="Urlaubseintrag speichern  und zur Überprüfung absenden">
                <span class="d-inline d-md-none fa fa-save"></span>
                <span class="d-none d-md-inline">Zur Überprüfung absenden</span>
            </button>
            <button v-if="role == 'self-editor'" @click="saveAbsence" class="btn btn-primary"  title="Urlaubseintrag speichern">
                <span class="d-inline d-md-none fa fa-save"></span>
                <span class="d-none d-md-inline">Speichern</span>
            </button>
            <button v-if="role == 'admin'" @click="checkAndSave()" class="btn btn-success"  title="Urlaubseintrag als überprüft markieren und zur Genehmigung weiterleiten">
                <span class="d-inline d-md-none fa fa-save"></span>
                <span class="d-none d-md-inline">Zur Genehmigung weiterleiten</span>
            </button>
            <button v-if="role == 'approver'" @click="approveAndSave()" class="btn btn-success"  title="Urlaubseintrag genehmigen">
                <span class="d-inline d-md-none fa fa-save"></span>
                <span class="d-none d-md-inline">Genehmigen</span>
            </button>
            <button v-if="role == 'approver'" @click="returnAndSave()" class="btn btn-warning ml-1"  title="Urlaubseintrag zurück zur Überprüfung verweisen">
                <span class="d-inline d-md-none fa fa-save"></span>
                <span class="d-none d-md-inline">Erneut überprüfen lassen</span>
            </button>
            <button v-if="(role == 'editor') && (role == 'self-editor')" @click="deleteAbsence" class="btn btn-danger ml-1" title="Urlaubseintrag löschen">
                <span class="d-inline d-md-none fa fa-trash"></span>
                <span class="d-none d-md-inline">Löschen</span>
            </button>
            <button v-if="(role == 'admin') || (role=='approver')" @click="rejectAbsence" class="btn btn-danger ml-1" title="Urlaubsantrag ablehnen">
                <span class="d-inline d-md-none fa fa-trash"></span>
                <span class="d-none d-md-inline">Ablehnen</span>
            </button>
            <div class="dropdown" v-if="role == 'self-editor'">
                <button class="btn btn-light dropdown-toggle m-1" type="button" id="dropdownMenuButton"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                        title="Dokumente herunterladen">
                    <span class="fa fa-download"></span> Anträge
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="#" @click.prevent="leaveRequestForm">Urlaubsantrag</a>
                    <a class="dropdown-item" href="#" @click.prevent="travelRequestForm">Dienstreiseantrag</a>
                </div>
            </div>
        </template>
        <div v-if="myAbsence.workflow_status < 10" class="alert" :class="setApproved ? 'alert-success' : 'alert-danger'">
            <checked-process-item :check="myAbsence.workflow_status > 0"
                                  :negative="needsCheckText(myAbsence.user.vacation_admins, 'überprüft')">
                <template slot="positive">Der Urlaubsantrag wurde
                    <span v-if="myAbsence.checked_at">am {{ moment(myAbsence.checked_at).locale('de').format('DD.MM.YYYY') }} um {{ moment(myAbsence.checked_at).locale('de').format('HH:MM') }} Uhr</span>
                    <span v-if="myAbsence.checked_by">von {{ myAbsence.checked_by.name }}</span>
                    überprüft und zur Genehmigung weitergeleitet.
                </template>
            </checked-process-item>
            <div v-if="myAbsence.admin_notes && (role=='approver')"><div class="text-bold">Bemerkungen zur Überprüfung:</div><nl2br class="text-small">{{ myAbsence.admin_notes }}</nl2br></div>
            <form-textarea v-if="(myAbsence.workflow_status == 0) && (role=='admin')" name="admin_notes"
                           label="Bemerkungen zur Überprüfung" v-model="myAbsence.admin_notes" />
            <checked-process-item :check="myAbsence.workflow_status > 1"
                                  :negative="needsCheckText(myAbsence.user.vacation_approvers, 'genehmigt')">
                <template slot="positive">Der Urlaubsantrag wurde
                    <span v-if="myAbsence.approved_at">am {{ moment(myAbsence.approved_at).locale('de').format('DD.MM.YYYY') }} um {{ moment(myAbsence.approved_at).locale('de').format('HH:MM') }} Uhr</span>
                    <span v-if="myAbsence.approved_by">von {{ myAbsence.approved_by.name }}</span>
                    genehmigt.
                </template>
            </checked-process-item>
            <div v-if="(role != 'approver') && myAbsence.approver_notes"><div class="text-bold">Bemerkungen zur Genehmigung:</div><nl2br class="text-small">{{ myAbsence.approver_notes }}</nl2br></div>
            <form-textarea v-if="role == 'approver'" label="Bemerkungen zur Genehmigung" v-model="myAbsence.approver_notes" name="approver_notes" />
        </div>
        <card>
            <card-header>Informationen zum Urlaub</card-header>
            <card-body>
                <date-range-input label="Zeitraum" :from="myAbsence.from" :to="myAbsence.to"
                                  @input="setDateRange" :disabled="!mayEdit"
                />
                <form-input label="Beschreibung" help="Grund der Abwesenheit" name="reason"
                            placeholder="z.B. Urlaub" v-model="myAbsence.reason" :disabled="!mayEdit"/>
                <fake-table :columns="[5,6,1]" :headers="['Vertreter:in', 'Zeitraum', '']"
                            collapsed-header="Vertreter:innen" class="mt-3" :key="myAbsence.replacements.length">
                    <div class="row py-1 fake-table-row"
                         v-for="(replacement, replacementKey, replacementIndex) in myAbsence.replacements">
                        <div class="col-md-5">
                            <people-select :people="users" v-model="replacement.users"  :disabled="!mayEdit"/>
                        </div>
                        <div class="col-md-6">
                            <date-range-input :from="replacement.from" :to="replacement.to" @input="setReplacementDateRange(replacement, $event)"  :disabled="!mayEdit"/>
                        </div>
                        <div class="col-md-1 text-right">
                            <button class="btn btn-danger" @click.prevent="deleteReplacement(replacementKey)"
                                    title="Vertretung entfernen"  :disabled="!mayEdit">
                                <span class="fa fa-trash"></span>
                            </button>
                        </div>
                    </div>
                </fake-table>
                <div>
                    <button class="btn btn-light" @click.prevent="addReplacement"  :disabled="!mayEdit">Vertretung hinzufügen</button>
                </div>
                <hr />
                <form-textarea label="Notizen für die Vertretung" v-model="myAbsence.replacement_notes" name="replacement_notes"  :disabled="!mayEdit"/>
                <div class="row" v-if="role == 'self-editor'">
                    <div class="col-md-6">
                        <form-check label="Von der zuständigen Stelle genehmigt" v-model="setApproved" :is-checked-item="true"/>
                    </div>
                    <div class="col-md-6" v-if="setApproved">
                        <form-date-picker label="Datum der Genehmigung" v-model="absence.approved_at" :is-checked-item="true" />
                    </div>
                </div>
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
import CheckedProcessItem from "../../components/Ui/elements/CheckedProcessItem";
import FormCheck from "../../components/Ui/forms/FormCheck";
import FormDatePicker from "../../components/Ui/forms/FormDatePicker";

export default {
    name: "AbsenceEditor",
    components: {
        FormDatePicker,
        FormCheck,
        CheckedProcessItem,
        FormTextarea, PeopleSelect, FakeTable, DateRangeInput, CardBody, FormInput, Card, CardHeader},
    props: ['absence', 'month', 'year', 'users', 'mayCheck', 'mayApprove', 'maySelfAdminister'],
    data() {
        let role = 'readonly';
        let mayEdit = (this.mayCheck && this.absence.workflow_status == 0) || (this.mayApprove && this.absence.workflow_status == 1) || (this.absence.workflow_status <= 0);
        if (mayEdit) role = 'editor';
        if (mayEdit && this.mayCheck) role = 'admin';
        if (mayEdit && this.mayApprove) role = 'approver';
        if (this.maySelfAdminister && this.absence.workflow_status >= 10) {
            mayEdit = true;
            role = 'self-editor';
        }

        return {
            myAbsence: this.absence,
            setChecked: (this.absence.workflow_status == 1) || (this.absence.workflow_status == 2),
            setApproved: (this.absence.workflow_status == 2) || (this.absence.workflow_status == 11),
            mayEdit: mayEdit,
            role: role,
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

            if (!this.maySelfAdminister) {
                record.workflow_status = 0;
                if (this.setChecked) record.workflow_status = 1;
                if (this.setApproved) {
                    record.workflow_status = 2;
                    record.approved_at = record.approved_at || moment();
                }
            } else {
                record.workflow_status = 10;
                if (this.setApproved) record.workflow_status = 11;
            }
            this.$inertia.patch(route('absence.update', {absence: this.absence.id}), record);
        },
        returnAndSave() {
            this.setChecked = false;
            this.setApproved = false;
            this.admin_id = null;
            this.approver_id = null;
            this.saveAbsence();
        },
        checkAndSave() {
            this.setChecked = true;
            this.saveAbsence();
        },
        approveAndSave() {
            this.setChecked = true;
            this.setApproved = true;
            this.saveAbsence();
        },
        deleteAbsence() {
            this.$inertia.delete(route('absence.destroy', {
                absence: this.myAbsence.id,
                year: moment(this.myAbsence.from).format('YYYY'),
                month: moment(this.myAbsence.from).format('MM')
            }));
        },
        rejectAbsence() {
            this.$inertia.delete(route('absence.destroy', {
                absence: this.myAbsence.id,
                year: moment(this.myAbsence.from).format('YYYY'),
                month: moment(this.myAbsence.from).format('MM'),
                sendRejectionMail: true,
            }));
        },
        leaveRequestForm() {
            window.location.href = route('reports.render.get', {report: 'leaveRequestForm', absence: this.myAbsence.id});
        },
        travelRequestForm() {
            window.location.href = route('reports.render.get', {report: 'travelRequestForm', absence: this.myAbsence.id});
        },
        needsCheckText(people, step) {
            if (people.length == 1) {
                return 'Der Urlaubsantrag muss noch von '+people[0].name+' '+step+' werden.';
            } else {
                let p = [];
                people.forEach(person => {p.push(person.name)});
                return 'Der Urlaubsantrag muss noch von einer der folgenden Personen '+step+' werden: '+p.join(', ');
            }
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
