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
    <form-group :id="myId" :name="name" :label="label" :help="help" pre-label="fa fa-user">
        <div ref="container">
            <div class="peopleselect-placeholder" v-if="!editing" @click="activate">
                <span v-for="person in getPeople(myValue)" class="badge badge-light people-badge">{{ person.name }}</span>
            </div>
            <div v-else>
                <selectize class="form-control" :class="{'is-invalid': error}" :name="name" :id="myId+'Input'"
                           v-model="myValue" multiple @input="changed" @blur="editing = false">
                    <option v-for="person in people" :value="person.id">{{ person.name }}</option>
                </selectize>
                <small class="form-text text-muted">Eine oder mehrere Personen (keine Anmerkungen, Notizen,
                    usw.)</small>
            </div>
        </div>
    </form-group>
</template>

<script>
import FormGroup from "../forms/FormGroup";
import Selectize from "vue2-selectize";

export default {
    name: "PeopleSelect",
    components: {FormGroup, Selectize},
    props: {
        label: String,
        id: String,
        type: {
            type: String,
            default: 'text',
        },
        name: String,
        value: Array,
        help: String,
        placeholder: String,
        error: String,
        people: Array,
    },
    mounted() {
        if (this.myId == '') this.myId = this._uid;
    },
    data() {
        var myValue = [];
        this.value.forEach(function (person) {
            myValue.push(person.id);
        });
        return {
            myId: this.id || '',
            myValue: myValue,
            myPeople: this.value,
            editing: false,
            clicked: false,
        }
    },
    updated() {
        this.$nextTick(function () {
            if (this.editing && this.clicked)  {
                console.log('hi');
                this.clicked = false;
                console.log(this.$refs.container.firstChild.firstChild.nextSibling);
                this.$refs.container.firstChild.firstChild.nextSibling.firstChild.click()
            }
        })
    },
    methods: {
        changed(newVal) {
            var allFound = [];
            this.people.forEach(function (person) {
                if (newVal.includes(person.id.toString())) allFound.push(person);
            });
            this.myPeople = allFound;
            this.$emit('input', allFound);
        },
        getPeople(value) {
            var people = [];
            this.people.forEach(function (person) {
                if (value.includes(person.id)) people.push(person);
            });
            return people;
        },
        activate() {
            this.editing = true;
            this.clicked = true;
        }
    }

}
</script>

<style scoped>
.peopleselect-placeholder {
    width: 100%;
    min-height: 2.2rem;
    border: 1px solid #ced4da;
    border-radius: .25rem;
    padding: .2rem .75rem;
}

.people-badge {
    background-color: #efefef;
    font-size: inherit;
    font-weight: normal;
}
</style>
