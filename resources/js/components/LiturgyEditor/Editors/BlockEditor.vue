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
        <div class="liturgy-item-block-editor">
            <div class="form-group">
                <label for="title">Titel des Abschnitts</label>
                <input class="form-control" v-model="editedElement.title" v-focus/>
            </div>
            <div class="form-group">
                <button class="btn btn-primary" @click="save">Speichern</button>
                <inertia-link class="btn btn-secondary" :href="route('services.liturgy.editor', element.service_id)">Abbrechen
                </inertia-link>
            </div>
        </div>
        <hr v-if="element.items.length > 0"/>
    </form>
</template>

<script>
export default {
    name: "BlockEditor",
    props: ['element'],
    data() {
        return {
            editedElement: this.element,
        };
    },
    methods: {
        save: function () {
            this.$inertia.patch('/liturgy/' + this.element.service_id + '/block/' + this.element.id, this.editedElement, {
                preserveState: false,
            });
        },
        cancel: function () {
            this.$emit('cancel');
        }
    }
}
</script>

<style scoped>
.liturgy-item-block-editor {
    padding: 5px;
}

</style>
