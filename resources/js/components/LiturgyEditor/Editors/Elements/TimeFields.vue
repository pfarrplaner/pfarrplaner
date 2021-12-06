<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
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
    <div class="mb-2">
        <hr/>
        <small @click="open = !open">
            <span class="text-bold fa" :class="open ? 'fa-chevron-circle-down' : 'fa-chevron-circle-right'"></span> Zusätzliche Zeitangaben</small>
        <div v-if="open" class="row">
            <div class="col-sm-6">
                <form-input label="Benötigte Zeit" v-model="editedElement.data.time" placeholder="MM:SS" />
            </div>
            <div class="col-sm-6">
                <form-input label="Abweichende Geschwindigkeit" help="Worte pro Minute" v-model="editedElement.data.wpm" />
            </div>
        </div>
    </div>
</template>

<script>
import FormInput from "../../../Ui/forms/FormInput";
export default {
    name: "TimeFields",
    components: {FormInput},
    props: ['service', 'element', 'agendaMode'],
    data() {
        var editedElement = this.element;
        editedElement.data.time = editedElement.data.time || '';
        editedElement.data.wpm = editedElement.data.wpm || '';

        return {
            editedElement: editedElement,
            open: (editedElement.data.time || editedElement.data.wpm) ? true: false,
        };
    }
}
</script>

<style scoped>

</style>
