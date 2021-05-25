<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
  - @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
  - @link https://github.com/potofcoffee/pfarrplaner
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
    <admin-layout :title="'Beerdigung von '+funeral.buried_name">
        <template slot="navbar-left">
            <button class="btn btn-primary" @click.prevent="saveFuneral" title="Speichern">
                <span class="d-inline d-md-none fa fa-save"></span> <span class="d-none d-md-inline">Speichern</span>
            </button>&nbsp;
            <button class="btn btn-danger" @click.prevent="deleteFuneral" title="Löschen">
                <span class="d-inline d-md-none fa fa-trash"></span> <span class="d-none d-md-inline">Löschen</span>
            </button>
        </template>
        <card>
            <card-header>
                <tab-headers>
                    <tab-header title="Allgemeines" id="home" :active-tab="activeTab"/>
                    <tab-header title="Bestattung" id="funeral" :active-tab="activeTab"
                                :is-checked-item="true"
                                :check-value="myFuneral.text && myFuneral.announcement"/>
                    <tab-header title="Angehörige" id="family" :active-tab="activeTab"/>
                    <tab-header title="Trauergespräch" id="interview" :active-tab="activeTab" :is-checked-item="true"
                                :check-value="myFuneral.appointment"/>
                    <tab-header title="Dateien" id="attachments" :active-tab="activeTab"
                                :count="myFuneral.attachments.length"/>
                </tab-headers>
            </card-header>
            <card-body>
                <tabs>
                    <tab id="home" :active-tab="activeTab">
                        <div class="row">
                            <div class="col-md-6">
                                <form-input name="buried_name" v-model="myFuneral.buried_name"
                                            label="Name" placeholder="Nachname, Vorname"/>
                            </div>
                            <div class="col-md-6">
                                <form-group label="Zu verwendendes Pronomen">
                                    <div>
                                        <div class="form-check-inline"
                                             v-for="(pronounSet, pronounSetIndex) in pronounSets"
                                             :key="'pronouns_'+pronounSet.key">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input"
                                                       v-model="myFuneral.pronoun_set"
                                                       :value="pronounSet.key">{{ pronounSet.label }}
                                            </label>
                                        </div>
                                    </div>
                                </form-group>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <form-group name="dob" label="Geburtsdatum">
                                    <date-picker :config="myDatePickerConfig" v-model="myFuneral.dob"/>
                                </form-group>
                            </div>
                            <div class="col-md-6">
                                <form-group name="dod" label="Sterbedatum" :help="age ? 'Sterbealter: '+age : ''">
                                    <date-picker :config="myDatePickerConfig" v-model="myFuneral.dod"/>
                                </form-group>
                            </div>
                        </div>
                        <hr/>
                        <form-input name="buried_address" v-model="myFuneral.buried_address"
                                    label="Adresse" help="Straße und Hausnummer"/>
                        <div class="row">
                            <div class="col-md-3">
                                <form-input name="buried_zip" v-model="myFuneral.buried_zip"
                                            label="Postleitzahl" type="number"/>
                            </div>
                            <div class="col-md-9">
                                <form-input name="buried_city" v-model="myFuneral.buried_city"
                                            label="Ort"/>
                            </div>
                        </div>
                    </tab>
                    <tab id="funeral" :active-tab="activeTab">
                        <fake-table :columns="[2,2,4,4]" :headers="['Datum', 'Uhrzeit', 'Ort', '']"
                                    collapsed-header="Bestattung">
                            <div class="row p-1">
                                <div class="col-md-2">{{
                                        moment(myFuneral.service.day.date).format('DD.MM.YYYY')
                                    }}
                                </div>
                                <div class="col-md-2">{{ myFuneral.service.timeText }}</div>
                                <div class="col-md-4">{{ myFuneral.service.locationText }}</div>
                                <div class="col-md-4 text-right">
                                    <inertia-link :href="route('services.edit', funeral.service.id)"
                                                  title="Gottesdienst bearbeiten"
                                                  class="btn btn-light">
                                        <span class="fa fa-edit"></span> <span
                                        class="d-none d-md-inline">Gottesdienst</span>
                                    </inertia-link>
                                    <inertia-link :href="route('services.liturgy.editor', funeral.service.id)"
                                                  title="Liturgie bearbeiten"
                                                  class="btn btn-light">
                                        <span class="fa fa-th-list"></span> <span
                                        class="d-none d-md-inline">Liturgie</span>
                                    </inertia-link>
                                    <inertia-link :href="route('services.sermon.editor', funeral.service.id)"
                                                  title="Predigt bearbeiten"
                                                  class="btn btn-light">
                                        <span class="fa fa-microphone"></span> <span
                                        class="d-none d-md-inline">Predigt</span>
                                    </inertia-link>
                                </div>
                            </div>
                        </fake-table>
                        <hr/>
                        <form-input label="Predigttext" v-model="myFuneral.text" :is-checked-item="true"/>
                        <form-group label="Bestattungsart">
                            <select v-model="myFuneral.type" class="form-control">
                                <option>Erdbestattung</option>
                                <option>Trauerfeier</option>
                                <option>Trauerfeier mit Urnenbeisetzung</option>
                                <option>Urnenbeisetzung</option>
                            </select>
                        </form-group>
                        <div v-if="myFuneral.type == 'Urnenbeisetzung'">
                            <form-group label="Datum der vorhergehenden Trauerfeier">
                                <date-picker :config="myDatePickerConfig" v-model="myFuneral.wake"/>
                            </form-group>
                            <form-input label="Ort der vorhergehenden Trauerfeier"
                                        v-model="myFuneral.wake_location" name="wake_location"/>
                        </div>
                        <form-group label="Abkündigen am" :is-checked-item="true" :value="myFuneral.announcement">
                            <date-picker :config="myDatePickerConfig" v-model="myFuneral.announcement"/>
                        </form-group>
                        <hr/>
                        <form-textarea label="Bestatter" v-model="myFuneral.undertaker"/>
                        <form-textarea label="Nachrufe" v-model="myFuneral.eulogies"/>
                        <form-textarea label="Notizen" v-model="myFuneral.notes"/>
                    </tab>
                    <tab id="family" :active-tab="activeTab">
                        <form-input name="relative_name" v-model="myFuneral.relative_name"
                                    label="Name" placeholder="Nachname, Vorname"/>
                        <div class="mb-3">
                            <button @click.prevent="copyAddress" class="btn btn-light">
                                <span class="fa fa-copy"></span> Adresse übernehmen
                            </button>
                        </div>
                        <form-input v-model="myFuneral.relative_address"
                                    label="Adresse" help="Straße und Hausnummer" :key="myFuneral.relative_address"/>
                        <div class="row">
                            <div class="col-md-3">
                                <form-input v-model="myFuneral.relative_zip"
                                            label="Postleitzahl" type="number" :key="myFuneral.relative_zip"/>
                            </div>
                            <div class="col-md-9">
                                <form-input v-model="myFuneral.relative_city" :key="myFuneral.relative_city"
                                            label="Ort"/>
                            </div>
                        </div>
                        <form-textarea label="Weitere Kontaktdaten" v-model="myFuneral.relative_contact_data"/>
                    </tab>
                    <tab id="interview" :active-tab="activeTab">
                        <form-group label="Trauergespräch" :is-checked-item="true" :value="myFuneral.appointment">
                            <date-picker v-model="myFuneral.appointment" :config="myDateTimePickerConfig"/>
                        </form-group>
                        <form-textarea label="Anwesende" v-model="myFuneral.attending"/>
                        <hr/>
                        <div class="row">
                            <div class="col-md-6">
                                <form-textarea label="Ehepartner" v-model="funeral.spouse"/>
                                <form-textarea label="Kinder" v-model="funeral.children"/>
                                <form-textarea label="Weitere Hinterbliebene" v-model="funeral.further_family"/>
                                <hr />
                                <form-textarea label="Taufe" v-model="funeral.baptism"/>
                                <form-textarea label="Konfirmation" v-model="funeral.confirmation"/>
                                <hr />
                                <form-textarea label="Kindheit, Jugend" v-model="funeral.childhood"/>
                                <form-textarea label="Ausbildung, Beruf" v-model="funeral.profession"/>
                                <form-textarea label="Heirat, Familie" v-model="funeral.family"/>
                                <form-textarea label="Weiterer Lebenslauf" v-model="funeral.further_life"/>
                                <form-textarea label="Lebensende" v-model="funeral.death"/>
                                <hr />
                                <form-textarea label="Prägende Erlebnisse, Hobbies, Interessen" v-model="funeral.events"/>
                                <form-textarea label="Charakter" v-model="funeral.character"/>
                                <form-textarea label="Glaube, Frömmigkeit, Kirche" v-model="funeral.faith"/>
                                <hr />
                                <form-textarea label="Zitate" v-model="funeral.quotes"/>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Lebenslauf</label>
                                    <quill-editor :class="{focused: textEditorActive}" ref="textEditor"
                                                  v-model="funeral.life"
                                                  :options="editorOption" @focus="textEditorActive = true"
                                                  @blur="textEditorActive = false"/>
                                </div>

                            </div>
                        </div>
                    </tab>
                    <tab id="attachments" :active-tab="activeTab">
                        <h3>Angehängte Dateien</h3>
                        <attachment-list v-model="myFuneral.attachments" delete-route-name="funeral.detach"
                                         :parent-object="myFuneral" parent-type="funeral"
                                         :key="myFuneral.attachments.length"/>
                        <hr/>
                        <h3>Dateien hinzufügen.</h3>
                        <form-file-uploader :parent="myFuneral"
                                            :upload-route="route('funeral.attach', this.myFuneral.id)"
                                            v-model="myFuneral.attachments"/>
                    </tab>
                </tabs>
            </card-body>
        </card>
    </admin-layout>
