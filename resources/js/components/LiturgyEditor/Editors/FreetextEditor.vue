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
        <div class="liturgy-item-freetext-editor">
                <div class="form-group">
                    <label for="title">Titel im Ablaufplan</label>
                    <input class="form-control" v-model="editedElement.title" v-focus/>
                </div>
                <div class="form-group">
                    <label for="description">Beschreibender Text</label>
                    <textarea class="form-control" v-model="editedElement.data.description"></textarea>
                    <text-stats :text="editedElement.data.description" />
                </div>
                <div class="help">
                    <div class="help-title">Verfügbare Platzhalter:</div>
                    <div v-for="(marker,key) in markers" :key="key" class="row">
                        <div class="col-2">[{{ key }}]</div>
                        <div class="col-10">{{ marker }}</div>
                    </div>
                </div>
            <time-fields :service="service" :element="element" :agenda-mode="agendaMode" />
            <div class="form-group">
                <button class="btn btn-primary" @click="save">Speichern</button>
                <inertia-link class="btn btn-secondary" :href="route('liturgy.editor', this.service.slug)">
                    Abbrechen
                </inertia-link>
            </div>
        </div>
    </form>
</template>

<script>
import TextStats from "../Elements/TextStats";
import TimeFields from "./Elements/TimeFields";
export default {
    name: "FreetextEditor",
    components: {TimeFields, TextStats},
    props: {
        element: Object,
        service: Object,
        agendaMode: {
            type: Boolean,
            default: false,
        },
        markers: {
            type: Object,
            default: null,
        }
    },
    data() {
        var e = this.element;
        if (undefined == e.data.description) e.data.description = '';
        return {
            editedElement: e,
        };
    },
    methods: {
        save: function () {
            this.$inertia.patch(route('liturgy.item.update', {
                service: this.service.id,
                block: this.element.liturgy_block_id,
                item: this.element.id,
            }), this.element, {preserveState: false});
        },
    }
}
</script>

<style scoped>
    .liturgy-item-freetext-editor {
        padding: 5px;
    }

    .help {
        margin-top: .5em;
        margin-bottom: .5em;
        font-size: .8em;
    }

    .help-title {
        font-weight: bold;
        margin-bottom: .25em;
    }
</style>
