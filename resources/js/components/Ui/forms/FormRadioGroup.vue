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
    <div class="form-group">
        <span v-if="preLabel"><span :class="['fa', 'fa-'+preLabel]"></span> </span><label>{{ label }}</label>
        <div>
            <div class="form-check form-check-inline" v-for="(subLabel,value,index) in items" :key="value">
                <input class="form-check-input" type="radio" :name="name" :id="'radio'+myId+index" :value="value"
                       v-model="myValue" :disabled="disabled"
                       @input="changed($event)">
                <label class="form-check-label" :for="'radio'+myId+index">{{ subLabel }}</label>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "FormRadioGroup",
    props: {
        id: String,
        label: String,
        name: String,
        value: Boolean,
        help: String,
        items: Object,
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
            myValue: this.value || '',
            error: this.$page.props.errors[this.name],
        }
    },
    methods: {
        changed(event) {
            if (event.target.checked) this.$emit('input', event.target.value);
        },
    }
}
</script>

<style scoped>

</style>
