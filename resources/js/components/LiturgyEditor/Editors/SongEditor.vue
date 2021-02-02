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
        <div class="liturgy-item-song-editor card">
            <div class="card-header">Lied/Musikstück bearbeiten</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="title">Titel des Abschnitts</label>
                    <input class="form-control" v-model="editedElement.title" v-focus/>
                </div>
                <div v-if="count(this.songs) == 0">
                    Bitte warten, Liederliste wird geladen...
                </div>
                <div v-else>
                    Liederliste geladen.
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-primary" @click="save">Speichern</button>
                <inertia-link class="btn btn-secondary" :href="route('services.liturgy.editor', this.service.id)">
                    Abbrechen
                </inertia-link>
            </div>
        </div>
    </form>
</template>

<script>
export default {
    name: "SongEditor",
    props: ['element', 'service'],
    data() {
        var e = this.element;
        if (undefined == e.data.description) e.data.description = '';
        return {
            editedElement: e,
            songs: {},
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

</style>
