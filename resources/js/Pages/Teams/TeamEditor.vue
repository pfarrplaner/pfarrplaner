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
    <admin-layout :title="team.name">
        <template slot="navbar-left">
            <button class="btn btn-primary" @click="saveTeam">
                <span class="d-inline d-md-none mdi mdi-content-save"></span>
                <span class="d-none d-md-inline">Speichern</span>
            </button>
            <button class="btn btn-danger ml-1" @click="deleteTeam">
                <span class="d-inline d-md-none mdi mdi-account-multiple-minus"></span>
                <span class="d-none d-md-inline">Löschen</span>
            </button>
        </template>
        <card>
            <card-body>
                <form-input name="name" v-model="myTeam.name" label="Bezeichnung" autofocus />
                <form-selectize name="city_id" v-model="myTeam.city_id" label="Kirchengemeinde"
                                :options="cities" />
                <people-select name="users" v-model="myTeam.users" :people="users" label="Mitglieder" />
            </card-body>
        </card>
    </admin-layout>
</template>

<script>
import Card from "../../components/Ui/cards/card";
import CardBody from "../../components/Ui/cards/cardBody";
import FormInput from "../../components/Ui/forms/FormInput";
import FormSelectize from "../../components/Ui/forms/FormSelectize";
import PeopleSelect from "../../components/Ui/elements/PeopleSelect";
export default {
    name: "TeamEditor",
    components: {PeopleSelect, FormSelectize, FormInput, CardBody, Card},
    props: ['team', 'cities', 'users'],
    data() {
        return {
            myTeam: this.team,
        }
    },
    methods: {
        saveTeam() {
            this.$inertia.patch(route('team.update', this.myTeam.id), this.myTeam);
        },
        deleteTeam() {
            if (confirm('Willst du dieses Team wirklich löschen?'))
                this.$inertia.delete(route('team.destroy', this.team.id));
        },
    }
}
</script>

<style scoped>

</style>
