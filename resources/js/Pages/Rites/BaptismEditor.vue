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
    <admin-layout :title="'Taufe von '+baptism.candidate_name">
        <template slot="navbar-left">
            <button class="btn btn-primary" @click.prevent="saveBaptism" title="Speichern">
                <span class="d-inline d-md-none fa fa-save"></span> <span class="d-none d-md-inline">Speichern</span>
            </button>&nbsp;
            <button class="btn btn-danger" @click.prevent="deleteBaptism" title="Löschen">
                <span class="d-inline d-md-none fa fa-trash"></span> <span class="d-none d-md-inline">Löschen</span>
            </button>
        </template>
        <template slot="tab-headers">
            <tab-headers>
                <tab-header title="Allgemeines" id="home" :active-tab="activeTab" :is-checked-item="true"
                            :check-value="(!myBaptism.needs_dimissorial) || (myBaptism.dimissorial_received)"/>
                <tab-header title="Vorbereitung" id="prep" :active-tab="activeTab" :is-checked-item="true"
                            :check-value="prepChecks()"/>
                <tab-header title="Dateien" id="attachments" :active-tab="activeTab"
                            :count="myBaptism.attachments.length"/>
            </tab-headers>
        </template>
        <tabs>
            <tab id="home" :active-tab="activeTab">
                <fieldset>
                    <legend>Gottesdienst</legend>
                    <form-selectize label="Taufgottesdienst"
                                    name="service_id"
                                    :options="services"
                                    id-key="id" title-key="name"
                                    help="Leer lassen, um dies als Taufanfrage einzuordnen"
                                    :settings="myServicePickerConfig"
                                    v-model="myBaptism.service_id"/>
                    <form-selectize label="Kirchengemeinde" :options="cities" v-model="myBaptism.city_id"
                                    id-key="id" title-key="name"
                                    name="city_id"/>
                </fieldset>
                <fieldset>
                    <legend>Täufling</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <form-input name="candidate_name"
                                        label="Name des Täuflings" v-model="myBaptism.candidate_name"/>
                        </div>
                        <div class="col-md-6">
                            <form-group label="Zu verwendendes Pronomen">
                                <div>
                                    <div class="form-check-inline"
                                         v-for="(pronounSet, pronounSetIndex) in pronounSets"
                                         :key="'pronouns_'+pronounSet.key">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input"
                                                   v-model="myBaptism.pronoun_set"
                                                   :value="pronounSet.key">{{ pronounSet.label }}
                                        </label>
                                    </div>
                                </div>
                            </form-group>
                        </div>
                    </div>
                    <form-input label="Adresse" v-model="myBaptism.candidate_address"/>
                    <div class="row">
                        <div class="col-md-6">
                            <form-input name="candidate_zip" label="PLZ" v-model="myBaptism.candidate_zip"/>
                        </div>
                        <div class="col-md-6">
                            <form-input name="candidate_city" label="Ort" v-model="myBaptism.candidate_city"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <form-input name="candidate_phone" label="Telefon" v-model="myBaptism.candidate_phone"/>
                        </div>
                        <div class="col-md-6">
                            <form-input name="candidate_email" label="E-Mailadresse" v-model="myBaptism.candidate_email"
                                        type="email"/>
                        </div>
                    </div>
                </fieldset>
                <dimissorial-form-part :parent="myBaptism"/>
            </tab>
            <tab id="prep" :active-tab="activeTab">
                <fieldset id="fsPrep">
                    <legend>Erstkontakt</legend>
                    <div class="row">
                        <div class="col-md-4">
                            <form-input name="first_contact_with"
                                        label="Erstkontakt mit" v-model="myBaptism.first_contact_with"
                                        is-checked-item="1"/>
                        </div>
                        <div class="col-md-4">
                            <form-group label="Datum" is-checked-item="1" :value="myBaptism.first_contact_on"
                                        name="first_contact_on">
                                <date-picker v-model="myBaptism.first_contact_on" :config="myDatePickerConfig"/>
                            </form-group>
                        </div>
                        <div class="col-md-4">
                            <form-group label="Taufgespräch" is-checked-item="1" :value="myBaptism.appointment"
                                        name="candidate_appointment">
                                <date-picker v-model="myBaptism.appointment" :config="myDateTimePickerConfig"/>
                            </form-group>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Wichtige Informationen</legend>
                    <form-input name="text" label="Taufspruch" v-model="myBaptism.text" is-checked-item="1"/>
                    <form-textarea name="notes" label="Notizen aus dem Taufgespräch" v-model="myBaptism.notes"/>
                </fieldset>
                <fieldset>
                    <legend>Unterlagen</legend>
                    <div v-if="!hasRegistrationForm" class="mb-3">
                        <p>Wenn das Anmeldeformular in AHAS Online erstellt wurde, kannst du es hier hochladen:</p>
                        <form-file-uploader :parent="myBaptism"
                                            :upload-route="route('baptism.attach', this.myBaptism.id)"
                                            title="Anmeldeformular"
                                            v-model="myBaptism.attachments"/>
                    </div>
                    <div v-else>
                        <checked-process-item check="1"
                                              positive="Anmeldedaten aufgenommen und Anmeldeformular erstellt"/>
                        <form-check label="Anmeldung unterschrieben" v-model="myBaptism.signed" name="signed"
                                    is-checked-item="1"/>
                        <form-check label="Urkunden erstellt" v-model="myBaptism.docs_ready" name="ready"
                                    is-checked-item="1"/>
                        <form-input label="Urkunden hinterlegt" v-model="myBaptism.docs_where" name="docs_where"
                                    class="mt-3"
                                    help="Wo sind die Unterlagen hinterlegt?"
                                    is-checked-item="1"/>
                    </div>
                    <form-check name="processed" label="Kirchenbucheintrag abgeschlossen" v-model="myBaptism.processed"
                                is-checked-item/>
                </fieldset>
            </tab>
            <tab id="attachments" :active-tab="activeTab">
                <fieldset>
                    <legend>Angehängte Dateien</legend>
                    <attachment-list v-model="myBaptism.attachments" delete-route-name="baptism.detach"
                                     :parent-object="myBaptism" parent-type="baptism"
                                     :key="myBaptism.attachments.length"/>
                </fieldset>
                <fieldset>
                    <legend>Dateien hinzufügen</legend>
                    <form-file-uploader :parent="myBaptism"
                                        :upload-route="route('baptism.attach', this.myBaptism.id)"
                                        v-model="myBaptism.attachments"/>
                    <div class="mt-2"><small>Das Anmeldeformular zur Taufe bitte nicht hier, sondern im Register
                        "Vorbereitung" hochladen.</small></div>
                </fieldset>
            </tab>
        </tabs>
    </admin-layout>

