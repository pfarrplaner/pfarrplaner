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
    <admin-layout title="Kirchen und Gottesdienstorte">
        <template slot="navbar-left">
            <a class="btn btn-success" :href="route('location.create')">
                <span class="d-inline d-md-none mdi mdi-plus"></span>
                <span class="d-none d-md-inline">Neuen Ort hinzufügen</span>
            </a>
        </template>
        <dataset v-slot="{ ds }"
                 :ds-data="locations"
                 ds-sort-by="name"
                 :ds-search-in="['name', 'city']"
                 :ds-search-as="{
                    city: searchAsCity,
                 }">
            <dataset-search ds-search-placeholder="Suchen..." ref="search" autofocus/>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover d-md-table">
                            <thead>
                            <tr>
                                <th>Kirche / Gottesdienstort</th>
                                <th>Kirchengemeinde</th>
                                <th>Gottesdienst um</th>
                                <th></th>
                            </tr>
                            </thead>
                            <dataset-item tag="tbody">
                                <template #default="{ row, rowIndex }">
                                    <tr>
                                        <td>{{ row.name }}</td>
                                        <td>{{ row.city.name }}</td>
                                        <td>{{ row.default_time ? row.default_time.substr(0,5)+' Uhr' : ''}}</td>
                                        <td class="text-right">
                                            <nav-button type="light" icon="mdi mdi-pencil" title="Ort bearbeiten"
                                                        @click="editLocation(row)" force-icon force-no-text/>
                                            <nav-button type="danger" icon="mdi mdi-delete"
                                                        title="Ort löschen"
                                                        @click="deleteLocation(row)" force-icon force-no-text/>
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
import {Dataset, DatasetItem, DatasetSearch} from "vue-dataset";
import DatasetInfo from "../../../components/Ui/dataset/DatasetInfo";
import DatasetPager from "../../../components/Ui/dataset/DatasetPager";
import DatasetShow from "../../../components/Ui/dataset/DatasetShow";
import NavButton from "../../../components/Ui/buttons/NavButton";

export default {
    name: "Index",
    props: ['locations'],
    components: {
        NavButton,
        Dataset,
        DatasetItem,
        DatasetInfo,
        DatasetPager,
        DatasetSearch,
        DatasetShow
    },
    methods: {
        searchAsCity(value, searchString, rowData) {
            return rowData.city.name.toLowerCase().includes(searchString);
        },
        editLocation(location) {
            this.$inertia.get(route('location.edit', location.id));
        },
        deleteLocation(location) {
            if (confirm('Willst du diesen Ort wirklich löschen?'))
                this.$inertia.delete(route('location.destroy', location.id));
        },
    }
}
</script>

<style scoped>

</style>
