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
    <form-group :id="id" :name="name" :label="label" :help="help">
        <select :id="id+'Input'" :name="name" class="form-control"
                :class="{'is-invalid' :error}" v-model="myValue"
                @input="changed">
            <option v-for="day in days" :value="day.id">{{ moment(day.date).format('DD.MM.YYYY') }}</option>
        </select>
    </form-group>
</template>

<script>
import FormGroup from "../forms/FormGroup";

export default {
    name: "DaySelect",
    components: {FormGroup},
    props: {
        label: String,
        id: String,
        type: {
            type: String,
            default: 'text',
        },
        name: String,
        value: Object,
        help: String,
        placeholder: String,
        error: String,
        city: Object,
        days: Array,
    },
    mounted() {
        if (this.myId == '') this.myId = this._uid;
    },
    data() {
        return {
            myId: this.id || '',
            myValue: this.value.id,
        }
    },
    methods: {
        changed(event) {
            var found = false;
            const target = event.target.value;
            this.days.forEach(function (day) {
                if (target == day.id) found = day;
            });
            if (found) {
                this.myValue = found.id;
                this.$emit('input', found);
            }
        }
    },

}
</script>

<style scoped>

</style>
