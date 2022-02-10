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
    <admin-layout title="Abwesenheit bearbeiten">
        <template slot="navbar-left">
            <nav-button v-if="role == 'editor'" @click="saveAbsence" type="primary" icon="save" force-icon
                        title="Abwesenheitseintrag speichern  und zur Überprüfung absenden">Zur Überprüfung absenden
            </nav-button>
            <save-button v-if="role == 'self-editor'" @click="saveAbsence" class="btn btn-primary"
                         title="Abwesenheitseintrag speichern"/>
            <nav-button v-if="role == 'admin'" @click="checkAndSave()" type="success" icon="check" force-icon
                        title="Antrag als überprüft markieren und zur Genehmigung weiterleiten">Zur Genehmigung
                weiterleiten
            </nav-button>
            <nav-button v-if="role == 'approver'" @click="approveAndSave()" type="success" icon="check" force-icon
                        title="Antrag genehmigen">Genehmigen
            </nav-button>
            <nav-button v-if="role == 'approver'" @click="returnAndSave()" class="ml-1" type="warning" icon="undo"
                        force-icon
                        title="Abwesenheitseintrag zurück zur Überprüfung verweisen">Erneut überprüfen lassen
            </nav-button>
            <nav-button v-if="(role == 'admin') || (role=='approver')" @click="rejectAbsence" class="ml-1"
                        title="Antrag ablehnen" type="danger" icon="times" force-icon>Ablehnen
            </nav-button>
            <nav-button v-if="mayDelete" @click="deleteAbsence" type="danger" icon="trash"
                        class="ml-1" title="Abwesenheitseintrag löschen" force-icon>Löschen
            </nav-button>
        </template>
        <template slot="before-flash">
            <div v-if="myAbsence.workflow_status < 10" class="alert"
                 :class="setApproved ? 'alert-success' : 'alert-danger'">
                <checked-process-item :check="myAbsence.workflow_status > 0"
                                      :negative="needsCheckText(myAbsence.user.vacation_admins, 'überprüft')">
                    <template slot="positive">Der Urlaubsantrag wurde
                        <span v-if="myAbsence.checked_at">am {{
                                moment(myAbsence.checked_at).locale('de').format('DD.MM.YYYY')
                            }} um {{ moment(myAbsence.checked_at).locale('de').format('HH:MM') }} Uhr</span>
                        <span v-if="myAbsence.checked_by">von {{ myAbsence.checked_by.name }}</span>
                        überprüft und zur Genehmigung weitergeleitet.
                    </template>
                </checked-process-item>
                <div v-if="myAbsence.admin_notes && (role=='approver')">
                    <div class="text-bold">Bemerkungen zur Überprüfung:</div>
                    <nl2br class="text-small">{{ myAbsence.admin_notes }}</nl2br>
                </div>
                <form-textarea v-if="(myAbsence.workflow_status == 0) && (role=='admin')" name="admin_notes"
                               label="Bemerkungen zur Überprüfung" v-model="myAbsence.admin_notes"/>
                <checked-process-item :check="myAbsence.workflow_status > 1"
                                      :negative="needsCheckText(myAbsence.user.vacation_approvers, 'genehmigt')">
                    <template slot="positive">Der Urlaubsantrag wurde
                        <span v-if="myAbsence.approved_at">am {{
                                moment(myAbsence.approved_at).locale('de').format('DD.MM.YYYY')
                            }} um {{ moment(myAbsence.approved_at).locale('de').format('HH:MM') }} Uhr</span>
                        <span v-if="myAbsence.approved_by">von {{ myAbsence.approved_by.name }}</span>
                        genehmigt.
                    </template>
                </checked-process-item>
                <div v-if="(role != 'approver') && myAbsence.approver_notes">
                    <div class="text-bold">Bemerkungen zur Genehmigung:</div>
                    <nl2br class="text-small">{{ myAbsence.approver_notes }}</nl2br>
                </div>
                <form-textarea v-if="role == 'approver'" label="Bemerkungen zur Genehmigung"
                               v-model="myAbsence.approver_notes" name="approver_notes"/>
            </div>
        </template>


        <template slot="tab-headers">
            <tab-headers>
                <tab-header id="home" :active-tab="activeTab" title="Abwesenheit" />
                <tab-header v-if="myAbsence.user.needs_replacement" id="replacement" :active-tab="activeTab" title="Vertretung" />
                <tab-header id="attachments" :active-tab="activeTab" title="Dateien" :count="fileCount" />
            </tab-headers>
        </template>


        <tabs>
            <tab id="home" :active-tab="activeTab">
                <date-range-input label="Zeitraum" :from="myAbsence.from" :to="myAbsence.to"
                                  @input="setDateRange" :disabled="!mayEdit"
                />
                <form-input label="Beschreibung" help="Grund der Abwesenheit" name="reason"
                            placeholder="z.B. Urlaub" v-model="myAbsence.reason" :disabled="!mayEdit"/>
                <form-check label="Es handelt sich um eine Krankmeldung" name="sick_days" v-model="myAbsence.sick_days"
                            :disabled="!mayEdit"/>
                <hr/>
                <form-textarea label="Interne Anmerkungen" v-model="myAbsence.internal_notes"
                               name="internal_notes" :disabled="!mayEdit"/>
                <div class="row" v-if="role == 'self-editor'">
                    <div class="col-md-6">
                        <form-check label="Von der zuständigen Stelle genehmigt" v-model="setApproved"
                                    :is-checked-item="true"/>
                    </div>
                    <div class="col-md-6" v-if="setApproved">
                        <form-date-picker label="Datum der Genehmigung" v-model="absence.approved_at"
                                          :is-checked-item="true"/>
                    </div>
                </div>

            </tab>
            <tab v-if="myAbsence.user.needs_replacement" id="replacement" :active-tab="activeTab">
                <fake-table :columns="[5,6,1]" :headers="['Vertreter:in', 'Zeitraum', '']"
                            collapsed-header="Vertreter:innen" class="mt-3" :key="myAbsence.replacements.length">
                    <div class="row py-1 fake-table-row"
                         v-for="(replacement, replacementKey, replacementIndex) in myAbsence.replacements">
                        <div class="col-md-5">
                            <people-select :people="users" v-model="replacement.users" :disabled="!mayEdit"/>
                        </div>
                        <div class="col-md-6">
                            <date-range-input :from="replacement.from" :to="replacement.to"
                                              @input="setReplacementDateRange(replacement, $event)"
                                              :disabled="!mayEdit"/>
                        </div>
                        <div class="col-md-1 text-right">
                            <button class="btn btn-danger" @click.prevent="deleteReplacement(replacementKey)"
                                    title="Vertretung entfernen" :disabled="!mayEdit">
                                <span class="fa fa-trash"></span>
                            </button>
                        </div>
                    </div>
                </fake-table>
                <div>
                    <button class="btn btn-light" @click.prevent="addReplacement" :disabled="!mayEdit">Vertretung
                        hinzufügen
                    </button>
                </div>
                <hr/>
                <form-textarea label="Notizen für die Vertretung" v-model="myAbsence.replacement_notes"
                               name="replacement_notes" :disabled="!mayEdit"/>
            </tab>
            <tab id="attachments" :active-tab="activeTab">

                <h3>Angehängte Dateien</h3>
                <attachment-list v-model="myAbsence.attachments" delete-route-name="absence.detach"
                                 :parent-object="myAbsence" parent-type="absence"
                                 :prevent-empty-list-message="fileCount > 0"
                                 :key="myAbsence.attachments.length"/>
                <fake-attachment @download="leaveRequestForm"
                                 title="Urlaubsantrag" extension="pdf"
                                 icon="fa-file-pdf" size="ca. 120 kB"/>
                <fake-attachment @download="travelRequestForm"
                                 title="Dienstreiseantrag" extension="pdf"
                                 icon="fa-file-pdf" size="ca. 120 kB"/>

                <hr/>
                <h3>Dateien hinzufügen.</h3>
                <form-file-uploader :parent="myAbsence"
                                    :upload-route="route('absence.attach', this.myAbsence.id)"
                                    v-model="myAbsence.attachments"/>


            </tab>
        </tabs>

        <div class="alert alert-light text-small">
            <div class="text-bold"><span class="fa fa-info"></span> Datenschutzhinweis</div>
            <div v-if="visibleTo.length == 1">
                Auf die Informationen in diesem Formular und die angehängten Dateien kannst nur du zugreifen.
            </div>
            <div v-else>
                Auf die Informationen in diesem Formular können folgende Benutzer zugreifen:
                {{ visibleTo.map(x => x.name).join(', ') }}
            </div>
        </div>



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
import NavButton from "../../components/Ui/buttons/NavButton";
import SaveButton from "../../components/Ui/buttons/SaveButton";
import {clone} from "lodash"
import TabHeaders from "../../components/Ui/tabs/tabHeaders";
import TabHeader from "../../components/Ui/tabs/tabHeader";
import Tabs from "../../components/Ui/tabs/tabs";
import Tab from "../../components/Ui/tabs/tab";
import AttachmentList from "../../components/Ui/elements/AttachmentList";
import FakeAttachment from "../../components/Ui/elements/FakeAttachment";
import FormFileUploader from "../../components/Ui/forms/FormFileUploader";

