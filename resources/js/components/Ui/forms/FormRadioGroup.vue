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
    <div class="form-group">
        <div v-if="label || preLabel"><span v-if="preLabel"><span :class="preLabel"></span> </span><label>{{ label }}</label></div>
        <div>
            <div class="form-check" :class="{'form-check-inline': inline}" v-for="(subLabel,subValue,index) in items" :key="subValue">
                <input class="form-check-input"
                       :class="{'is-invalid': $page.props.errors[name]}"
                       type="radio" :name="name" :id="'radio'+myId+index" :value="subValue"
                       v-model="myValue" :disabled="disabled"
                       @input="changed($event)" />
                <label class="form-check-label" :for="'radio'+myId+index">{{ subLabel }}</label>
                <div v-if="$page.props.errors[name]" class="invalid-feedback">{{ $page.props.errors[name] }}</div>
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
        value: String,
        help: String,
        items: Object,
        preLabel: String,
        disabled: {
            type: Boolean,
            default: false,
        },
        inline: {
            type: Boolean,
            default: true,
        }
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
            var result = typeof event.target.value == Array ? event.target.value[0] : event.target.value;
            if (!isNaN(result)) result=parseInt(result);
            if (event.target.checked) this.$emit('input', result);
            this.$forceUpdate();
        },
    }
}
</script>

<style scoped>

</style>
