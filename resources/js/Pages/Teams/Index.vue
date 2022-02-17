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
    <admin-layout title="Teams">
        <template slot="navbar-left">
            <a class="btn btn-success" :href="route('teams.create')">
                <span class="d-inline d-md-none mdi mdi-account-multiple-plus"></span>
                <span class="d-none d-md-inline">Team hinzufügen</span>
            </a>
        </template>
        <card>
            <card-body>
                <div class="teams-index">
                    <fake-table v-if="teams.length > 0" :columns="[3,3,4,2]" :headers="['Team', 'Kirchengemeinde', 'Mitglieder', '']"
                                collapsed-header="Teams">
                        <div class="row mt-1" v-for="(team,teamIndex) in teams">
                            <div class="col-md-3">{{ team.name }}</div>
                            <div class="col-md-3">{{ team.city.name }}</div>
                            <div class="col-md-4">
                                <span v-for="user in team.users" class="badge badge-light">{{ user.name }}</span>
                            </div>
                            <div class="col-md-2 text-right">
                                <div v-if="team.writable">
                                    <a class="btn btn-light" :href="route('team.edit', team.id)" title="Team bearbeiten">
                                        <span class="mdi mdi-pencil"></span>
                                    </a>
                                    <button class="btn btn-danger" @click="deleteTeam(team)" title="Team löschen">
                                        <span class="mdi mdi-account-multiple-minus"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </fake-table>
                    <div v-else class="alert alert-info">
                        Zur Zeit sind noch keine Teams vorhanden.
                    </div>
                </div>
            </card-body>
        </card>
    </admin-layout>
</template>

<script>
import FakeTable from "../../components/Ui/FakeTable";
import CardBody from "../../components/Ui/cards/cardBody";
import Card from "../../components/Ui/cards/card";

export default {
    components: {Card, CardBody, FakeTable},
    props: ['teams'],
    methods: {
        deleteTeam(team) {
            if (confirm('Willst du dieses Team wirklich löschen?'))
                this.$inertia.delete(route('team.destroy', team.id));
        }
    }
}
</script>

<style scoped>

</style>
