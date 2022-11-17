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
    <div class="sermon-editor">
        <admin-layout title="Predigt bearbeiten">
            <template slot="navbar-left">
                <button class="btn btn-primary" @click.prevent="saveSermon">Speichern</button>&nbsp;
            </template>
            <form @submit.prevent="saveSermon" id="formSermon">
                <div v-if="services.length >0" class="mb-3">
                    <div class="row py-1 border-bottom mb-1" v-for="service in services">
                        <div class="col-md-8">
                            {{ service.titleText }} am {{ moment(service.date).format('DD.MM.YYYY') }},
                            {{ service.timeText }}, {{ service.locationText }}
                        </div>
                        <div class="col-md-4 text-right">
                            <inertia-link class="btn btn-sm btn-light"
                                          :href="route('service.edit', {service: service.slug})"
                                          title="Gottesdienst bearbeiten"><span class="mdi mdi-pencil"></span>
                                Gottesdienst
                            </inertia-link>
                            <inertia-link class="btn btn-sm btn-light"
                                          :href="route('liturgy.editor', {service: service.slug})"
                                          title="Liturgie bearbeiten">
                                <span class="mdi mdi-view-list"></span> Liturgie
                            </inertia-link>
                            <span v-if="undefined != editedSermon.id">
                                <button v-if="services.length > 1" class="btn btn-sm btn-secondary"
                                        @click.prevent.stop="uncoupleService(service)"
                                        title="Gottesdienst entkoppeln">
                                    <span class="mdi mdi-link-off"></span>
                                </button>
                                <button v-else class="btn btn-sm btn-danger"
                                        @click.prevent.stop="uncoupleService(service)"
                                        title="Predigt entfernen">
                                    <span class="mdi mdi-delete"></span>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Titel</label>
                            <input class="form-control" type="text" v-model="editedSermon.title" v-focus/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Untertitel</label>
                            <input class="form-control" type="text" v-model="editedSermon.subtitle"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <form-bible-reference-input name="reference" :key="referenceCopied"
                                                    label="Predigttext"
                                                    v-model="editedSermon.reference" :sources="textSources" />
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Reihe</label>
                            <input class="form-control" type="text" v-model="editedSermon.series"/>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Zusammenfassung</label>
                    <textarea class="form-control" v-model="editedSermon.summary"/>
                </div>
                <div class="form-group">
                    <label>Text der Predigt</label>
                    <!-- HERE --->

                    <quill-editor :class="{focused: textEditorActive}" ref="textEditor"
                                  v-model="editedSermon.text" :key="textUpdated"
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
                            <button class="ql-insertbible quill-mdi-button" title="Bibeltext hinzufügen"><span
                                class="mdi mdi-book-open-variant"></span></button>
                        </div>
                    </quill-editor>

                    <text-stats :text="editedSermon.text"  :key="textUpdated"/>
                    <div v-if="funerals.length > 0" class="mt-1 mb-3">
                        <nav-button v-for="(funeral, funeralIndex, funeralKey) in funerals" type="light" class="mr-1"
                                    :key="funeralKey"
                                    icon="mdi mdi-text" :title="'Lebenslauf von '+funeral.buried_name+' in den Text einfügen'"
                                    @click="insertFuneralStory(funeral)">
                            Lebenslauf von {{ funeral.buried_name }} einfügen
                        </nav-button>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Überschrift für die Hauptpunkte</label>
                            <input class="form-control" type="text" v-model="editedSermon.notes_header"/>
                        </div>
                        <div class="form-group">
                            <label>Hauptpunkte</label>
                            <textarea class="form-control" v-model="editedSermon.key_points"
                                      aria-describedBy="helpKeyPoints"/>
                            <small id="helpKeyPoints" class="form-text text-muted">Ein Hauptpunkt pro Zeile,
                                Lücken für Lückentext mit [ ] markieren</small>
                        </div>
                        <div class="form-group">
                            <label>Fragen für die Zuhörer</label>
                            <textarea class="form-control" v-model="editedSermon.questions" rows="6"
                                      aria-describedby="helpQuestions"/>
                            <small id="helpQuestions" class="form-text text-muted">Eine Frage pro Zeile</small>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="inputCCLicense"
                                   v-model="editedSermon.cc_license" value="1"/>
                            <label class="form-check-label" for="inputCCLicense">Predigt und Materialien unter
                                der CC-BY-SA 4.0 Lizenz freigeben</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="inputPermitHandouts"
                                   v-model="editedSermon.permit_handouts" value="1"/>
                            <label class="form-check-label" for="inputPermitHandouts">Handouts freigeben</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div v-if="!(editedSermon.id)" class="alert alert-info">
                            Du musst die Predigt erst einmal speichern, um ein Bild hinzufügen zu können.
                        </div>
                        <div v-else>
                            <form-image-attacher
                                :attach-route="route('sermon.image.attach', {model: editedSermon.id})"
                                :detach-route="route('sermon.image.detach', {model: editedSermon.id})"
                                label="Bild zur Predigt" :handle-paste="true"
                                v-model="editedSermon.image" width="1024" height="768"
                            />
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Literaturhinweise</label>
                    <quill-editor :class="{focused: literatureEditorActive}" ref="literatureEditor"
                                  v-model="editedSermon.literature"
                                  :options="editorOptionListOnly" @focus="literatureEditorActive = true"
                                  @blur="literatureEditorActive = false"/>
                </div>
            </form>
        </admin-layout>
    </div>
