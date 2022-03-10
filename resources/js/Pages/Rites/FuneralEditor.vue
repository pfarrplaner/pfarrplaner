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
    <admin-layout :title="'Beerdigung von '+funeral.buried_name" :key="formKey">
        <template slot="navbar-left">
            <button class="btn btn-primary" @click.prevent="saveFuneral" title="Speichern">
                <span class="d-inline d-md-none mdi mdi-content-save"></span> <span
                class="d-none d-md-inline">Speichern</span>
            </button>&nbsp;
            <button class="btn btn-danger" @click.prevent="deleteFuneral" title="Löschen">
                <span class="d-inline d-md-none mdi mdi-delete"></span> <span class="d-none d-md-inline">Löschen</span>
            </button>
            <div class="dropdown show">
                <a class="btn btn-light dropdown-toggle ml-1" href="#" role="button" id="dropdownMenuLink"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Weitere Aktionen
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" @click.prevent.stop="saveInLocalStorage">Datenpaket im Browser sichern</a>
                    <a class="dropdown-item" @click.prevent.stop="downloadAsJson">Datenpaket auf Festplatte sichern</a>
                </div>
            </div>
        </template>
        <template slot="after-flash">
            <div v-if="inLocalStorage" class="alert alert-info">
                <div>Eine Kopie dieses Datensatzes wurde im Browser zwischengespeichert. Willst du diese Kopie wiederherstellen?</div>
                <div class="pull-right">
                    <button class="btn btn-info btn-sm" @click.prevent.stop="loadFromLocalStorage">Wiederherstellen</button>
                    <button class="btn btn-danger btn-sm" @click.prevent.stop="deleteFromLocalStorage">Löschen</button>
                </div>
            </div>
        </template>
        <template slot="tab-headers">
            <tab-headers>
                <tab-header title="Allgemeines" id="home" :active-tab="activeTab" :is-checked-item="true"
                            :check-value="(!myFuneral.needs_dimissorial) || (myFuneral.dimissorial_received)"/>
                <tab-header title="Bestattung" id="funeral" :active-tab="activeTab"
                            :is-checked-item="true"
                            :check-value="myFuneral.text && myFuneral.announcement && myFuneral.processed"/>
                <tab-header title="Angehörige" id="family" :active-tab="activeTab"/>
                <tab-header title="Trauergespräch" id="interview" :active-tab="activeTab" :is-checked-item="true"
                            :check-value="myFuneral.appointment"/>
                <tab-header title="Dateien" id="attachments" :active-tab="activeTab"
                            :count="myFuneral.attachments.length +1"/>
            </tab-headers>
        </template>
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
                        <form-input name="birth_name" label="Geburtsname"
                                    help="falls abweichend vom Nachnamen" v-model="myFuneral.birth_name"/>
                    </div>
                    <div class="col-md-6">
                        <form-input label="Rufname" v-model="myFuneral.spoken_name" name="spoken_name"
                                    help="falls abweichend vom kompletten Vornamen"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <form-input label="Beruf" v-model="myFuneral.profession" name="profession"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <form-group name="dob" label="Geburtsdatum">
                            <date-picker :config="myDatePickerConfig" v-model="myFuneral.dob"/>
                        </form-group>
                    </div>
                    <div class="col-md-6">
                        <form-group name="dod" label="Sterbedatum"
                                    :help="relativeDate(myFuneral.dod, moment())+(age ? ' im Alter von '+age+' Jahren' : '')">
                            <date-picker :config="myDatePickerConfig" v-model="myFuneral.dod"/>
                        </form-group>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <form-input label="Geburtsort" v-model="myFuneral.birth_place" name="birth_place"/>
                    </div>
                    <div class="col-md-6">
                        <form-input label="Sterbeort" v-model="myFuneral.death_place" name="death_place"/>
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
                <dimissorial-form-part :parent="myFuneral"/>
            </tab>
            <tab id="funeral" :active-tab="activeTab">
                <fake-table :columns="[2,2,2,3,3]" :headers="['Datum', 'Uhrzeit', 'Ort', 'Pfarrer:in', '']"
                            collapsed-header="Bestattung">
                    <div class="row p-1">
                        <div class="col-md-2">{{
                                moment(myFuneral.service.date).format('DD.MM.YYYY')
                            }}
                        </div>
                        <div class="col-md-2">{{ myFuneral.service.timeText }}</div>
                        <div class="col-md-2">{{ myFuneral.service.locationText }}</div>
                        <div class="col-md-3">
                            <participants :participants="myFuneral.service.pastors"></participants>
                        </div>
                        <div class="col-md-3 text-right">
                            <inertia-link :href="route('service.edit', funeral.service.slug)"
                                          title="Gottesdienst bearbeiten"
                                          class="btn btn-light">
                                <span class="mdi mdi-pencil"></span> <span
                                class="d-none d-md-inline">Gottesdienst</span>
                            </inertia-link>
                            <inertia-link :href="route('liturgy.editor', funeral.service.slug)"
                                          title="Liturgie bearbeiten"
                                          class="btn btn-light">
                                <span class="mdi mdi-view-list"></span> <span
                                class="d-none d-md-inline">Liturgie</span>
                            </inertia-link>
                            <inertia-link :href="route('service.sermon.editor', funeral.service.slug)"
                                          title="Predigt bearbeiten"
                                          class="btn btn-light">
                                <span class="mdi mdi-microphone"></span> <span
                                class="d-none d-md-inline">Predigt</span>
                            </inertia-link>
                        </div>
                    </div>
                </fake-table>
                <hr/>
                <form-bible-reference-input label="Predigttext" v-model="myFuneral.text" :is-checked-item="true"
                                            :key="referenceCopied" :sources="textSources"/>
                <button v-if="funeral.service.sermon && funeral.service.sermon.reference"
                        class="btn btn-sm btn-light"
                        :title="'Von Predigt übernehmen ('+funeral.service.sermon.reference+')'"
                        @click="setFuneralText(funeral.service.sermon.reference)">Von Predigt übernehmen
                </button>
                <form-group label="Bestattungsart">
                    <select v-model="myFuneral.type" class="form-control" name="type">
                        <option>Erdbestattung</option>
                        <option>Trauerfeier</option>
                        <option>Trauerfeier mit Urnenbeisetzung</option>
                        <option>Urnenbeisetzung</option>
                    </select>
                </form-group>
                <div v-if="myFuneral.type == 'Urnenbeisetzung'">
                    <form-group label="Datum der vorhergehenden Trauerfeier" name="wake">
                        <date-picker :config="myDatePickerConfig" v-model="myFuneral.wake"/>
                    </form-group>
                    <form-input label="Ort der vorhergehenden Trauerfeier"
                                v-model="myFuneral.wake_location" name="wake_location"/>
                </div>
                <form-group label="Abkündigen am" :is-checked-item="true" :value="myFuneral.announcement"
                            name="announcement">
                    <date-picker :config="myDatePickerConfig" v-model="myFuneral.announcement"/>
                </form-group>
                <hr/>
                <form-textarea label="Bestatter" v-model="myFuneral.undertaker" name="undertaker"/>
                <form-textarea label="Nachrufe" v-model="myFuneral.eulogies" name="eulogies"/>
                <form-textarea label="Notizen" v-model="myFuneral.notes" name="notes"/>
                <form-check name="processed" label="Kirchenbucheintrag abgeschlossen"
                            v-model="myFuneral.processed" is-checked-item/>
            </tab>
            <tab id="family" :active-tab="activeTab">
                <form-input name="relative_name" v-model="myFuneral.relative_name"
                            label="Name" placeholder="Nachname, Vorname"/>
                <div class="mb-3">
                    <button @click.prevent="copyAddress" class="btn btn-light">
                        <span class="mdi mdi-content-copy"></span> Adresse übernehmen
                    </button>
                </div>
                <form-input v-model="myFuneral.relative_address" name="relative_address" :key="copied"
                            label="Adresse" help="Straße und Hausnummer"/>
                <div class="row">
                    <div class="col-md-3">
                        <form-input v-model="myFuneral.relative_zip" name="relative_zip" :key="copied"
                                    label="Postleitzahl" type="number"/>
                    </div>
                    <div class="col-md-9">
                        <form-input v-model="myFuneral.relative_city" name="relative_city" :key="copied"
                                    label="Ort"/>
                    </div>
                </div>
                <form-textarea label="Weitere Kontaktdaten" v-model="myFuneral.relative_contact_data"
                               name="relative_contact_data"/>
            </tab>
            <tab id="interview" :active-tab="activeTab">
                <div class="row">
                    <div class="col-md-6">
                        <form-group label="Trauergespräch" :is-checked-item="true" :value="myFuneral.appointment"
                                    name="appointment">
                            <date-picker v-model="myFuneral.appointment" :config="myDateTimePickerConfig"/>
                        </form-group>
                    </div>
                    <div class="col-md-6">
                        <form-input label="Ort des Trauergesprächs" name="appointment_address"
                                    :key="myFuneral.appointment_address"
                                    v-model="myFuneral.appointment_address"/>
                        <button class="btn btn-sm btn-light" @click="copyBuriedAddress">Von Adresse übernehmen</button>
                        <button class="btn btn-sm btn-light" @click="copyRelativeAddress">Von Angehörigen übernehmen
                        </button>
                    </div>
                </div>
                <form-textarea label="Anwesende" v-model="myFuneral.attending" name="attending"/>
                <hr/>
                <div v-if="imageAttachments.length > 0" class="d-none d-md-block">
                    <label>Angehängte Bilder</label>
                    <div class="row">
                        <div class="col-md-3 p-2" v-for="(image,imageIndex,imageKey) in imageAttachments"
                             :key="imageKey">
                            <attachment :attachment="image"/>
                        </div>
                    </div>
                    <hr/>
                </div>
                <div class="row">
                    <div class="col-12 text-right">
                        <button v-if="!showStoryEditor" class="btn btn-light" @click="showStoryEditor = true">
                            <span class="mdi mdi-chevron-left"></span> Editor für Lebenslauf einblenden
                        </button>
                        <button v-else class="btn btn-light" @click="showStoryEditor = false">
                            <span class="mdi mdi-chevron-right"></span> Editor für Lebenslauf ausblenden
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div :class="showStoryEditor ? 'col-lg-4': 'col-md-6'">
                        <form-textarea label="Eltern, Herkunftsfamilie" v-model="funeral.parents"
                                       name="parents"/>

                        <accordion id="casesAccordion">
                            <accordion-element title="Taufe" icon="mdi mdi-water">
                                <form-textarea label="Taufe" v-model="funeral.baptism" name="baptism"/>
                                <form-date-picker label="Taufdatum" v-model="myFuneral.baptism_date" name="baptism_date"
                                                  :help="ageText(myFuneral.baptism_date, myFuneral.dob, 'mit ', 'n')"/>
                            </accordion-element>
                            <accordion-element title="Konfirmation" icon="mdi mdi-cross-outline">
                                <form-textarea label="Konfirmation" v-model="funeral.confirmation" name="confirmation"/>
                                <form-date-picker label="Datum der Konfirmation" v-model="funeral.confirmation_date"
                                                  name="confirmation_date"
                                                  :help="ageText(myFuneral.confirmation_date, myFuneral.dob, 'mit ', 'n')"/>
                                <form-bible-reference-input label="Denkspruch" v-model="myFuneral.confirmation_text"
                                                            name="confirmation_text"/>
                            </accordion-element>
                            <accordion-element title="Heirat" icon="mdi mdi-ring">
                                <form-textarea label="Ehepartner:in" v-model="funeral.spouse" name="spouse"/>
                                <form-date-picker label="Heiratsdatum" v-model="funeral.wedding_date"
                                                  name="wedding_date"
                                                  :help="myFuneral.wedding_date ? ageText(myFuneral.dod_spouse || myFuneral.dod, myFuneral.wedding_date, '', ' verheiratet') : ''"/>
                                <form-bible-reference-input label="Trauspruch" v-model="myFuneral.wedding_text"
                                                            name="wedding_text"/>
                                <form-date-picker label="Sterbedatum Ehepartner:in" v-model="funeral.dod_spouse"
                                                  name="dod_spouse"
                                                  :help="myFuneral.dod_spouse ? ageText(moment().format('DD.MM.YYYY'), myFuneral.dod_spouse, 'vor ', 'n') : ''"/>
                            </accordion-element>
                            <accordion-element title="Familie" icon="mdi mdi-human-male-female-child">
                                <form-textarea label="Kinder" v-model="funeral.children" name="children"/>
                                <form-textarea label="Weitere Hinterbliebene" v-model="funeral.further_family"
                                               name="further_family"/>
                            </accordion-element>
                        </accordion>


                        <hr/>
                    </div>
                    <div :class="showStoryEditor ? 'col-lg-4': 'col-md-6'">
                        <form-textarea label="Kindheit, Jugend" v-model="funeral.childhood" name="childhood"/>
                        <form-input label="Beruf" v-model="myFuneral.profession" name="profession"/>
                        <form-textarea label="Ausbildung, Beruf" v-model="funeral.professional_life"
                                       name="professional_life"/>
                        <form-textarea label="Heirat, Familie" v-model="funeral.family" name="family"/>
                        <form-textarea label="Weiterer Lebenslauf" v-model="funeral.further_life"
                                       name="further_life"/>
                        <form-textarea label="Lebensende" v-model="funeral.death" name="death" :help="endOfLifeText"/>
                        <hr/>
                        <form-textarea label="Prägende Erlebnisse, Hobbies, Interessen"
                                       v-model="funeral.events" name="events"/>
                        <form-textarea label="Charakter" v-model="funeral.character" name="character"/>
                        <form-textarea label="Glaube, Frömmigkeit, Kirche" v-model="funeral.faith"
                                       name="faith"/>
                        <hr/>
                        <form-textarea label="Zitate" v-model="funeral.quotes" name="quotes"/>
                    </div>
                    <div v-if="showStoryEditor" class="col-lg-4">
                        <div class="form-group">
                            <label>Lebenslauf</label>
                            <quill-editor :class="{focused: textEditorActive}" ref="textEditor"
                                          v-model="funeral.life"
                                          :options="editorOption" @focus="textEditorActive = true"
                                          @blur="textEditorActive = false">
                                <div id="toolbar" slot="toolbar">
                                    <button class="ql-bold"></button>
                                    <button class="ql-italic"></button>
                                    <button class="ql-underline mr-2"></button>
                                    <button class="ql-header" value="1"></button>
                                    <button class="ql-blockquote mr-2"></button>
                                    <span class="ql-formats mr-2">
                                                <button class="ql-list" value="ordered"></button>
                                                <button class="ql-list" value="bullet"></button>
                                                <button class="ql-indent" value="-1"></button>
                                                <button class="ql-indent" value="+1"></button>
                                            </span>
                                    <button class="ql-clean mr-2"></button>
                                    <select class="ql-custom" data-label="Texte">
                                        <option value="dob" data-label="Geburtsdatum" data-value="dob">Geburtsdatum
                                        </option>
                                        <option value="dod" data-label="Sterbedatum" data-value="dod">Sterbedatum
                                        </option>
                                        <option value="birth_place" data-label="Geburtsort" data-value="birth_place">
                                            Geburtsort
                                        </option>
                                        <option value="death_place" data-label="Sterbeort" data-value="death_place">
                                            Sterbeort
                                        </option>
                                        <option value="birth_name" data-label="Geburtsname" data-value="birth_name">
                                            Geburtsname
                                        </option>
                                        <option value="spoken_name" data-label="Rufname" data-value="spoken_name">
                                            Rufname
                                        </option>
                                        <option value="age" data-label="Sterbealter" data-value="age">Sterbealter
                                        </option>
                                    </select>
                                </div>
                            </quill-editor>
                            <text-stats :text="funeral.life"/>
                        </div>

                    </div>
                </div>
            </tab>
            <tab id="attachments" :active-tab="activeTab">
                <h3>Angehängte Dateien</h3>
                <attachment-list v-model="myFuneral.attachments" delete-route-name="funeral.detach"
                                 :parent-object="myFuneral" parent-type="funeral" :prevent-empty-list-message="true"
                                 :key="myFuneral.attachments.length"/>
                <fake-attachment :href="route('funeral.form', {funeral: this.myFuneral.id})"
                                 title="Formular für Kirchenregisteramt" extension="pdf"
                                 icon="mdi mdi-file-pdf-box" size="ca. 135 kB"/>

                <hr/>
                <h3>Dateien hinzufügen.</h3>
                <form-file-uploader :parent="myFuneral"
                                    :upload-route="route('funeral.attach', this.myFuneral.id)"
                                    v-model="myFuneral.attachments"/>
            </tab>
        </tabs>
    </admin-layout>
