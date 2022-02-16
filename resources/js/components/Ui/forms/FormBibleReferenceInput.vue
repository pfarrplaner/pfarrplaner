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
               @input="bibleText(component); $emit('input', $event.target.value);" :disabled="disabled"
               :required="required" :aria-required="required"/>
        <small class="form-text text-muted":title="myBibleText">
            <span v-if="myBibleTextLoading" class="fa fa-spin fa-spinner" title="Bibeltext wird geladen..."></span>
            <span v-else>{{ myBibleText }}</span>
            <span v-if="(!myBibleTextLoading) && (myBibleText)" class="fa fa-copy" @click.prevent.stop="copyToClipboard"
                                                       title="Klicken, um den Text in die Zwischenablage zu kopieren"></span>
        </small>
    </form-group>
</template>

<script>
import FormGroup from "./FormGroup";
import ValueCheck from "../elements/ValueCheck";
import BibleReference from "../../LiturgyEditor/Elements/BibleReference";
import __ from 'lodash';

export default {
    name: "FormBibleReferenceInput",
    components: {BibleReference, ValueCheck, FormGroup},
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
            myBibleText: '',
            myBibleTextLoading: false,
            component: this,
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
    methods: {
        bibleText: __.debounce((component) => {
            if (!component.myValue.includes(' ')) return;
            if (!component.myValue.includes(',')) return;
            component.myBibleText = '';
            component.myBibleTextLoading = true;
            axios.get(route('bible.text', {reference: component.myValue}))
                .then(result => {
                    component.myBibleText = result.data.text;
                    component.myBibleTextLoading = false;
                });
        }, 1000),
        copyToClipboard() {
            const cb = navigator.clipboard;
            cb.writeText(this.myBibleText+"\n("+this.myValue+')').then(result => {});
        }
    },
}
</script>

<style scoped>
</style>
