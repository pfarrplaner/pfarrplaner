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
    <div class="peopleselect">
        <form-group :id="myId" :name="name" :label="label" :help="help" pre-label="fa fa-user">
            <div ref="container">
                <selectize class="form-control" :class="{'is-invalid': error}" :name="name" :id="myId+'Input'"
                           :value="myValue" multiple @input="changed" @blur="editing = false" :settings="settings"
                           :options="people" />
                <small class="form-text text-muted">Eine oder mehrere Personen (keine Anmerkungen, Notizen,
                    usw.)</small>
            </div>
        </form-group>
        <modal title="Neue Person anlegen" v-if="showModal" @close="closeModal" @cancel="cancelModal"
               @shown="modalShown" close-button-label="Person speichern">
            <form-input name="name" label="Name" v-model="newPerson.name" ref="newPersonName" id="newPersonName"
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
        includeTeamsFromCity: Object,
        teams: {
            type: Array,
            default() { return [] },
        },
    },
    mounted() {
        if (this.myId == '') this.myId = this._uid;
    },
    data() {
        var myValue = this.value;
        var myPeople = this.people;
        var myPeopleReference = {};
        var myTeamReference = {};

        this.value.forEach(function (person) {
            myValue.push(person.id);
        });

        myPeople.forEach(person => {
            myPeopleReference[person.id] = person;
            person.type = person.type || 'user';
            person.category = person.category || 'Personen';
            person.userString = person.userString || '';
        });

        this.teams.forEach(team => {
            myTeamReference[team.id] = team;
            let tempTeam = {name: team.name, id: 'team:'+team.id, type: 'users', category: 'Teams', users: team.users, userString: ''};
            tempTeam.users.forEach(user => tempTeam.userString += user.name+' ');
            myPeople.push(tempTeam);
        });



        return {
            createCallback: null,
            myId: this.id || '',
            myValue: myValue,
            myPeople: myPeople,
            myPeopleReference: myPeopleReference,
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
            myTeamReference: myTeamReference,
            settings: {
                valueField: 'id',
                labelField: 'name',
                searchField: ['name', 'category', 'userString'],
                optgroupField: 'category',
                optgroupLabelField: 'groupName',
                optgroupValueField: 'groupName',
                optgroups: [{ groupName: 'Personen'}, {groupName: 'Teams'}],
                create: this.addPerson,
                options: myPeople,
                render: {
                    option_create: function (data, escape) {
                        return '<div class="create">Neue Person anlegen: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                    },
                    item: function (item, escape) {
                        if (item.type == 'user') {
                            return '<div><span class="fa fa-user"></span> '+escape(item.name)+'</div>';
                        } else {
                            let users = [];
                            item.users.forEach(user => {
                                users.push(user.name);
                            });
                            return '<div><span class="fa fa-users"></span> '+escape(item.name)+': '+escape(users.join(', '))+'</div>';
                        }
                    },
                    option: function (item, escape) {
                        var t= '<div><span class="ml-1 fa fa-'+item.type+'"></span> '+escape(item.name);

                        if (item.type == 'users') {
                            t += '<span class="ml-1 badge badge-dark">'+item.users.length+'</span>'
                            if (item.users.length > 0) t += '<div>';
                            item.users.forEach(user => {
                                t += '<span class="ml-1 badge badge-light">'+user.name+'</span>'
                            });
                            if (item.users.length > 0) t += '</div>';
                        }
                            t+='</div>';
                        return t;
                    }
                },
            },
        }
    },
    methods: {
        changed(newVal) {
            var externalValue = [];
            var newVal2 = [];

            newVal.forEach(item => {
               if (isNaN(item) && (item.substr(0,5) == 'team:')) {
                   console.log('referencing team #'+item.substr(5), this.myTeamReference[item.substr(5)]);
                   this.myTeamReference[item.substr(5)].users.forEach(user => {
                       newVal2.push(user.id);
                       console.log('added user #'+user.id, this.myValue);
                   });
               } else {
                   newVal2.push(item);
               }
            });

            newVal2.forEach(item => {
                externalValue.push(this.myPeopleReference[item]);
            })

            this.$emit('input', externalValue);
            this.myValue = newVal2;
            this.$forceUpdate();
        },
        closeModal() {
            var component = this;
            this.showModal = false;
            axios.post(route('users.add'), this.newPerson)
                .then(response => {
                    return response.data;
                })
                .then(data => {
                    data.type='user';
                    data.category='Personen';
                    data.userString = '';

                    this.myPeople.push(data);
                    this.myPeopleReference[data.id] = data;
                    this.createCallback(data);
                    this.changed(this.myValue);
                    component.personCreated = true;
                });
        },
        cancelModal() {
            this.showModal = false;
        },
        modalShown(ref) {
            var el = this.$refs['newPersonName'].$el.firstChild.nextSibling.nextSibling.nextSibling.nextSibling;
            el.focus();
            el.select();
        },
        addPerson(item, callback = null) {
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
            this.createCallback = callback;
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
