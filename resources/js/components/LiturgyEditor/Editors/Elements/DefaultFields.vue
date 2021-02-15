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
    <div class="default-fields">
        <div class="form-group">
            <label for="title">Titel im Ablaufplan</label>
            <input class="form-control" v-model="element.title" v-focus/>
        </div>
        <div v-if="agendaMode" class="form-group">
            <label for="agenda_instructions">Agendarische Hinweise</label>
            <textarea class="form-control" v-model="editedElement.data.agenda_instructions" v-focus/>
        </div>
        <div v-else v-if="editedElement.data.agenda_instructions" class="agenda-instructions">
            {{ editedElement.data.agenda_instructions }}
        </div>
   </div>
</template>

<script>
export default {
    name: "DefaultFields",
    props: {
        element: Object,
        service: Object,
        agendaMode: {
            type: Boolean,
            default: false,
        }
    },
    data() {
        var e = this.element;
        if (undefined == e.data) e.data = {};
        if (undefined == e.data.agenda_instructions) e.data.agenda_instructions = '';
        return {
            editedElement: e,
        }
    }
}
</script>

<style scoped>
    .agenda-instructions {
        italic: true;
    }
</style>
