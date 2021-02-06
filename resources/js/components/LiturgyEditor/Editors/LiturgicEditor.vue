<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
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
    <form @submit.prevent="save">
        <div class="liturgy-item-liturgic-editor">
            <default-fields :service="service" :element="editedElement" :agenda-mode="agendaMode"/>
            <div v-if="agendaMode" class="form-group">
                <label for="text_filter">Agenda-Code zum Filtern der Textliste</label>
                <input class="form-control" type="text" name="text_filter" v-model="editedElement.data.text_filter"/>
            </div>
            <div v-if="this.texts === null">
                Bitte warten, Textliste wird geladen...
            </div>
            <div v-else>
                <div class="form-group">
                    <label for="existing_text">Liturgische Texte</label>
                    <selectize class="form-control" name="existing_text" v-model="selectedText">
                        <option value="-1"></option>
                        <optgroup v-if="(editedElement.data.text_filter != '')" label="Vorgeschlagene Texte">
                            <option v-for="text in texts"
                                    v-if="editedElement.data.text_filter == text.agenda_code"
                                    :value="text.id">
                                <div><span class="option-text-title">{{ text.title }}</span> <span class="option-text-source">{{ (text.source ? '('+text.source+')' : '')}}</span></div>
                            </option>
                        </optgroup>
                        <optgroup v-if="(editedElement.data.text_filter != '')" label="Andere Texte">
                            <option v-for="text in texts"
                                    v-if="editedElement.data.text_filter != text.agenda_code"
                                    :value="text.id">
                                <div><span class="option-text-title">{{ text.title }}</span> <span class="option-text-source">{{ (text.source ? '('+text.source+')' : '')}}</span></div>
                            </option>
                        </optgroup>
                        <option v-if="(editedElement.data.text_filter == '')" v-for="text in texts" :value="text.id">
                            <div><span class="option-text-title">{{ text.title }}</span> <span class="option-text-source">{{ (text.source ? '('+text.source+')' : '')}}</span></div>
                        </option>
                    </selectize>
                </div>
                <div v-if="editedElement.data.needs_replacement == 'funeral'" class="form-group">
                    <label>Platzhalter ersetzen für Beerdigung</label>
                    <select class="form-control" v-model="editedElement.data.replacement">
                        <option v-for="funeral in service.funerals" :value="funeral.id">{{ funeral.buried_name }}</option>
                    </select>
                </div>
                <div v-if="editedElement.data.needs_replacement == 'baptism'" class="form-group">
                    <label>Platzhalter ersetzen für Taufe</label>
                    <select class="form-control" v-model="editedElement.data.replacement">
                        <option v-for="funeral in service.baptisms" :value="baptism.id">{{ funeral.candidate_name }}</option>
                    </select>
                </div>
                <div v-if="editedElement.data.needs_replacement == 'wedding'" class="form-group">
                    <label>Platzhalter ersetzen für Trauung</label>
                    <select class="form-control" v-model="editedElement.data.replacement">
                        <option v-for="funeral in service.wedding" :value="wedding.id">{{ funeral.spouse1_name }} &amp; {{ funeral.spouse2_name }} </option>
                    </select>
                </div>
                <div v-if="editedElement.data.id == -1">
                    <div class="form-group">
                        <label for="title">Titel des Texts</label>
                        <input class="form-control" v-model="editedElement.data.title"/>
                    </div>
                    <div class="form-group">
                        <label for="text">Text</label>
                        <textarea class="form-control" v-model="editedElement.data.text"/>
                    </div>
                    <div class="form-group">
                        <label for="agenda_code">Agenda-Code</label>
                        <input class="form-control" v-model="editedElement.data.agenda_code"/>
                    </div>
                    <div class="form-group">
                        <label for="source">Quellenangaben</label>
                        <textarea class="form-control" v-model="editedElement.data.source"/>
                    </div>
                    <div v-if="agendaMode">
                        <label for="needs_replacement">Enthält automatisch zu ersetzende Tags:</label>
                        <select class="form-control" name="needs_replacement"
                                v-model="editedElement.data.needs_replacement">
                            <option value="">Keine</option>
                            <option value="funeral">Bestattung</option>
                            <option value="baptism">Taufe</option>
                            <option value="wedding">Trauung</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-sm btn-light" @click.prevent="saveText">Als neuen Text speichern</button>
                    </div>
                </div>
                <div v-else class="liturgical-text-quote">
                    <nl2br tag="div" :text="editedElement.data.text"/>
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-primary" @click="save">Speichern</button>
                <inertia-link class="btn btn-secondary" :href="route('services.liturgy.editor', this.service.id)">
                    Abbrechen
                </inertia-link>
            </div>
        </div>
    </form>
</template>

<script>
import Nl2br from 'vue-nl2br';
import DefaultFields from "./Elements/DefaultFields";
import Selectize from "vue2-selectize";

export default {
    name: "LiturgicEditor",
    components: {
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
        }
    },
    /**
     * Load existing texts
     * @returns {Promise<void>}
     */
    async created() {
        const texts = await axios.get(route('liturgy.text.index'))
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
            texts: null,
            selectedText: e.data.id,
        };
    },
    watch: {
        selectedText: {
            handler: function(newVal, oldVal) {
                if (newVal == -1) {
                    var data = {
                        ...this.initialElement.data,
                        id: -1,
                        title: '',
                        text: '',
                        agenda_code: '',
                        needs_replacement: '',
                    }
                    this.editedElement.data = data;
                    return;
                }
                var found = false;
                this.texts.forEach(function (thisText){
                    if (thisText.id == newVal) found = thisText;
                });
                if (found) {
                    this.editedElement.data = {
                        ...this.editedElement.data,
                        ...found,
                    };
                    this.editedElement = this.checkReplacement(this.editedElement);
                }
            }
        }
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
            axios.post(route('liturgy.text.store'), this.editedElement.data)
            .then(response => {
                return response.data;
            }).then(data => {
                this.texts = data.texts;
                this.editedElement.data = data.text;
            });
        },
        checkReplacement(e) {
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
