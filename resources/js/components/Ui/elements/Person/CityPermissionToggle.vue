<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2022 Christoph Fischer, https://christoph-fischer.org
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
    <div class="btn-group btn-group-toggle" data-toggle="buttons">
        <label class="btn btn-light btn-sm no-rights" :class="{active: rights == 'n'}">
            <input type="radio" v-model="rights" value="n" @input="changed" title="Keine Rechte"> <span class="mdi mdi-eye-off"></span> Keine
        </label>
        <label class="btn btn-light btn-sm rights-read" :class="{active: rights == 'r'}">
            <input type="radio" v-model="rights" value="r" @input="changed"  title="Leserecht"> <span class="mdi mdi-eye"></span> Lesen
        </label>
        <label class="btn btn-light btn-sm rights-write" :class="{active: rights == 'w'}">
            <input type="radio" v-model="rights" value="w" @input="changed"  title="Schreibrecht"> <span class="mdi mdi-pencil"></span> Schreiben
        </label>
        <label class="btn btn-light btn-sm rights-admin" :class="{active: rights == 'a'}">
            <input type="radio" v-model="rights" value="a" @input="changed"  title="Administrator"> <span class="mdi mdi-account-cowboy-hat"></span> Administrator
        </label>
    </div>
</template>

<script>
export default {
    name: "CityPermissionToggle",
    props: ['value'],
    data() {
        return {
            rights: this.value,
        }
    },
    methods: {
        changed(e) {
            console.log('toggle', e.target.value);
            this.$emit('input', e.target.value);
        }
    }
}
</script>

<style scoped>
    .btn-light.no-rights:hover {
        background-color: lightcoral;
    }
    .btn-light.no-rights.active {
        background-color: #dc3545;
        color: white;
    }
    .btn-light.rights-read:hover {
        background-color: lightyellow;
    }
    .btn-light.rights-read.active {
        background-color: #ffc107;
    }
    .btn-light.rights-write:hover {
        background-color: lightgreen;
    }
    .btn-light.rights-write.active {
        background-color: #28a745;
    }
    .btn-light.rights-admin:hover {
        background-color: plum;
    }
    .btn-light.rights-admin.active {
        background-color: purple;
        color: white;
    }
</style>