</template>

<script>
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'
import '../components/SermonEditor/quill.css';

import {quillEditor} from 'vue-quill-editor';
import FormFileUploader from "../components/Ui/forms/FormFileUploader";
import FormImageAttacher from "../components/Ui/forms/FormImageAttacher";
import TextStats from "../components/LiturgyEditor/Elements/TextStats";
import Card from "../components/Ui/cards/card";
import CardBody from "../components/Ui/cards/cardBody";
import FormBibleReferenceInput from "../components/Ui/forms/FormBibleReferenceInput";
import NavButton from "../components/Ui/buttons/NavButton";

export default {
    name: "sermonEditor",
    components: {
        NavButton,
        FormBibleReferenceInput,
        CardBody,
        Card,
        TextStats,
        FormImageAttacher,
        FormFileUploader,
        quillEditor,
    },
    props: {
        sermon: Object,
        services: Array,
        service: {
            type: Object,
            default: null,
        },
    },
    computed: {
        funerals() {
            let f = [];
            this.services.forEach(s => {
                if (s.funerals.length > 0) {
                    s.funerals.forEach(funeral => {
                        f.push(funeral);
                    });
                }
            });
            return f;
        }
    },
    data() {
        var emptySermon = {
            title: '',
            subtitle: '',
            reference: '',
            series: '',
            summary: '',
            text: '',
            image: '',
            notes_header: '',
            key_points: '',
            questions: '',
            literature: '',
            audio_recording: '',
            video_url: '',
            external_url: '',
            cc_license: false,
            permit_handouts: false,
        };
        var editedSermon = this.sermon ? this.sermon : emptySermon
        if (null === editedSermon.text) editedSermon.text = '';

        let allServices = this.services || [this.service];
        let textSources = {};
        allServices.forEach(thisService => {
            if (undefined !== thisService.liturgicalInfo.title) {
                textSources['Perikope für '+thisService.liturgicalInfo.title] = thisService.liturgicalInfo.currentPerikope;
                for (let i=1; i<=6; i++) {
                    textSources[thisService.liturgicalInfo.title+' '+this.romanize(i)] = thisService.liturgicalInfo['litTextsPerikope'+i];
                }
                textSources[thisService.liturgicalInfo.title+' Psalm'] = thisService.liturgicalInfo['litTextsWeeklyPsalm'];
                textSources[thisService.liturgicalInfo.title+' Wochenspruch'] = thisService.liturgicalInfo['litTextsWeeklyQuote'];
            }
            thisService.baptisms.forEach(baptism => {
                if (baptism.text) textSources['Taufspruch '+baptism.candidate_name] = baptism.text;
            });
            thisService.funerals.forEach(funeral => {
                if (funeral.text) textSources['Beerdigungstext '+funeral.buried_name] = funeral.text;
                if (funeral.confirmation_text) textSources['Denkspruch '+funeral.buried_name] = funeral.confirmation_text;
                if (funeral.wedding_text) textSources['Trauspruch '+funeral.buried_name] = funeral.wedding_text;
            });
            thisService.weddings.forEach(wedding => {
                if (wedding.text) textSources['Trauspruch '+wedding.spouse1_name+' & '+wedding.spouse2_name] = wedding.text;
            });
        });


        return {
            referenceCopied: 0,
            textEditorActive: false,
            literatureEditorActive: false,
            editedSermon: editedSermon,
            editorOption: {
                placeholder: 'Schreibe hier den Text deiner Predigt hin...',
                modules: {
                    toolbar: {
                        container: '#toolbar',
                        handlers: {
                            custom: this.quillInsertText,
                            insertbible: this.quillInsertBible,
                        }
                    },
                    clipboard: {
                        matchVisual: false,
                    },
                }
            },
            editorOptionListOnly: {
                placeholder: 'Hier gibt es Platz z.B. für eine Literaturliste...',
                modules: {
                    toolbar: [
                        ['italic'],        // toggled buttons
                        [{'list': 'ordered'}, {'list': 'bullet'}],
                        ['clean']                                         // remove formatting button
                    ],
                    clipboard: {
                        matchVisual: false,
                    },
                }
            },
            fileUpload: null,
            removeImage: false,
            textSources,
            textUpdated: 0,
        }
    },
    methods: {
        saveSermon() {
            let formData = new FormData(document.getElementById('formSermon'));
            for (const [key, value] of Object.entries(this.editedSermon)) {
                if (key != 'image') formData.append(key, value || '');
            }
            if (!formData.has('cc_license')) formData.append('cc_license', 0);
            if (!formData.has('permit_handouts')) formData.append('permit_handouts', 0);
            if (this.removeImage) formData.append('remove_image', 1);
            if (this.fileUpload) {
                formData.append('image', this.fileUpload);
            }
            if (undefined === this.editedSermon.id) {
                this.$inertia.post(route('sermon.store', {service: this.service.slug}), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    preserveState: false,
                });
            } else {
                formData.append('_method', 'PATCH');
                this.$inertia.post(route('sermon.update', {sermon: this.editedSermon.id}), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    preserveState: false,
                });
            }
        },
        uncoupleService(service) {
            if ((this.services.length > 1) || confirm('Diese Predigt ist nur mit einem Gottesdienst verbunden. Wenn du diese Verbindung trennst, wird die Predigt gelöscht. Willst du das wirklich?')) {
                this.$inertia.delete(route('sermon.uncouple', {service: service.slug}), {preserveState: false});
            }
        },
        setImage(event) {
            this.fileUpload = event.target.files[0];
        },
        quillInsertText(e) {
            var quill = this.$refs.textEditor.quill;
            var text = null;

            switch (e) {
                default:
                    text = e;
            }

            if (text) quill.insertText(quill.getSelection(true).index, text);
        },
        quillInsertBible() {
            var reference = window.prompt('Welche Bibelstelle möchtest du einfügen?');
            axios.get(route('bible.text', {
                reference: reference,
                showReference: 1,
                showVerseNumbers: 0
            })).then(result => {
                if (result.data.text) {
                    this.quillInsertText(result.data.text);
                } else {
                    alert('Zu dieser Stellenangabe konnte kein Text gefunden werden.');
                }
            });
        },
        setSermonReference(ref) {
            this.editedSermon.reference = ref;
            this.referenceCopied++;
        },
        /**
         * @source http://blog.stevenlevithan.com/archives/javascript-roman-numeral-converter
         * @param num
         * @returns {string|number}
         */
        romanize(num) {
            if (isNaN(num))
                return NaN;
            var digits = String(+num).split(""),
                key = ["", "C", "CC", "CCC", "CD", "D", "DC", "DCC", "DCCC", "CM",
                    "", "X", "XX", "XXX", "XL", "L", "LX", "LXX", "LXXX", "XC",
                    "", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX"],
                roman = "",
                i = 3;
            while (i--)
                roman = (key[+digits.pop() + (i * 10)] || "") + roman;
            return Array(+digits.join("") + 1).join("M") + roman;
        },
        insertFuneralStory(funeral) {
            if (this.editedSermon.text.trim() != '') this.editedSermon.text += "\n\n";
            this.editedSermon.text += funeral.life;
            this.textUpdated++;
        }
    }
}
</script>

<style scoped>
.ql-toolbar .quill-mdi-button {
    padding-top: 1px;
}
>>> .ql-container.ql-snow,
>>> .ql-container.ql-snow .ql-editor {
    font-family: inherit !important;
    font-weight: normal;
}


</style>
