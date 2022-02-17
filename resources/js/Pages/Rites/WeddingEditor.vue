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
    <admin-layout :title="'Trauung von '+spouseName(1)+' &amp; '+spouseName(2)">
        <template slot="navbar-left">
            <button class="btn btn-primary" @click.prevent="saveWedding" title="Speichern">
                <span class="d-inline d-md-none mdi mdi-content-save"></span> <span class="d-none d-md-inline">Speichern</span>
            </button>&nbsp;
            <button class="btn btn-danger" @click.prevent="deleteWedding" title="Löschen">
                <span class="d-inline d-md-none mdi mdi-delete"></span> <span class="d-none d-md-inline">Löschen</span>
            </button>
        </template>
        <template slot="tab-headers">
            <tab-headers>
                <tab-header title="Brautpaar" id="home" :active-tab="activeTab" :is-checked-item="true"
                            :check-value="permissionChecks()"/>
                <tab-header title="Vorbereitung" id="prep" :active-tab="activeTab" :is-checked-item="true"
                            :check-value="prepChecks()"/>
                <tab-header title="Dateien" id="attachments" :active-tab="activeTab"
                            :count="myWedding.attachments.length"/>
            </tab-headers>
        </template>
        <tabs>
            <tab id="home" :active-tab="activeTab">
                <div class="row">
                    <div v-for="spouseIndex in [1,2]" class="col-md-6">
                        <form-input :name="spouseKey(spouseIndex, 'name')" label="Name"
                                    v-model="wedding[spouseKey(spouseIndex, 'name')]"/>
                        <form-group label="Zu verwendendes Pronomen">
                            <div>
                                <div class="form-check-inline"
                                     v-for="(pronounSet, pronounSetIndex) in pronounSets"
                                     :key="'pronouns_'+pronounSet.key">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input"
                                               v-model="myWedding['pronoun_set'+spouseIndex]"
                                               :value="pronounSet.key">{{ pronounSet.label }}
                                    </label>
                                </div>
                            </div>
                        </form-group>
                        <form-input :name="spouseKey(spouseIndex, 'birth_name')"
                                    label="Evtl. Geburtsname"
                                    v-model="wedding[spouseKey(spouseIndex, 'birth_name')]"/>
                        <form-input :name="spouseKey(spouseIndex, 'phone')"
                                    label="Telefon"
                                    v-model="wedding[spouseKey(spouseIndex, 'phone')]"/>
                        <form-input :name="spouseKey(spouseIndex, 'email')"
                                    label="E-Mailadresse"
                                    v-model="wedding[spouseKey(spouseIndex, 'email')]"/>
                        <form-check :name="spouseKey(spouseIndex, 'needs_dimissorial')"
                                    label="Dimissoriale benötigt"
                                    v-model="wedding[spouseKey(spouseIndex, 'needs_dimissorial')]"/>
                        <div v-if="wedding[spouseKey(spouseIndex, 'needs_dimissorial')]">
                            <form-input :name="spouseKey(spouseIndex, 'dimissorial_issuer')"
                                        label="Zuständiges Pfarramt"
                                        :is-checked-item="true"
                                        v-model="wedding[spouseKey(spouseIndex, 'dimissorial_issuer')]"/>
                            <form-date-picker :name="spouseKey(spouseIndex, 'dimissorial_requested')"
                                              v-model="wedding[spouseKey(spouseIndex, 'dimissorial_requested')]"
                                              :is-checked-item="true"
                                              label="Dimissoriale beantragt am"/>
                            <form-date-picker :name="spouseKey(spouseIndex, 'dimissorial_received')"
                                              v-model="wedding[spouseKey(spouseIndex, 'dimissorial_received')]"
                                              :is-checked-item="true"
                                              label="Dimissoriale erhalten am"/>
                            <div>
                                <dimissorial-url :url="wedding['spouse'+spouseIndex+'DimissorialUrl']"/>
                            </div>
                        </div>
                    </div>
                </div>
                <fieldset>
                    <legend>Genehmigung</legend>
                    <form-selectize name="needs_permission" v-model="permissionState"
                                    :options="permissionItems" :settings="{searchField: ['name']}"/>
                    <div v-if="permissionState != 0" class="row" :key="permissionState">
                        <div class="col-md-6">
                            <form-date-picker name="permission_requested"
                                              v-model="myWedding.permission_requested"
                                              :is-checked-item="true"
                                              label="Genehmigung beantragt"/>
                        </div>
                        <div class="col-md-6">
                            <form-date-picker name="permission_received" v-model="myWedding.permission_received"
                                              :is-checked-item="true"
                                              label="Genehmigung erhalten"/>
                        </div>
                    </div>
                </fieldset>
            </tab>
            <tab id="prep" :active-tab="activeTab">
                <fieldset id="fsPrep">
                    <legend>Traugespräch</legend>
                    <form-group label="Traugespräch" :is-checked-item="true" :value="myWedding.appointment"
                                name="candidate_appointment">
                        <date-picker v-model="myWedding.appointment" :config="myDateTimePickerConfig"/>
                    </form-group>
                    <form-input name="text" label="Trautext" v-model="myWedding.text" :is-checked-item="true"/>
                    <form-textarea name="notes" label="Notizen aus dem Traugespräch" v-model="myWedding.notes"/>
                </fieldset>
                <fieldset>
                    <legend>Trauung</legend>
                    <form-textarea name="music" label="Musikalische Wünsche" v-model="myWedding.music"/>
                    <form-input name="gift" label="Bibel/Buchgabe" v-model="myWedding.gift"/>
                    <form-textarea name="flowers" label="Blumenschmuck" v-model="myWedding.flowers"/>
                </fieldset>
                <fieldset>
                    <legend>Unterlagen</legend>
                    <form-group label="Format der Urkunde">
                        <div>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input"
                                           v-model="myWedding.docs_format"
                                           value="0"> Urkunde klein (für Familienstammbuch)
                                </label>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input"
                                           v-model="myWedding.docs_format"
                                           value="1"> Urkunde A4
                                </label>
                            </div>
                        </div>
                    </form-group>
                    <div v-if="!hasRegistrationForm" class="mb-3">
                        <p>Wenn das Anmeldeformular in AHAS Online erstellt wurde, kannst du es hier
                            hochladen:</p>
                        <form-file-uploader :parent="myWedding"
                                            :upload-route="route('wedding.attach', this.myWedding.id)"
                                            title="Anmeldeformular"
                                            v-model="myWedding.attachments"/>
                    </div>
                    <div v-else>
                        <checked-process-item check="1"
                                              positive="Anmeldedaten aufgenommen und Anmeldeformular erstellt"/>
                        <form-check label="Anmeldung unterschrieben" v-model="myWedding.signed" name="signed"
                                    :is-checked-item="true"/>
                        <form-check label="Urkunden erstellt" v-model="myWedding.docs_ready" name="ready"
                                    :is-checked-item="true"/>
                        <form-input label="Urkunden hinterlegt" v-model="myWedding.docs_where" name="docs_where"
                                    class="mt-3"
                                    help="Wo sind die Unterlagen hinterlegt?"
                                    :is-checked-item="true"/>
                    </div>

                    <form-check name="processed" label="Kirchenbucheintrag abgeschlossen" v-model="myWedding.processed"
                                is-checked-item/>
                </fieldset>
            </tab>
            <tab id="attachments" :active-tab="activeTab">
                <fieldset>
                    <legend>Angehängte Dateien</legend>
                    <attachment-list v-model="myWedding.attachments" delete-route-name="wedding.detach"
                                     :parent-object="myWedding" parent-type="wedding"
                                     :key="myWedding.attachments.length"/>
                </fieldset>
                <fieldset>
                    <legend>Dateien hinzufügen</legend>
                    <form-file-uploader :parent="myWedding"
                                        :upload-route="route('wedding.attach', this.myWedding.id)"
                                        v-model="myWedding.attachments"/>
                    <div class="mt-2"><small>Das Anmeldeformular zur Trauung bitte nicht hier, sondern im
                        Register
                        "Vorbereitung" hochladen.</small></div>
                </fieldset>
            </tab>
        </tabs>
    </admin-layout>
