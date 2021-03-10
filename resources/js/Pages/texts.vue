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
    <admin-layout enable-control-sidebar="true" title="Texte bearbeiten">
        <div v-if="!importMode">
            <div v-if="texts.length > 0">
                <div class="card" v-if="selectedText">
                    <div class="card-header">Text bearbeiten</div>
                    <liturgic-editor :agenda-mode="true" :element="getElement(selectedText)" :open-to-edit="true"/>
                </div>
                <table class="table table-striped table-hover" v-else>
                    <thead>
                    <tr>
                        <th>Text</th>
                        <th>Code</th>
                        <th>Quelle</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="text in texts" @click="openText(text)" style="cursor: pointer"
                        title="Klicken, um auszuwählen">
                        <td valign="top">{{ title(text) }}</td>
                        <td valign="top">{{ text.agenda_code }}</td>
                        <td valign="top">{{ text.source }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div v-else>Es sind noch keine Texte angelegt.</div>
            <button class="btn btn-success" @click="createText">Neuen Text anlegen</button>
            <button class="btn btn-secondary" @click="importMode = true">Texte importieren</button>
        </div>
        <div v-else>
            <div class="form-group">
                <label>Importdatei hier einfügen:</label>
                <textarea class="form-control" v-model="importText" rows="15"/>
            </div>
            <div class="form-group">
                <button class="btn btn-primary" @click.prevent="importTexts">Importieren</button>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import LiturgicEditor from "../components/LiturgyEditor/Editors/LiturgicEditor";

export default {
    props: ['texts'],
    components: {
        LiturgicEditor
    },
    data() {
        return {
            selectedText: null,
            importMode: false,
            importText: '',
        }
    },
    methods: {
        title(text) {
            if (text.title) return text.title;
            var s = text.text.split("\n")[0];
            if (s.length > 200) s = s.substr(0,200);
            s += '...';
            return s;
        },
        createText() {
            axios.get(route('liturgy.text.create'))
                .then(response => {
                    return response.data;
                }).then(data => {
                this.selectedText = data;
            });
        },
        getElement(text) {
            return {
                data: text,
            }
        },
        openText(text) {
            this.selectedText = text;
        },
        importTexts() {
            this.$inertia.post(route('liturgy.text.import'), {
                data: this.importText,
            }, {preserveState: false})
        }
    }
}
</script>
<style scoped>
</style>
