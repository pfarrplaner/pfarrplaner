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
    <form-group :id="myId" :label="label" :help="help" :name="name" :pre-label="preLabel">
        <selectize class="form-control" :class="{'is-invalid': error}" v-model="myValue" :id="myId+'Input'"
               :placeholder="placeholder" :aria-placeholder="placeholder" :disabled="disabled"
               @input="changed" :settings="settings" multiple>
            <option v-for="item in items" :value="item[idKey]">{{ item[titleKey] }}</option>
        </selectize>
    </form-group>
</template>

<script>
import FormGroup from "./FormGroup";
import Selectize from 'vue2-selectize';
export default {
    name: "FormSelectize",
    components: {FormGroup, Selectize},
    props: {
        label: String,
        id: String,
        type: {
            type: String,
            default: 'text',
        },
        name: String,
        value: Object,
        items: Array,
        idKey: {
            type: String,
            default: 'id',
        },
        titleKey: {
            type: String,
            default: 'name',
        },
        help: String,
        placeholder: String,
        error: String,
        settings: {},
        preLabel: String,
        disabled: {
            type: Boolean,
            default: false,
        },
    },
    mounted() {
        if (this.myId == '') this.myId = this._uid;
    },
    data() {
        return {
            myId: this.id || '',
            myValue: this.value,
        }
    },
    methods: {
        changed(newVal) {
            var allFound = [];
            this.items.forEach(function(item){
                if (newVal.includes(item[idKey].toString())) allFound.push(item);
            });
            this.$emit('input', allFound);
        }
    }
}
</script>

<style scoped>

</style>