</template>

<script>
import Tab from '../../components/Ui/tabs/tab';
import Tabs from "../../components/Ui/tabs/tabs";
import TabHeaders from "../../components/Ui/tabs/tabHeaders";
import TabHeader from "../../components/Ui/tabs/tabHeader";
import FormInput from "../../components/Ui/forms/FormInput";
import FormGroup from "../../components/Ui/forms/FormGroup";
import AttachmentList from "../../components/Ui/elements/AttachmentList";
import FormFileUploader from "../../components/Ui/forms/FormFileUploader";
import FormCheck from "../../components/Ui/forms/FormCheck";
import FormTextarea from "../../components/Ui/forms/FormTextarea";
import CheckedProcessItem from "../../components/Ui/elements/CheckedProcessItem";
import FormDatePicker from "../../components/Ui/forms/FormDatePicker";
import FormRadioGroup from "../../components/Ui/forms/FormRadioGroup";
import FormSelectize from "../../components/Ui/forms/FormSelectize";
import DimissorialUrl from "../../components/RiteEditors/DimissorialUrl";

export default {
    name: "WeddingEditor",
    components: {
        DimissorialUrl,
        FormSelectize,
        FormRadioGroup,
        FormDatePicker,
        CheckedProcessItem,
        FormTextarea,
        FormCheck,
        FormFileUploader,
        AttachmentList, FormGroup, FormInput, Tab,  TabHeader, TabHeaders, Tabs,
    },
    created() {
        this.wedding.docs_format = this.wedding.docs_format || 0;
        this.wedding.needs_permission = this.wedding.needs_permission || 0;
    },
    computed: {
        hasRegistrationForm() {
            var found = false;
            this.myWedding.attachments.forEach(attachment => {
                found = found || (attachment.title == 'Anmeldeformular');
            });
            return found;
        },
    },
    props: ['wedding', 'tab', 'pronounSets'],
    data() {
        var myWedding = this.wedding;
        myWedding.docs_format = myWedding.docs_format || 0;
        myWedding.needs_permission = myWedding.needs_permission || 0;

        if (myWedding.appointment) myWedding.appointment = moment(myWedding.appointment);
        [
            'permission_requested',
            'permission_received',
            'spouse1_dimissorial_requested',
            'spouse1_dimissorial_received',
            'spouse2_dimissorial_requested',
            'spouse2_dimissorial_received'
        ].forEach(item => {
            if (myWedding[item]) myWedding[item] = moment(myWedding['item']);
        });

        return {
            myWedding: myWedding,
            activeTab: this.tab || 'home',
            myDateTimePickerConfig: {
                locale: 'de',
                format: 'DD.MM.YYYY HH:mm',
                showClear: true,
                sideBySide: true,
            },
            permissionState: myWedding.needs_permission || 0,
            permissionItems: [
                {id: 0, name: 'Für diese Trauung ist keine Genehmigung des Dekanatamts erforderlich'},
                {
                    id: 1,
                    name: 'Für diese Trauung ist eine Genehmigung des Dekanatamts erforderlich, weil ein Ehegatte keiner Kirche oder Religionsgemeinschaft angehört. (§5 Abs. 2b TrauO)'
                },
                {
                    id: 2,
                    name: 'Für diese Trauung ist eine Genehmigung des Dekanatamts erforderlich, weil mindestens ein Ehegatte geschieden ist. (§7 Abs. 2b TrauO)'
                },
                {
                    id: 3,
                    name: 'Für diese Trauung ist eine Genehmigung des Dekanatamts aus anderen Gründen erforderlich.'
                },
            ]
        }
    },
    methods: {
        prepChecks() {
            return (this.myWedding.appointment)
                && (this.myWedding.registered)
                && (this.myWedding.signed)
                && (this.myWedding.docs_ready)
                && (this.myWedding.docs_where)
                && (this.myWedding.text)
                && (this.myWedding.processed);
        },
        permissionChecks() {
            var check = true;
            if (this.myWedding.spouse1_needs_dimissorial) check = check && (this.myWedding.spouse1_dimissorial_received);
            if (this.myWedding.spouse2_needs_dimissorial) check = check && (this.myWedding.spouse2_dimissorial_received);
            if (this.permissionState != 0) check = check && (this.myWedding.permission_received);
            return check;
        },
        spouseName(index) {
            var name = this.myWedding['spouse' + index + '_name'].split(', ');
            return name[1] + ' ' + name[0];
        },
        spouseKey(index, key) {
            return 'spouse' + index + '_' + key;
        },
        saveWedding() {
            var result = this.myWedding;
            result.needs_permission = this.permissionState;

            ['permission_requested',
                'permission_received',
                'spouse1_dimissorial_requested',
                'spouse1_dimissorial_received',
                'spouse2_dimissorial_requested',
                'spouse2_dimissorial_received'
            ].forEach(item => {
                if (result[item]) result[item] = moment(result['item']).format('DD.MM.YYYY');
            });

            if (result.appointment) result.appointment = moment(result.appointment, 'DD.MM.YYYY HH:mm').format('DD.MM.YYYY HH:mm');

            this.$inertia.patch(route('weddings.update', this.myWedding.id), result);
        },
        deleteWedding() {
            this.$inertia.delete(route('weddings.destroy', this.myWedding.id), this.myWedding);
        }
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
