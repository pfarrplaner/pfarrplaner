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
    <form-group :id="myId" :label="label" :help="help" :name="name" :pre-label="preLabel" :is-checked-item="isCheckedItem">
        <selectize class="form-control" :class="{'is-invalid': $page.props.errors[name]}" v-model="myValue" :id="myId+'Input'"
               :placeholder="placeholder" :aria-placeholder="placeholder" :disabled="disabled" :name="name"
               @input="changed" :settings="mySettings" :multiple="multiple" v-if="options.length == 0">
            <option v-for="item in items" :value="item[idKey]">{{ titleKey ? item[titleKey] : item }}</option>
        </selectize>
        <selectize class="form-control" :class="{'is-invalid': $page.props.errors[name]}" v-model="myValue" :id="myId+'Input'"
               :placeholder="placeholder" :aria-placeholder="placeholder" :disabled="disabled"
               @input="changed" :settings="mySettings" :multiple="multiple" v-else>
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
        id: {
            type: null,
        },
        type: {
            type: String,
            default: 'text',
        },
        name: String,
        value: {
            type: null,
        },
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
        multiple: {
            type: Boolean,
            default: false,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        options: {
            type: Array,
            default() { return []; }
        },
        itemRenderer: {
            type: null,
        },
        optionRenderer: {
            type: null,
        },
        isCheckedItem: {
            type: Boolean,
        }
    },
    mounted() {
        if (this.myId == '') this.myId = this._uid;
    },
    data() {
        var mySettings = (this.settings == undefined ? {} : this.settings);
        if (this.options.length > 0) {
            mySettings['options'] = this.options;
            if (this.idKey) mySettings['valueField'] = this.idKey;
            if (this.titleKey) mySettings['labelField'] = this.titleKey;
            if (undefined != this.itemRenderer) {
                if (undefined == mySettings['render']) mySettings['render'] = {};
                mySettings['render']['item'] = this.itemRenderer;
            }
            if (undefined != this.optionRenderer) {
                if (undefined == mySettings['render']) mySettings['render'] = {};
                mySettings['render']['option'] = this.optionRenderer;
            }
        }
        if (!mySettings['searchField']) mySettings['searchField'] = [mySettings['labelField'] || 'name'];

        return {
            myId: this.id || '',
            myValue: this.value,
            mySettings: mySettings,
        }
    },
    methods: {
        changed(newVal) {
            var allFound = [];
            if (this.items) {
                this.items.forEach(item => {
                    if (newVal.includes(item[this.idKey].toString())) allFound.push(item);
                });
                if (this.multiple) {
                    this.$emit('input', allFound);
                } else {
                    this.$emit('input', allFound[0]);
                }
            } else {
                if (this.multiple) {
                    this.$emit('input', newVal);
                } else {
                    this.$emit('input', typeof newVal == 'Array' ? newVal[0] : newVal);
                }
            }
        }
    }
}
</script>

<style scoped>

</style>
