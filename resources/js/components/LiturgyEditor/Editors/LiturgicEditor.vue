<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
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
    <form @submit.prevent="save">
        <div class="liturgy-item-liturgic-editor">
            <div class="row">
                <div class="col-md-8">
                    <default-fields v-if="service" :service="service" :element="editedElement"
                                    :agenda-mode="agendaMode"/>
                    <div v-if="(agendaMode) && (service)" class="form-group">
                        <label for="text_filter">Agenda-Code zum Filtern der Textliste</label>
                        <input class="form-control" type="text" name="text_filter"
                               v-model="editedElement.data.text_filter"/>
                    </div>
                    <div v-if="service">
                        <div class="form-group">
                            <label for="existing_text">Liturgische Texte</label>
                            <div v-if="texts.length == 0">
                                Bitte warten, Textliste wird geladen...
                            </div>
                            <selectize v-if="texts.length > 0" class="form-control" name="existing_text"
                                       :value="selectedText"
                                       :settings="settings"
                                       @input="chosen">
                                <optgroup v-if="(editedElement.data.text_filter != '')" label="Vorgeschlagene Texte">
                                    <option v-for="text in texts"
                                            v-if="editedElement.data.text_filter == text.agenda_code"
                                            :value="text.id">
                                        {{ text.title + (text.source ? '(' + text.source + ')' : '') }}
                                    </option>
                                </optgroup>
                                <optgroup v-if="(editedElement.data.text_filter != '')" label="Andere Texte">
                                    <option v-for="text in texts"
                                            v-if="editedElement.data.text_filter != text.agenda_code"
                                            :value="text.id">
                                        {{ text.title + (text.source ? '(' + text.source + ')' : '') }}
                                    </option>
                                </optgroup>
                                <option v-if="(editedElement.data.text_filter == '')" v-for="text in texts"
                                        :value="text.id">
                                    {{ text.title + (text.source ? '(' + text.source + ')' : '') }}
                                </option>
                            </selectize>
                        </div>
                        <div v-if="editedElement.data.needs_replacement == 'funeral'" class="form-group">
                            <label>Platzhalter ersetzen für Beerdigung</label>
                            <select class="form-control" v-model="editedElement.data.replacement">
                                <option v-for="funeral in service.funerals" :value="funeral.id">{{
                                        funeral.buried_name
                                    }}
                                </option>
                            </select>
                        </div>
                        <div v-if="editedElement.data.needs_replacement == 'baptism'" class="form-group">
                            <label>Platzhalter ersetzen für Taufe</label>
                            <select class="form-control" v-model="editedElement.data.replacement">
                                <option v-for="baptism in service.baptisms" :value="baptism.id">{{
                                        baptism.candidate_name
                                    }}
                                </option>
                            </select>
                        </div>
                        <div v-if="editedElement.data.needs_replacement == 'wedding'" class="form-group">
                            <label>Platzhalter ersetzen für Trauung</label>
                            <select class="form-control" v-model="editedElement.data.replacement">
                                <option v-for="wedding in service.weddings" :value="wedding.id">{{
                                        wedding.spouse1_name
                                    }}
                                    &amp; {{ wedding.spouse2_name }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div v-else class="liturgical-text-quote">
                        <nl2br tag="div" :text="editedElement.data.text"/>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-sm btn-light" @click.prevent="editText"
                                v-if="editedElement.data.id != -1">Text bearbeiten
                        </button>
                        <button class="btn btn-sm btn-light" @click.prevent="openNewTextForm">Neuer Text</button>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="liturgical-text-quote">
                        <nl2br tag="div" :text="editedElement.data.text" />
                    </div>
                    <text-stats class="mt-3" :text="editedElement.data.text" />
                </div>
            </div>
            <div class="form-group" v-if="service">
                <button class="btn btn-primary" @click="save">Speichern</button>
                <inertia-link class="btn btn-secondary"
                              :href="route('liturgy.editor', this.service.slug)">
                    Abbrechen
                </inertia-link>
            </div>
            <div class="form-group" v-else>
                <button class="btn btn-primary" @click="updateText">Speichern</button>
                <inertia-link class="btn btn-secondary"
                              :href="route('liturgy.text.index')">
                    Abbrechen
                </inertia-link>
            </div>

        </div>
        <modal :title="(editedElement.data.id == -1) ? 'Neuer Text' : 'Text bearbeiten'" v-if="editingText || newText"
               @close="newText ? saveText() : updateText()" @cancel="newText = editingText = false;"
               close-button-label="Text speichern" cancel-button-label="Abbrechen">
            <div class="form-group">
                <label for="title">Titel des Texts</label>
                <input class="form-control" v-model="modalText.title"/>
            </div>
            <div class="form-group">
                <label for="text">Text</label>
                <textarea class="form-control" v-model="modalText.text"/>
            </div>
            <div class="form-group">
                <label for="agenda_code">Agenda-Code</label>
                <input class="form-control" v-model="modalText.agenda_code"/>
            </div>
            <div class="form-group">
                <label for="source">Quellenangaben</label>
                <textarea class="form-control" v-model="modalText.source"/>
            </div>
            <div class="form-group">
                <label for="needs_replacement">Enthält automatisch zu ersetzende Tags:</label>
                <select class="form-control" name="needs_replacement"
                        v-model="modalText.needs_replacement">
                    <option value="">Keine</option>
                    <option value="funeral">Bestattung</option>
                    <option value="baptism">Taufe</option>
                    <option value="wedding">Trauung</option>
                </select>
            </div>
        </modal>

    </form>
</template>

<script>
import Nl2br from 'vue-nl2br';
import DefaultFields from "./Elements/DefaultFields";
import Selectize from "vue2-selectize";
import Modal from "../../Ui/modals/Modal";
import TextStats from "../Elements/TextStats";

export default {
    name: "LiturgicEditor",
    components: {
        TextStats,
        Modal,
        Nl2br,
        DefaultFields,
        Selectize,
    },
    props: {
        element: Object,
        service: Object,
        agendaMode: {
            type: Boolean,
            default: false,
        },
        openToEdit: {
            type: Boolean,
            default: false,
        }
    },
    /**
     * Load existing texts
     * @returns {Promise<void>}
     */
    async mounted() {
        const texts = await axios.get(route('liturgy.text.list'))
        if (texts.data) {
            this.texts = texts.data;
        }
    },
    data() {
        var e = this.element;
        if (undefined == e.data.id) e.data.id = -1;
        if (undefined == e.data.title) e.data.title = '';
        if (undefined == e.data.text) e.data.text = '';
        if (undefined == e.data.text_filter) e.data.text_filter = '';
        if (undefined == e.data.agenda_code) e.data.agenda_code = '';
        if (undefined == e.data.needs_replacement) e.data.needs_replacement = '';
        if (undefined == e.data.replacement) e.data.replacement = '';
        if (undefined == e.data.source) e.data.source = '';
        if (undefined == e.data.responsible) e.data.responsible = [];

        e = this.checkReplacement(e);

        return {
            initialElement: e,
            editedElement: e,
            texts: [],
            selectedText: e.data.id,
            editingText: this.openToEdit,
            newText: false,
            textOnlyMode: this.openToEdit,
            settings: {
                empty: true,
            },
            modalText: {
                title: '',
                text: '',
                agenda_code: '',
                source: '',
                needs_replacement: '',
            }
        };
    },
    methods: {
        save: function () {
            this.$inertia.patch(route('liturgy.item.update', {
                service: this.service.id,
                block: this.element.liturgy_block_id,
                item: this.element.id,
            }), this.editedElement, {preserveState: false});
        },
        saveText() {
            this.editingText = this.newText = false;
            this.texts = [];
            axios.post(route('liturgy.text.store'), this.modalText)
                .then(response => {
                    return response.data;
                }).then(data => {
                this.texts = data.texts;
                this.editedElement.data = {
                    ...this.editedElement.data,
                    id: data.text.id,
                    title: data.text.title,
                    text: data.text.text,
                    source: data.text.source,
                    agenda_code: data.text.agenda_code,
                    needs_replacement: data.text.needs_replacement,
                };
                this.selectedText = data.text.id;
            });
        },
        updateText() {
            this.editingText = this.newText = false;
            this.texts = [];
            axios.patch(route('liturgy.text.update', this.editedElement.data.id), this.modalText)
                .then(response => {
                    return response.data;
                }).then(data => {
                this.texts = data.texts;
                this.editedElement.data = {
                    ...this.editedElement.data,
                    id: data.text.id,
                    title: data.text.title,
                    text: data.text.text,
                    source: data.text.source,
                    agenda_code: data.text.agenda_code,
                    needs_replacement: data.text.needs_replacement,
                };
                this.selectedText = data.text.id;
                if (this.agendaMode && this.textOnlyMode) this.$inertia.visit(route('liturgy.text.index'));
            });
        },
        chosen(newVal) {
            var component = this;
            if (newVal == -1 || newVal == '') {
                this.editedElement.data = {
                    title: '',
                    text: '',
                    id: -1,
                    agenda_code: '',
                    source: '',
                    responsible: this.element.responsible,
                };
                this.selectedText = '';
                return;
            }
            this.texts.forEach(function (text) {
                if (newVal == text.id || newVal == text.id.toString()) {
                    component.editedElement.data = {
                        ...component.editedElement.data,
                        ...text
                    };
                    component.selectedText = text.id;
                }
            });
        },
        checkReplacement(e) {
            if (undefined === this.service) return e;
            var record = e;
            if ((record.data.needs_replacement == 'funeral') && (this.service.funerals.length > 0) && (record.data.replacement == '')) {
                record.data.replacement = this.service.funerals[0].id;
            }
            if ((record.data.needs_replacement == 'baptism') && (this.service.baptisms.length > 0) && (record.data.replacement == '')) {
                record.data.replacement = this.service.baptisms[0].id;
            }
            if ((record.data.needs_replacement == 'wedding') && (this.service.weddings.length > 0) && (record.data.replacement == '')) {
                record.data.replacement = this.service.weddings[0].id;
            }
            return record;
        },
        openNewTextForm() {
            this.modalText = {
                id: -1,
                title: '',
                text: '',
                agenda_code: '',
                source: '',
                needs_replacement: '',
            }
            this.newText = true;
        },
        editText() {
            if (this.editedElement.data.id) {
                this.modalText = {
                    title: this.editedElement.data.title,
                    text: this.editedElement.data.text,
                    agenda_code: this.editedElement.data.agenda_code,
                    source: this.editedElement.data.source,
                    needs_replacement: this.editedElement.data.needs_replacement,
                };
            }
            this.editingText = true;
        }
    }
}
</script>

<style scoped>
.liturgy-item-liturgic-editor {
    padding: 5px;
}

.liturgical-text-quote {
    padding: 20px;
    border-left: solid 3px lightgray;
    margin: 10px;
}

.option-text-title {
    font-weight: bold;
}

.option-text-source {
    font-weight: normal;
    font-size: .7em;
}
</style>
