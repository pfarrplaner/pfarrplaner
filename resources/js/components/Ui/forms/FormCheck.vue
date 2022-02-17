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
    <div class="form-check">
        <input type="hidden" :name="name" value="0" />
        <input type="checkbox" class="form-check-input" :class="{'is-invalid': $page.props.errors[name]}" :id="myId+'Input'" :name="name"
               :checked="isChecked" @input="handleInput" value="1" :disabled="disabled"/>
        <label class="form-check-label" v-if="label" :for="id+'Input'">{{ label }}</label>
        <span v-if="isCheckedItem"  :class="myValue ? 'mdi mdi-check-circle' : 'mdi mdi-close-circle'" :key="myValue"></span>
        <div v-if="$page.props.errors[name]" class="invalid-feedback">{{ $page.props.errors[name] }}</div>
        <div v-if="help" class="form-text text-muted">{{ help }}</div>
    </div>
</template>

<script>
import FormGroup from "./FormGroup";
export default {
    name: "FormCheck",
    components: {FormGroup},
    props: {
        label: String,
        id: String,
        name: String,
        value: {
            type: null,
        },
        help: String,
        preLabel: String,
        disabled: {
            type: Boolean,
            default: false,
        },
        isCheckedItem: Boolean,
    },
    computed: {
        isChecked() {
            return (this.myValue) && (this.myValue != 0) && (this.myValue != '0');
        }
    },
    mounted() {
        if (this.myId == '') this.myId = this._uid;
    },
    data() {
        return {
            myId: this.id || '',
            myValue: this.value,
            error: this.$page.props.errors[this.name],
        }
    },
    methods: {
        handleInput(event) {
            this.myValue = event.target.checked ? 1: 0;
            this.$emit('input', this.myValue);
        }
    }
}
</script>

<style scoped>
    .mdi-check-circle {
        color: green;
    }
    .mdi-close-circle {
        color: red;
    }
</style>
