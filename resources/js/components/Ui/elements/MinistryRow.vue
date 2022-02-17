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
    <div class="row">
        <div class="col-md-6">
            <selectize class="form-group" :name="'ministries['+index+']'+'[description]'" v-model="myDescription" :settings="settings" />
        </div>
        <div class="col-md-5">
            <people-select :name="'ministries['+index+']'+'[people][]'" v-model="myMembers" :teams="teams"
                           :people="people" @input="changed" :include-teams-from-city="includeTeamsFromCity"
                           @count="$emit('count')" />
        </div>
        <div class="col-md-1 text-right">
            <button class="btn btn-danger btn-sm" @click.prevent="deleteRow()" title="Reihe entfernen"><span class="mdi mdi-delete"></span></button>
        </div>
    </div>
</template>

<script>
import Selectize from "vue2-selectize";
import PeopleSelect from "./PeopleSelect";
export default {
    name: "MinistryRow",
    components: {PeopleSelect, Selectize},
    props: {
        title: String,
        members: Array,
        people: Array,
        index: Number,
        ministries: Array,
        value: Object,
        includeTeamsFromCity: Object,
        teams: Array,
    },
    data() {
        var myMinistries = [];
        const defaults = ['P', 'O', 'M', 'A'];
        this.ministries.forEach(function (ministry) {
            if (!defaults.includes(ministry.category)) {
                myMinistries.push(ministry);
            }
        });
        return {
            myDescription: this.title != 'test' ? this.title : '',
            myMinistries: myMinistries,
            myMembers: this.members,
            store: this.value,
            settings: {
                searchField: ['category'],
                labelField: 'category',
                valueField: 'category',
                options: myMinistries,
                create: function(input) {
                    return {category: input};
                },
                render: {
                    option_create: function (data, escape) {
                        return '<div class="create">Neuen Dienst anlegen: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                    }
                },
            }
        }
    },
    watch: {
        myDescription: {
            /**
             * Called when ministry description changes
             * @param newVal new ministry description
             * @param oldVal old ministry description
             */
            handler: function (newVal, oldVal) {
                this.store[newVal] = this.store[oldVal];
                delete this.store[oldVal];
                this.$emit('input', this.store);
            }
        }
    },
    methods: {
        /**
         * Called when ministry assignment changes
         * @param newVal Array with all assigned people ids
         */
        changed(newVal) {
            this.store[this.myDescription] = newVal;
            this.$emit('input', this.store);
        },
        deleteRow(index) {
            delete this.store[this.myDescription];
            this.$emit('delete', this.store);
        }
    }
}
</script>

<style scoped>

</style>
