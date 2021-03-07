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
    <form-group :id="myId" :name="name" :label="label" :help="help">
        <selectize :id="id+'Input'" class="form-control" :class="{'is-invalid': error}" :settings="settings"
                   :name="name" :value="myLocation" @input="locationChanged($event)" @input.native="$event.stopPropagation()">
            <option v-if="(typeof myValue != 'object')" :value="myValue">{{ myValue }}</option>
            <option v-for="location in locations" :value="location.id">{{ location.name }}</option>
        </selectize>
    </form-group>
</template>

<script>
import FormGroup from "../forms/FormGroup";
import Selectize from "vue2-selectize";

export default {
    name: "LocationSelect",
    components: {FormGroup, Selectize},
    props: {
        label: String,
        id: String,
        type: {
            type: String,
            default: 'text',
        },
        name: String,
        value: {},
        help: String,
        placeholder: String,
        locations: Array,
        error: String,
    },
    mounted() {
        if (this.myId == '') this.myId = this._uid;
    },
    data() {
        return {
            myId: this.id || '',
            myValue: this.value,
            myLocation: typeof this.value == 'object' ? this.value.id : this.value,
            isSpecialLocation: (typeof value != 'object'),
            settings: {
                create: true,
                render: {
                    option_create: function (data, escape) {
                        return '<div class="create">Freie Ortsangabe: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                    }
                },
            },
        }
    },
    methods: {
        locationChanged(newVal) {
            console.log(newVal);
            if (!isNaN(newVal)) {
                var found = false;
                this.locations.forEach(function (location){
                    if (location.id == newVal) found = location;
                });
                if (found) {
                    this.$emit('set-location', found);
                    this.myValue = found;
                }
                return;
            }
            this.$emit('set-location', newVal);
            this.myValue = newVal;
        },
    }
}
</script>

<style scoped>

</style>
