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
        <div class="liturgy-item-psalm-editor">
            <div class="form-group">
                <label for="title">Titel im Ablaufplan</label>
                <input class="form-control" v-model="editedElement.title" v-focus/>
            </div>
            <div v-if="psalms === null">
                Bitte warten, Liste der Psalmen wird geladen...
            </div>
            <div v-else>
                <form-selectize name="psalm" label="Psalm" :value="selectedPsalm"
                                :options="psalms" :settings="{ searchField: ['name'], }"
                                @input="handlePsalmSelection" />
                <div v-if="(editedElement.data.psalm.id == -1) || editPsalm">
                    <div class="form-group">
                        <label for="title">Titel des Psalms</label>
                        <input class="form-control" v-model="editedElement.data.psalm.title"/>
                    </div>
                    <div class="form-group">
                        <label>Einleitende Worte</label>
                        <textarea class="form-control" v-model="editedElement.data.psalm.intro" placeholder="Leer lassen, wenn es keinen spezielle Einleitungstext gibt"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Text</label>
                        <textarea rows="10" class="form-control" v-model="editedElement.data.psalm.text"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Copyrights</label>
                        <textarea class="form-control" v-model="editedElement.data.psalm.copyrights"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-11">
                            <div class="form-group">
                                <label>Liederbuch</label>
                                <textarea class="form-control" v-model="editedElement.data.psalm.songbook"></textarea>
                            </div>
                        </div>
                        <div class="col-1 form-group">
                            <label>Abkürzung</label>
                            <input type="text" class="form-control" v-model="editedElement.data.psalm.songbook_abbreviation"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="reference">Liednummer</label>
                        <input class="form-control" v-model="editedElement.data.psalm.reference"/>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-sm btn-light" @click.prevent="saveText" v-if="!editPsalm">Als neuen Psalm speichern</button>
                        <button class="btn btn-sm btn-light" @click.prevent="updateText" v-if="editPsalm" :title="psalmIsDirty ? 'Es existieren ungespeicherte Änderungen am Lied.' : ''">
                            <span v-if="psalmIsDirty" class="fa fa-exclamation-triangle" style="color:red;"></span>
                            Änderungen am Psalm speichern
                        </button>
                    </div>
                </div>
                <div v-else>
                    <button class="btn btn-sm btn-light" @click.prevent="editPsalm = true">Psalm bearbeiten</button>
                    <div class="liturgical-text-quote">
                        <p v-if="editedElement.data.psalm.title"><b>{{ editedElement.data.psalm.title }}</b></p>
                        <p v-if="editedElement.data.psalm.intro"><i>{{ editedElement.data.psalm.intro }}</i></p>
                        <nl2br v-if="editedElement.data.psalm.text" tag="p" :text="editedElement.data.psalm.text" />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-primary" @click="save" :disabled="psalmIsDirty" :title="psalmIsDirty ? 'Du musst zuerst die Änderungen am Lied speichern.' : ''">Speichern</button>
                <inertia-link class="btn btn-secondary" :href="route('services.liturgy.editor', this.service.id)">
                    Abbrechen
                </inertia-link>
            </div>
        </div>
    </form>
</template>

<script>
import Nl2br from 'vue-nl2br';
import Selectize from 'vue2-selectize';
import FormSelectize from "../../Ui/forms/FormSelectize";

export default {
    name: "PsalmEditor",
    components: {
        Nl2br,
        Selectize,
        FormSelectize,
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
     * Load existing psalms
     * @returns {Promise<void>}
     */
    async created() {
        const psalms = await axios.get(route('liturgy.psalm.index'))
        if (psalms.data) {
            psalms.data.forEach(psalm => {
                psalm['name'] = this.displayTitle(psalm);
            });
            this.psalms = psalms.data;
        }
    },
    data() {
        var emptyPsalm = {
            id: -1,
            title: '',
            intro: '',
            text: '',
            copyrights: '',
            songbook: '',
            songbook_abbreviation: '',
            reference: '',
        };

        if (undefined == this.element.data.psalm) {
            this.element.data = {};
            this.element.data['psalm'] = {};
            this.element.data['psalm'] = emptyPsalm;
        }
        var editedElement = this.element;
        return {
            editedElement: editedElement,
            emptyPsalm: emptyPsalm,
            editPsalm: false,
            psalms: null,
            psalmIsDirty: false,
            selectedPsalm: editedElement.data.psalm.id,
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
            axios.post(route('liturgy.psalm.store'), this.editedElement.data.psalm).then(response => {
                return response.data;
            }).then(data => {
                this.editPsalm = false;
                this.psalms = data.psalms;
                this.editedElement.data.psalm = data.psalm;
                this.psalmIsDirty = false;
            });
            this.psalmIsDirty = false;
        },
        updateText() {
            axios.patch(route('liturgy.psalm.update', this.editedElement.data.psalm.id), this.editedElement.data.psalms).then(response => {
                return response.data;
            }).then(data => {
                this.editPsalm = false;
                this.psalms = data.psalms;
                this.editedElement.data.psalm = data.psalm;
                this.psalmIsDirty = false;
            });
            this.psalmIsDirty = false;
        },
        displayTitle(psalm) {
            var title = psalm.title;
            if (psalm.reference)  {
                title = psalm.reference+' '+title;
            }
            if (psalm.songbook_abbreviation) {
                title = psalm.songbook_abbreviation+' '+title;
            } else if (psalm.songbook) {
                title = psalm.songbook+' '+title;
            }
            return title;
        },
        quotableText() {
            var text = '';
            var title = this.displayTitle(this.editedElement.data.psalm);
            if (title.trim().length > 0) {
                text = '<b>'+title+"</b>\n\n";
            }
            text = text + this.editedElement.data.psalm.text;

            return text;
        },
        handlePsalmSelection(e) {
            this.selectedPsalm = e;

            var found = false;
            this.psalms.forEach(function(psalm) {
                if (psalm.id == e) found = psalm;
            })
            if (found) this.editedElement.data.psalm = found;
            console.log('found', found, this.editedElement.data.psalm);
            this.psalmIsDirty = this.editPsalm;
        }
    }
}
</script>

<style scoped>
.liturgy-item-psalm-editor {
    padding: 5px;
}

.liturgical-text-quote {
    padding: 20px;
    border-left: solid 3px lightgray;
    margin: 10px;
}
</style>