export default {
    name: "AbsenceEditor",
    components: {
        FormFileUploader,
        FakeAttachment,
        AttachmentList,
        Tab,
        Tabs,
        TabHeader,
        TabHeaders,
        SaveButton,
        NavButton,
        FormDatePicker,
        FormCheck,
        CheckedProcessItem,
        FormTextarea, PeopleSelect, FakeTable, DateRangeInput, CardBody, FormInput, Card, CardHeader
    },
    props: ['absence', 'month', 'year', 'users', 'mayCheck', 'mayApprove', 'maySelfAdminister', 'activeTab'],
    computed: {
        fileCount() {
            let count = this.myAbsence.attachments.length;
            if (this.role = 'self-editor') count +=2;
            return count;
        },
        visibleTo() {
            return [...[this.myAbsence.user], ...this.myAbsence.user.vacation_admins, ...this.myAbsence.user.vacation_approvers];
        },
    },
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
            editingUser: this.$page.props.currentUser.data.id,
            isForeignEditor: this.hasPermission('fremden-urlaub-bearbeiten') && (this.$page.props.currentUser.data.id != this.absence.user.id),
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
        getDMYDate(d) {
            return d.length == 10 ? d : moment(d).format('DD.MM.YYYY');
        },
        getUpdateRecord() {
            var record = clone(this.myAbsence);
            record.from = this.getDMYDate(record.from);
            record.to = this.getDMYDate(record.to);
            record.replacements.forEach(replacement => {
                replacement.from = this.getDMYDate(replacement.from);
                replacement.to = this.getDMYDate(replacement.to);
            });

            if (!this.maySelfAdminister) {
                record.workflow_status = 0;
                if (this.setChecked) record.workflow_status = 1;
                if (this.setApproved) {
                    record.workflow_status = 2;
                    if (record.approved_at && (record.approved_at.length != 10)) {
                        record.approved_at = moment(record.approved_at).format('DD.MM.YYYY')
                    } else {
                        record.approved_at = record.approved_at || moment().format('DD.MM.YYYY');
                    }
                }
            } else {
                record.workflow_status = 10;
                if (this.setApproved) record.workflow_status = 11;
                if (record.approved_at && (record.approved_at.length != 10)) {
                    record.approved_at = moment(record.approved_at).format('DD.MM.YYYY')
                } else {
                    record.approved_at = record.approved_at || null;
                }
            }
            if (record.sick_days && (record.reason == 'Urlaub')) record.reason = 'Krankheit';
            return record;
        },
        saveAbsence() {
            this.$inertia.patch(route('absence.update', {absence: this.absence.id}), this.getUpdateRecord());
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
            if (this.confirmDelete()) this.$inertia.delete(route('absence.destroy', {
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
            if (!confirm('Deine Eingaben werden gespeichert, bevor das Antragsformular heruntergeladen wird.')) return;
            this.$inertia.patch(route('absence.update', {
                absence: this.absence.id,
                redirectTo: route('reports.render.get', {
                    report: 'leaveRequestForm',
                    absence: this.myAbsence.id
                })
            }), this.getUpdateRecord());
        },
        travelRequestForm() {
            if (!confirm('Deine Eingaben werden gespeichert, bevor das Antragsformular heruntergeladen wird.')) return;
            this.$inertia.patch(route('absence.update', {
                absence: this.absence.id,
                redirectTo: route('reports.render.get', {
                    report: 'travelRequestForm',
                    absence: this.myAbsence.id
                })
            }), this.getUpdateRecord());
        },
        needsCheckText(people, step) {
            if (people.length == 1) {
                return 'Der Urlaubsantrag muss noch von ' + (people[0].id == this.editingUser ? 'dir' : people[0].name) + ' ' + step + ' werden.';
            } else {
                let p = [];
                people.forEach(person => {
                    p.push(person.name)
                });
                return 'Der Urlaubsantrag muss noch von einer der folgenden Personen ' + step + ' werden: ' + p.join(', ');
            }
        },
        mayDelete() {
            return ((this.role == 'editor') && this.absence.workflow_status == 0)
                || (this.role == 'self-editor')
                || (this.isForeignEditor && (this.absence.workflow_status == 0))
                || (this.mayCheck) || (this.mayApprove);
        },
        confirmDelete() {
            if (this.absence.workflow_status > 0) {
                return confirm('Diese Aktion löscht den Abwesenheitseintrag komplett, ohne irgendjemanden zu benachrichtigen. Wenn eine Benachrichtigung versandt werden soll, benutze die Schaltfläche "Ablehnen".');
            }
            return true;
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
