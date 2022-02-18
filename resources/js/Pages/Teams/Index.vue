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
        <dataset v-slot="{ ds }"
                 :ds-data="teams"
                 ds-sort-by="name"
                 :ds-search-in="['name', 'city', 'users']"
                 :ds-search-as="{
                    city: searchAsCity,
                    users: searchMembers,
                 }">
            <dataset-search ds-search-placeholder="Suchen..." ref="search" autofocus/>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover d-md-table">
                            <thead>
                            <tr>
                                <th>Team</th>
                                <th>Kirchengemeinde</th>
                                <th>Mitglieder</th>
                                <th></th>
                            </tr>
                            </thead>
                            <dataset-item tag="tbody">
                                <template #default="{ row, rowIndex }">
                                    <tr>
                                        <td>{{ row.name }}</td>
                                        <td>{{ row.city.name }}</td>
                                        <td><span v-for="user in row.users" class="badge badge-light">{{
                                                user.name
                                            }}</span></td>
                                        <td>
                                            <nav-button type="light" icon="mdi mdi-pencil" title="Team bearbeiten"
                                                        @click="editTeam(row)" force-icon force-no-text/>
                                            <nav-button type="danger" icon="mdi mdi-account-multiple-minus"
                                                        title="Team bearbeiten"
                                                        @click="deleteTeam(row)" force-icon force-no-text/>
                                        </td>
                                    </tr>
                                </template>
                            </dataset-item>
                        </table>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-md-row flex-column justify-content-between align-items-center border-top pt-2">
                <dataset-info class="mb-2 mb-md-0"/>
                <dataset-pager/>
            </div>
        </dataset>
    </admin-layout>
</template>

<script>
import FakeTable from "../../components/Ui/FakeTable";
import CardBody from "../../components/Ui/cards/cardBody";
import Card from "../../components/Ui/cards/card";
import {Dataset, DatasetItem, DatasetSearch} from "vue-dataset";
import DatasetInfo from "../../components/Ui/dataset/DatasetInfo";
import DatasetPager from "../../components/Ui/dataset/DatasetPager";
import DatasetShow from "../../components/Ui/dataset/DatasetShow";
import NavButton from "../../components/Ui/buttons/NavButton";

export default {
    components: {
        NavButton, Card, CardBody, FakeTable,
        Dataset,
        DatasetItem,
        DatasetInfo,
        DatasetPager,
        DatasetSearch,
        DatasetShow
    },
    props: ['teams'],
    methods: {
        editTeam(team) {
            this.$inertia.get(route('team.edit', team.id));
        },
        deleteTeam(team) {
            if (confirm('Willst du dieses Team wirklich löschen?'))
                this.$inertia.delete(route('team.destroy', team.id));
        },
        searchAsCity(value, searchString, rowData) {
            return rowData.city.name.toLowerCase().includes(searchString);
        },
        searchMembers(value, searchString, rowData) {
            let found = false;
            rowData.users.forEach(user => {
                if (user.name.toLowerCase().includes(searchString)) found = true;
            })
            return found;
        },
    }
}
</script>

<style scoped>

</style>
