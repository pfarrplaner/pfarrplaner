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
            <div class="form-group">
                <label for="title">Titel im Ablaufplan</label>
                <input class="form-control" v-model="editedElement.title" v-focus/>
            </div>
            <div v-if="this.texts.length == 0">
                Bitte warten, Textliste wird geladen...
            </div>
            <div v-else>
                <div class="form-group">
                    <label for="existing_text">Liturgische Texte</label>
                    <select class="form-control" name="existing_text" v-model="editedElement.data" @input="selectText">
                        <option :value="{id: -1, text: '', title: ''}"></option>
                        <option v-for="text in texts" :value="text">{{ text.title }}</option>
                    </select>
                </div>
                <div v-if="editedElement.data.id == -1">
                    <div class="form-group">
                        <label for="title">Titel des Texts</label>
                        <input class="form-control" v-model="editedElement.data.title"/>
                    </div>
                    <div class="form-group">
                        <label for="title">Text</label>
                        <textarea class="form-control" v-model="editedElement.data.text"/>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-sm btn-light" @click.prevent="saveText">Als neuen Text speichern</button>
                    </div>
                </div>
                <div v-else class="liturgical-text-quote">
                    <nl2br tag="div" :text="editedElement.data.text" />
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

export default {
    name: "LiturgicEditor",
    components: {
        Nl2br,
    },
    props: ['element', 'service'],
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
        return {
            editedElement: e,
            texts: {},
            selectedText: {
                id: -1,
                title: '',
                text: '',
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
            axios.post(route('liturgy.text.store'), {
                title: this.editedElement.data.title,
                text: this.editedElement.data.text,
            }).then(response => {
                return response.data;
            }).then(data => {
                this.texts = data.texts;
                this.editedElement.data = data.text;
            });
        },
        selectText() {
            this.editedElement.data = this.selectedText;
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
</style>