</template>

<script>
import TabHeaders from "../../components/Ui/tabs/tabHeaders";
import TabHeader from "../../components/Ui/tabs/tabHeader";
import FormGroup from "../../components/Ui/forms/FormGroup";
import FormInput from "../../components/Ui/forms/FormInput";
import Tab from "../../components/Ui/tabs/tab";
import Tabs from "../../components/Ui/tabs/tabs";
import FormCheck from "../../components/Ui/forms/FormCheck";
import FormSelectize from "../../components/Ui/forms/FormSelectize";
import AttachmentList from "../../components/Ui/elements/AttachmentList";
import FormFileUploader from "../../components/Ui/forms/FormFileUploader";
import CheckedProcessItem from "../../components/Ui/elements/CheckedProcessItem";
import FormTextarea from "../../components/Ui/forms/FormTextarea";
import DimissorialFormPart from "../../components/RiteEditors/DimissorialFormPart";

export default {
    name: "BaptismEditor",
    components: {
        DimissorialFormPart,
        FormTextarea,
        CheckedProcessItem,
        FormFileUploader,
        AttachmentList,
        FormSelectize,
        FormCheck, Tabs, Tab, FormInput, FormGroup, TabHeader, TabHeaders,
    },
    props: ['baptism', 'services', 'cities', 'pronounSets'],
    data() {
        var myBaptism = this.baptism;
        myBaptism.first_contact_on = myBaptism.first_contact_on ? moment(myBaptism.first_contact_on).format('DD.MM.YYYY') : null;
        myBaptism.appointment = myBaptism.appointment ? moment(myBaptism.appointment).format('DD.MM.YYYY HH:mm') : null;
        return {
            myDatePickerConfig: {
                locale: 'de',
                format: 'DD.MM.YYYY',
                showClear: true,
            },
            myDateTimePickerConfig: {
                locale: 'de',
                format: 'DD.MM.YYYY HH:mm',
                showClear: true,
                sideBySide: true,
            },
            myServicePickerConfig: {
                searchField: ['name'],
                allowEmptyOption: true,
                showEmptyOptionInDropdown: true,
                emptyOptionLabel: 'noch nicht gewählt (Taufanfrage)',
                optgroupField: 'category',
                optgroupLabelField: 'groupName',
                optgroupValueField: 'groupName',
                optgroups: [{groupName: 'Taufgottesdienste'}, {groupName: 'Andere Gottesdienste'}],
            },
            activeTab: 'home',
            myBaptism: myBaptism,
        }
    },
    computed: {
        hasRegistrationForm() {
            var found = false;
            this.myBaptism.attachments.forEach(attachment => {
                found = found || (attachment.title == 'Anmeldeformular');
            });
            return found;
        },
    },
    methods: {
        prepChecks() {
            return (this.myBaptism.first_contact_with)
                && (this.myBaptism.first_contact_with)
                && (this.myBaptism.appointment)
                && (this.myBaptism.registered)
                && (this.myBaptism.signed)
                && (this.myBaptism.docs_ready)
                && (this.myBaptism.docs_where)
                && (this.myBaptism.text)
                && (this.myBaptism.processed);
        },
        saveBaptism() {
            this.$inertia.patch(route('baptisms.update', {baptism: this.myBaptism.id}), this.myBaptism);
        },
        deleteBaptism() {
            this.$inertia.delete(route('baptisms.destroy', {baptism: this.myBaptism.id}));
        },
    }
}
</script>

<style scoped>
.card-header {
    padding-bottom: 0 !important;
    background-color: #fcfcfc;
}

ul.nav.nav-tabs {
    margin-bottom: 0;
    border-bottom-width: 0;
}

</style>
