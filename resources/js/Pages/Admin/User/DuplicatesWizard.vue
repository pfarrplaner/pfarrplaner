<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2022 Christoph Fischer, https://christoph-fischer.org
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
    <admin-layout title="Doppelte Personeneinträge finden">
        <template slot="navbar-left">
            <save-button @click="fixDuplicates" />
        </template>
        <draggable :list="people" group="users" handle=".handle" @end="dragged">
            <div v-for="user in people" class="row user-item my-3 p-2 border rounded rounded-lg">
                <div class="col-md-3">
                    <span class="fa handle" :class="user.isOfficialUser ? 'fa-user-check' : 'fa-user'"></span>
                    <span class="text-bold">{{ user.fullNameText || user.name }}</span>
                    <span class="badge badge-info">{{ user.duplicates.length }}</span><br />
                    <small>
                        <div v-if="user.email">{{ user.email }}</div>
                        <div v-if="user.home_cities">
                            <span v-for="city in user.home_cities" class="badge badge-dark mr-1 mb-1">{{ city.name }}</span>
                        </div>
                    </small>
                </div>
                <div class="col-md-7">
                    <draggable :list="user.duplicates" group="users" handle=".handle" @end="dragged" class="duplicates-drop"
                        :class="user.duplicates.length ? 'has-duplicates' : 'no-duplicates'" :emptyInsertThreshold="100">
                        <div v-for="duplicateUser in user.duplicates" class="user-item my-3 p-2 border rounded rounded-lg">
                            <span class="fa handle" :class="duplicateUser.isOfficialUser ? 'fa-user-check' : 'fa-user'"></span>
                            <span class="text-bold">{{ duplicateUser.fullNameText || duplicateUser.name }}</span>
                            <div v-if="user.email">{{ user.email }}</div>
                            <div v-if="duplicateUser.home_cities">
                                <span v-for="city in user.home_cities" class="badge badge-dark mr-1 mb-1">{{ city.name }}</span>
                            </div>
                        </div>
                    </draggable>
                </div>
                <div class="col-md-2"></div>
            </div>
        </draggable>
        <hr />
        <form-selectize label="Zur Liste hinzufügen" :options="withoutDuplicates" @input="addUserToList" />
    </admin-layout>
</template>

<script>

import draggable from 'vuedraggable'
import FormSelectize from "../../../components/Ui/forms/FormSelectize";
import SaveButton from "../../../components/Ui/buttons/SaveButton";

export default {
    name: "DuplicatesWizard",
    props: { possibleDuplicates: Array, withoutDuplicates: Array },
    components: {SaveButton, FormSelectize, draggable },
    data() {
        var people = this.possibleDuplicates;
        for (const personIndex in people) {
            people[personIndex].duplicates = Object.values(people[personIndex].duplicates);
        }

        return {
            people: people,
        }
    },
    methods: {
        dragged(event) {
            console.log('dragged', event);
            for (const personIndex in this.people) {
                for (const dupIndex in this.people[personIndex].duplicates) {
                    if (this.people[personIndex].duplicates[dupIndex].duplicates.length > 0) {
                        this.people[personIndex].duplicates[dupIndex].duplicates.forEach(duplicate => {
                            this.people[personIndex].duplicates.push(duplicate);
                        });
                        this.people[personIndex].duplicates[dupIndex].duplicates = [];
                    }
                }
            }
        },
        addUserToList(id) {
            this.withoutDuplicates.forEach(user => {
                if (id == user.id) this.possibleDuplicates.push(user);
            });
        },
        fixDuplicates() {
            let record = {};
            this.people.forEach(person => {
               if (person.duplicates.length > 0) {
                   record[person.id] = [];
                   person.duplicates.forEach(duplicate => {
                        record[person.id].push(duplicate.id);
                   });
               }
            });

            this.$inertia.post(route('users.duplicates.fix'), record, { preserveState: false});
        }
    }
}
</script>

<style scoped>
    .user-item {
        background-color: white;
    }

    .fa-user-check {
        color: blue;
    }

    .handle {
        cursor: move;
    }

    .duplicates-drop {
        min-height: 3em;
    }

    .duplicates-drop.no-duplicates {
        background-color: lightyellow;
    }

    .sortable-chosen {
        background-color: yellow;
    }

</style>
