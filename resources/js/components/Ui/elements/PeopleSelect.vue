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
    <div class="peopleselect">
        <form-group :id="myId" :name="name" :label="label" :help="help" pre-label="fa fa-user">
            <div ref="container">
                <div class="peopleselect-placeholder" v-if="!editing" @click="activate">
                <span v-for="person in getPeople(myValue)" class="badge badge-light people-badge">{{
                        person.name
                    }}</span>
                </div>
                <div v-else>
                    <selectize class="form-control" :class="{'is-invalid': error}" :name="name" :id="myId+'Input'"
                               :value="myValue" multiple @input="changed" @blur="editing = false" :settings="settings">
                        <option v-for="person in allPeople" :value="person.id">{{ person.name }}</option>
                    </selectize>
                    <small class="form-text text-muted">Eine oder mehrere Personen (keine Anmerkungen, Notizen,
                        usw.)</small>
                </div>
            </div>
        </form-group>
        <modal title="Neue Person anlegen" v-if="showModal" @close="closeModal" @cancel="cancelModal"
               @shown="modalShown" close-button-label="Person speichern">
            <form-input name="name" label="Name" v-model="newPerson.name" ref="newPersonName"
                        :autofocus="true"/>
            <hr/>
            <form-input name="title" label="Titel" v-model="newPerson.title"
                        placeholder="z.B. Pfr."/>
            <form-input name="first_name" label="Vorname" v-model="newPerson.first_name"/>
            <form-input name="last_name" label="Nachname" v-model="newPerson.last_name"/>
            <form-input name="email" label="E-Mailadresse" v-model="newPerson.email" type="email" help="(falls bekannt)"/>
        </modal>
    </div>
</template>

<script>
import FormGroup from "../forms/FormGroup";
import Selectize from "vue2-selectize";
import FormInput from "../forms/FormInput";
import Modal from "../modals/Modal";

export default {
    name: "PeopleSelect",
    components: {Modal, FormInput, FormGroup, Selectize},
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
            allPeople: this.people,
            editing: false,
            clicked: false,
            personCreated: false,
            showModal: false,
            newPerson: {
                name: '',
                first_name: '',
                last_name: '',
                title: '',
            },
            settings: {
                create: true,
                render: {
                    option_create: function (data, escape) {
                        return '<div class="create">Neue Person anlegen: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                    }
                },
            }
        }
    },
    updated() {
        this.$nextTick(function () {
            if (this.editing && this.clicked) {
                this.clicked = false;
                this.$refs.container.firstChild.firstChild.nextSibling.firstChild.click()
            }
            if (this.personCreated) {
                console.log('hey');
                this.personCreated = false;
                var foundPeople = this.getPeople(this.myValue);
                console.log('foundPeople', foundPeople);
                this.$emit('input', foundPeople);
            }
        })
    },
    methods: {
        changed(newVal) {
            var newList = [];
            var allFound = [];
            var foundString = false;
            const container = this;
            newVal.forEach(function (item) {
                if (isNaN(item)) {
                    foundString = item; //container.addPerson(item);
                } else {
                    newList.push(item);
                }
            });
            this.people.forEach(function (person) {
                if (newList.includes(person.id.toString())) allFound.push(person);
            });
            this.myPeople = allFound;
            this.$emit('input', allFound);

            // new Person added?
            if (foundString) this.addPerson(foundString);
        },
        getPeople(value) {
            var foundPeople = [];
            this.people.forEach(function (person) {
                if (value.includes(person.id) || value.includes(person.id.toString())) foundPeople.push(person);
            });
            return foundPeople;
        },
        activate() {
            this.editing = true;
            this.clicked = true;
        },
        closeModal() {
            var component = this;
            this.showModal = false;
            axios.post(route('users.add'), this.newPerson)
                .then(response => {
                    return response.data;
                })
                .then(data => {
                    console.log(data);
                    var allFound = component.getPeople(component.myValue);
                    allFound.push(data);
                    console.log(allFound);
                    this.$emit('input', allFound);
                    component.myValue.push(data.id);
                    component.allPeople.push(data);
                    component.personCreated = true;
                });
        },
        cancelModal() {
            this.showModal = false;
        },
        modalShown(ref) {
            console.log(ref.firstChild.firstChild.nextSibling.nextSibling);
            ref.firstChild.firstChild.nextSibling.nextSibling.focus();
            ref.firstChild.firstChild.nextSibling.nextSibling.select();
        },
        addPerson(item) {
            var tmp, firstName, lastName;
            tmp = item.split(' ');
            firstName = tmp[0];
            lastName = tmp[1];
            this.newPerson = {
                name: item,
                first_name: firstName.trim(),
                last_name: lastName.trim(),
                title: '',
                email: '',
            };
            this.showModal = true;
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
    margin: 0 3px 3px 0;
}

</style>