</template>

<script>
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'
import '../../components/SermonEditor/quill.css';
import {quillEditor} from 'vue-quill-editor';


import Card from "../../components/Ui/cards/card";
import CardBody from "../../components/Ui/cards/cardBody";
import CardHeader from "../../components/Ui/cards/cardHeader";
import TabHeaders from "../../components/Ui/tabs/tabHeaders";
import TabHeader from "../../components/Ui/tabs/tabHeader";
import Tabs from "../../components/Ui/tabs/tabs";
import Tab from "../../components/Ui/tabs/tab";
import FormInput from "../../components/Ui/forms/FormInput";
import FormGroup from "../../components/Ui/forms/FormGroup";
import BasicInfo from "../../components/Service/BasicInfo";
import FakeTable from "../../components/Ui/FakeTable";
import FormTextarea from "../../components/Ui/forms/FormTextarea";
import FormFileUploader from "../../components/Ui/forms/FormFileUploader";
import Attachment from "../../components/Ui/elements/Attachment";
import AttachmentList from "../../components/Ui/elements/AttachmentList";
import ValueCheck from "../../components/Ui/elements/ValueCheck";


export default {
    name: "FuneralEditor",
    components: {
        quillEditor,
        ValueCheck,
        AttachmentList,
        Attachment,
        FormFileUploader,
        FormTextarea,
        FakeTable,
        BasicInfo, FormGroup, FormInput, TabHeader, TabHeaders, Tabs, Tab, CardHeader, CardBody, Card
    },
    props: ['funeral', 'pronounSets'],
    computed: {
        age() {
            if ((!this.myFuneral.dod) || (!this.myFuneral.dob)) return null;
            return moment(this.myFuneral.dod, 'DD.MM.YYYY').diff(moment(this.myFuneral.dob, 'DD.MM.YYYY'), 'years');
        }
    },
    created() {
        if (this.myFuneral.dob) this.myFuneral.dob = moment(this.myFuneral.dob).format('DD.MM.YYYY');
        if (this.myFuneral.dod) this.myFuneral.dod = moment(this.myFuneral.dod).format('DD.MM.YYYY');
        if (this.myFuneral.announcement) this.myFuneral.announcement = moment(this.myFuneral.announcement).format('DD.MM.YYYY');
        if (this.myFuneral.wake) this.myFuneral.wake = moment(this.myFuneral.wake).format('DD.MM.YYYY');
        if (this.myFuneral.appointment) this.myFuneral.appointment = moment(this.myFuneral.appointment).format('DD.MM.YYYY HH:mm');
    },
    data() {
        var myFuneral = this.funeral;
        myFuneral.life = myFuneral.life || '';

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
            },
            myFuneral: myFuneral,
            activeTab: 'home',
            textEditorActive: false,
            editorOption: {
                placeholder: 'Hier kannst du einen Textentwurf für den Lebenslauf schreiben...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],        // toggled buttons
                        ['blockquote'],

                        [{'header': 1}],               // custom button values
                        [{'list': 'ordered'}, {'list': 'bullet'}],

                        ['clean']                                         // remove formatting button
                    ],
                    clipboard: {
                        matchVisual: false,
                    },
                }
            },

        }
    },
    methods: {
        copyAddress() {
            this.myFuneral.relative_address = this.myFuneral.buried_address;
            this.myFuneral.relative_zip = this.myFuneral.buried_zip;
            this.myFuneral.relative_city = this.myFuneral.buried_city;
            this.$forceUpdate();
        },
        saveFuneral() {
            this.$inertia.patch(route('funerals.update', this.myFuneral.id), this.myFuneral, {
                preserveState: false,
            });
        },
        deleteFuneral() {
            this.$inertia.delete(route('funerals.destroy', this.myFuneral.id), {
                preserveState: false,
            })
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