</template>

<script>
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'
import '../../components/SermonEditor/quill.css';
import {quillEditor} from 'vue-quill-editor';
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
import Participants from "../../components/Calendar/Service/Participants";
import FakeAttachment from "../../components/Ui/elements/FakeAttachment";
import RelativeDate from "../../libraries/RelativeDate";
import FormCheck from "../../components/Ui/forms/FormCheck";
import DimissorialFormPart from "../../components/RiteEditors/DimissorialFormPart";
import TextStats from "../../components/LiturgyEditor/Elements/TextStats";
import FormDatePicker from "../../components/Ui/forms/FormDatePicker";
import NavButton from "../../components/Ui/buttons/NavButton";
import FormBibleReferenceInput from "../../components/Ui/forms/FormBibleReferenceInput";
import __ from 'lodash';
import Accordion from "../../components/Ui/accordion/Accordion";
import AccordionElement from "../../components/Ui/accordion/AccordionElement";


export default {
    name: "FuneralEditor",
    components: {
        AccordionElement,
        Accordion,
        FormBibleReferenceInput,
        NavButton,
        FormDatePicker,
        TextStats,
        DimissorialFormPart,
        FormCheck,
        FakeAttachment,
        Participants,
        quillEditor,
        ValueCheck,
        AttachmentList,
        Attachment,
        FormFileUploader,
        FormTextarea,
        FakeTable,
        BasicInfo, FormGroup, FormInput, TabHeader, TabHeaders, Tabs, Tab,
    },
    props: ['funeral', 'pronounSets', 'activeTab'],
    computed: {
        age() {
            if ((!this.myFuneral.dod) || (!this.myFuneral.dob)) return null;
            return moment(this.myFuneral.dod, 'DD.MM.YYYY').diff(moment(this.myFuneral.dob, 'DD.MM.YYYY'), 'years');
        },
        imageAttachments() {
            let images = [];
            this.myFuneral.attachments.forEach(attachment => {
                if (attachment.mimeType.substr(0, 6) == 'image/') images.push(attachment);
            });
            return images;
        },
        endOfLifeText() {
            let parts = [];
            let dod = this.myFuneral.dod ? this.dateFromString(this.myFuneral.dod) : null;
            let dob = this.myFuneral.dob ? this.dateFromString(this.myFuneral.dob) : null;
            if (this.myFuneral.dod) parts.push(dod.locale('de').format('dddd, DD.MM.YYYY'));
            if (this.myFuneral.death_place) parts.push(this.myFuneral.death_place);
            if (this.myFuneral.dod) parts.push(String(this.age) + ' Jahre alt');
            if (this.myFuneral.dob && this.myFuneral.dod) parts.push(dod.diff(dob, 'days').toLocaleString('de-DE') + ' Lebenstage');
            if (parts.length == 0) return '';
            return 'Gestorben: ' + parts.join(', ');
        },
        textSources() {
            let sources = {};
            if (this.myFuneral.service.sermon && this.myFuneral.service.sermon.reference) sources['Predigttext'] = this.myFuneral.service.sermon.reference;
            if (this.myFuneral.confirmation_text) sources['Denkspruch'] = this.myFuneral.confirmation_text;
            if (this.myFuneral.wedding_text) sources['Trauspruch'] = this.myFuneral.wedding_text;
            return sources;
        }
    },
    created() {
        if (this.myFuneral.dob) this.myFuneral.dob = moment(this.myFuneral.dob).format('DD.MM.YYYY');
        if (this.myFuneral.dod) this.myFuneral.dod = moment(this.myFuneral.dod).format('DD.MM.YYYY');
        if (this.myFuneral.announcement) this.myFuneral.announcement = moment(this.myFuneral.announcement).format('DD.MM.YYYY');
        if (this.myFuneral.wake) this.myFuneral.wake = moment(this.myFuneral.wake).format('DD.MM.YYYY');
        if (this.myFuneral.appointment) this.myFuneral.appointment = moment(this.myFuneral.appointment).format('DD.MM.YYYY HH:mm');
    },
    mounted() {
        this.refreshKey++;
        this.$forceUpdate();
    },
    data() {
        var myFuneral = this.funeral;
        myFuneral.life = myFuneral.life || '';

        let ls = this.getLocalStorage();
        let inLocalStorage = (undefined !== ls.funerals[this.funeral.id]);

        return {
            formKey: 0,
            referenceCopied: 0,
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
            showStoryEditor: false,
            myFuneral: myFuneral,
            copied: 0,
            textEditorActive: false,
            editorOption: {
                placeholder: 'Hier kannst du einen Textentwurf für den Lebenslauf schreiben...',
                modules: {
                    toolbar: {
                        container: '#toolbar',
                        handlers: {
                            custom: this.quillInsertText,
                        }
                    },
                    clipboard: {
                        matchVisual: false,
                    },
                }
            },
            inLocalStorage,
        }
    },
    methods: {
        copyAddress() {
            this.myFuneral.relative_address = this.myFuneral.buried_address;
            this.myFuneral.relative_zip = this.myFuneral.buried_zip;
            this.myFuneral.relative_city = this.myFuneral.buried_city;
            this.copied++;
            this.$forceUpdate();
        },
        copyBuriedAddress() {
            this.myFuneral.appointment_address = this.myFuneral.buried_address + ', ' + this.myFuneral.buried_zip + ' ' + this.myFuneral.buried_city;
        },
        copyRelativeAddress() {
            this.myFuneral.appointment_address = this.myFuneral.relative_address + ', ' + this.myFuneral.relative_zip + ' ' + this.myFuneral.relative_city;
        },
        saveFuneral() {
            let record = __.clone(this.myFuneral);
            ['baptism_date', 'confirmation_date', 'wedding_date', 'dod_spouse'].forEach(key => {
                if (record[key] && (record[key].length != 10)) record[key] = moment(record[key]).format('DD.MM.YYYY');
            });
            this.$inertia.patch(route('funerals.update', this.myFuneral.id), record, {
                preserveState: false,
            });
        },
        deleteFuneral() {
            this.$inertia.delete(route('funerals.destroy', this.myFuneral.id), {
                preserveState: false,
            })
        },
        downloadForm() {
            window.location.href = route('funeral.form', {funeral: this.myFuneral.id});
        },
        relativeDate: RelativeDate,
        quillInsertText(e) {
            var quill = this.$refs.textEditor.quill;
            var text = null;

            switch (e) {
                case 'dob':
                    text = moment(this.myFuneral.dob, 'DD.MM.YYYY').locale('de').format('LL');
                    break;
                case 'dod':
                    text = moment(this.myFuneral.dod, 'DD.MM.YYYY').locale('de').format('LL');
                    break;
                case 'age':
                    text = this.age.toString();
                    break;
                default:
                    if (this.myFuneral[e]) text = this.myFuneral[e];
            }

            if (text) quill.insertText(quill.getSelection(true).index, text);
        },
        setFuneralText(t) {
            this.myFuneral.text = t;
            this.referenceCopied++;
        },
        dateFromString(s) {
            if (!s) return false;
            if (s.length != 10) return moment(s);
            return moment(s.substr(6, 4) + '-' + s.substr(3, 2) + '-' + s.substr(0, 2));
        },
        ageText(date, startDate, prefix, suffix) {
            startDate = this.dateFromString(startDate || this.myFuneral.dob);
            date = this.dateFromString(date);
            if (!(startDate && date)) return '';
            suffix = suffix || '';
            let dayDiff = date.diff(startDate, 'days');
            if (dayDiff < 14) return prefix + String(dayDiff) + ' Tage' + suffix;
            if (dayDiff < 60) return prefix + 'ca. ' + String(Math.floor(dayDiff / 7)) + ' Wochen';
            if (dayDiff < 365) return prefix + 'ca. ' + String(date.diff(startDate, 'months')) + ' Monate' + suffix;
            return prefix + String(date.diff(startDate, 'years')) + ' Jahre' + suffix;
        },
        downloadAsJson(exportObj, exportName) {
            var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(this.myFuneral));
            var downloadAnchorNode = document.createElement('a');
            downloadAnchorNode.setAttribute("href", dataStr);
            downloadAnchorNode.setAttribute("download", exportName + ".json");
            document.body.appendChild(downloadAnchorNode); // required for firefox
            downloadAnchorNode.click();
            downloadAnchorNode.remove();
        },
        saveInLocalStorage() {
            let ls = this.getLocalStorage();
            ls.funerals[this.myFuneral.id] = this.myFuneral;
            localStorage.pfarrplaner = JSON.stringify(ls);
            this.inLocalStorage = true;
        },
        loadFromLocalStorage() {
            let ls = this.getLocalStorage();
            if (ls.funerals[this.myFuneral.id]) {
                this.myFuneral = ls.funerals[this.myFuneral.id];
                this.formKey++;
                this.$forceUpdate();
                this.deleteFromLocalStorage();
            }
        },
        deleteFromLocalStorage() {
            let ls = this.getLocalStorage();
            if (ls.funerals[this.myFuneral.id]) delete ls.funerals[this.myFuneral.id];
            localStorage.pfarrplaner = JSON.stringify(ls);
            this.inLocalStorage = false;
        },
        getLocalStorage() {
            let ls = JSON.parse(localStorage.getItem('pfarrplaner')) || {};
            ls.funerals = ls.funerals || {};
            return ls;
        }
    }
}
</script>

<style scoped>

.attachment {
    width: 100%;
    text-align: left;
    margin-bottom: .25rem;
    vertical-align: middle;
}

.mdi-download {
    margin-right: 20px;
    color: gray !important;
}

</style>
