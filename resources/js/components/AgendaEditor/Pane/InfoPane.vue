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
    <div class="card agenda-editor-info-pane">
        <form @submit.prevent="saveAgendaInfo">
            <div class="card-body">
                <div v-if="!editing" class="row">
                    <div class="col-sm-8">
                        <h1>{{ agenda.title }}</h1>
                    </div>
                    <div class="col-sm-4 text-right">
                        <button class="btn btn-light" @click="editAgendaInfo"><span class="fa fa-edit"></span>
                            Bearbeiten
                        </button>
                        <inertia-link :href="route('liturgy.agenda.index')" class="btn btn-light">Zur Liste</inertia-link>
                    </div>
                </div>
                <div v-if="editing" class="form-group">
                    <label for="title">Titel</label>
                    <input class="form-control" type="text" name="title" v-model="agenda.title" v-focus/>
                </div>
                <div v-if="!editing">
                    {{ agenda.description }}
                </div>
                <div v-if="editing" class="form-group">
                    <label for="description">Beschreibung</label>
                    <textarea class="form-control" name="description" v-model="agenda.description"/>
                </div>
                <small v-if="!editing && (agenda.internal_remarks != '')">Quelle: {{ agenda.internal_remarks }}</small>
                <div v-if="editing" class="form-group">
                    <label for="description">Quelle</label>
                    <textarea class="form-control" name="description" v-model="agenda.internal_remarks"/>
                </div>
                <div v-if="editing" class="form-group">
                    <label for="code"></label>
                    <input class="form-control" type="text" name="code" v-model="agenda.special_location"/>
                </div>
                <div v-if="editing" class="form-group">
                    <button class="btn btn-primary">Speichern</button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    name: "InfoPane",
    data() {
        return {
            editing: (this.agenda.id == undefined),
        };
    },
    props: ['agenda'],
    methods: {
        editAgendaInfo() {
            this.editing = true;
        },
        saveAgendaInfo() {
            if (undefined == this.agenda.id) {
                this.$inertia.post(route('liturgy.agenda.store'), this.agenda, { preserveState: false});
            } else {
                this.$inertia.patch(route('liturgy.agenda.update', this.agenda.id), this.agenda, { preserveState: false});
            }
        }
    }
}
</script>

<style scoped>
.agenda-editor-info-pane {
    font-size: 0.8em;
}

.litColor {
    border: solid 1px gray;
    min-width: 10px;
    min-height: 10px;
    border-radius: 5px;
    display: inline-block;
}
</style>
