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
    <form-group :id="myId" :label="label" :help="help" :name="name" :pre-label="preLabel" :required="required"
                :value="value" :is-checked-item="isCheckedItem">
        <input class="form-control" :class="{'is-invalid': $page.props.errors[name], 'checked-input': isCheckedItem}" :type="type" v-model="myValue" :id="myId+'Input'"
               :placeholder="placeholder" :aria-placeholder="placeholder" :autofocus="autofocus"
               @input="$emit('input', $event.target.value);" :disabled="disabled"
               :required="required" :aria-required="required"/>
    </form-group>
</template>

<script>
import FormGroup from "./FormGroup";
import ValueCheck from "../elements/ValueCheck";

export default {
    name: "FormInput",
    components: {ValueCheck, FormGroup},
    props: {
        label: String,
        id: String,
        type: {
            type: String,
            default: 'text',
        },
        required: {
            type: Boolean,
            default: false,
        },
        name: String,
        value: {
            type: null,
        },
        help: String,
        placeholder: String,
        preLabel: String,
        autofocus: Boolean,
        disabled: {
            type: Boolean,
            default: false,
        },
        handleInput: Object,
        isCheckedItem: {
            type: null,
            default: false,
        },
    },
    mounted() {
        if (this.myId == '') this.myId = this._uid;
    },
    data() {
        return {
            errors: this.$page.props.errors,
            error: this.$page.props.errors[this.name] || false,
            myId: this.id || '',
            myValue: this.value,
        }
    },
    watch: {
        value: {
            handler: function (newVal) {
                if (this.required) {
                    this.error = this.$page.props.errors[this.name] = newVal ? '' : 'Dieses Feld darf nicht leer bleiben.';
                    this.$forceUpdate();
                }
            }
        }
    },
}
</script>

<style scoped>
</style>
