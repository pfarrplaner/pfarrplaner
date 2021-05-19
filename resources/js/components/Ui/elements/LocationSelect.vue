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
    <form-selectize :id="myId" :name="name" :label="label" :help="help"
                    :value="myValue"
                    :options="myOptions" id-key="id" title-key="name" :multiple="multiple"
                    :settings="settings" @input="locationChanged" />
</template>

<script>
import FormGroup from "../forms/FormGroup";
import Selectize from "vue2-selectize";
import FormSelectize from "../forms/FormSelectize";

export default {
    name: "LocationSelect",
    components: {FormSelectize, FormGroup, Selectize},
    props: {
        label: String,
        id: String,
        type: {
            type: String,
            default: 'text',
        },
        name: String,
        value: {
            type: null,
        },
        help: String,
        placeholder: String,
        locations: Array,
        error: String,
        useInput: Boolean,
        returnObject: Boolean,
        multiple: Boolean,
    },
    mounted() {
        if (this.myId == '') this.myId = this._uid;
    },
    data() {
        var myValue;
        var myOptions = [];
        var myCities = {};
        var myCityOptions = [];

        this.locations.forEach(item => {
            item['cityName'] = item.city.name;
            if (undefined == myCities[item.city.name]) myCities[item.city.name] = item.city.name;
            myOptions.push(item);
        });

        Object.keys(myCities).forEach(cityItem => {
            myCityOptions.push({groupName: cityItem});
        });
        myCityOptions.push('Freie Ortsangabe');

        if (this.value) {
            if (typeof this.value == 'object') {
                myValue = this.value.id
            } else {
                myValue = this.value;
                if (isNaN(myValue)) {
                    myOptions.push({id: myValue, name: myValue, cityName: 'Freie Ortsangabe'});
                }
            }
        }
        return {
            myId: this.id || '',
            myValue: myValue,
            myOptions: myOptions,
            settings: {
                labelField: 'name',
                searchField: ['name', 'cityName'],
                optgroupField: 'cityName',
                optgroupLabelField: 'groupName',
                optgroupValueField: 'groupName',
                optgroups: myCityOptions,
                create: function(input, callback){
                    return callback({id: input, name: input, cityName: 'Freie Ortsangabe'});
                },
                render: {
                    option_create: function (data, escape) {
                        return '<div class="create">Freie Ortsangabe: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                    },
                    optgroup_header: function (data, escape) {
                        return '<div class="optgroup-header">' + escape(data.groupName) +'</div>';
                    }
                },
            },
        }
    },
    methods: {
        locationChanged(newVal) {
            if (this.returnObject) {
                if (!isNaN(newVal)) {
                    var found = false;
                    this.locations.forEach((thisLocation) => {
                        if (thisLocation.id == newVal) found = thisLocation;
                    })
                    if (found) newVal = found;
                }
            }
            this.sendEvent(newVal);
        },
        sendEvent(found) {
            if (this.useInput) {
                this.$emit('input', found)
            } else {
                this.$emit('set-location', found);
            }
        }
    }
}
</script>

<style scoped>

</style>
