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
    <admin-layout title="Kirchengemeinden">
        <dataset v-slot="{ ds }"
                 :ds-data="cities"
                 ds-sort-by="name"
                 :ds-search-in="['name']">
            <div class="row mb-3" :data-page-count="ds.dsPagecount">
                <div class="col-md-6 mb-2 mb-md-0">
                    <dataset-search ds-search-placeholder="Suchen..." ref="search" autofocus />
                </div>
                <div class="col-md-1 mb-2 mb-md-0">
                    <label v-for="checkbox in checkboxes" class="mt-1" :title="checkbox.title" v-if="checkbox.condition">
                        <input type="checkbox" v-model="checkbox.value" :title="checkbox.title" />
                        <span v-if="checkbox.icon" class="fa" :class="checkbox.icon"></span>
                        <span v-if="checkbox.label">{{ checkbox.label }}</span>
                    </label>
                </div>
                <div class="col-md-5 text-right">
                    <dataset-show class="float-right" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover d-md-table">
                            <thead>
                            <tr>
                                <th>Kirchengemeinde(n)</th>
                                <th></th>
                            </tr>
                            </thead>
                            <dataset-item tag="tbody">
                                <template #default="{ row, rowIndex }">
                                    <tr>
                                        <td>{{ row.name }}</td>
                                        <td class="text-right">
                                            <inertia-link v-if="row.canEdit" class="btn btn-sm btn-primary" title="Kirchengemeinde bearbeiten"
                                                          :href="route('city.edit', {city: row.name})">
                                                <span class="mdi mdi-pencil"></span>
                                            </inertia-link>
                                            <button v-if="row.canDelete" class="btn  btn-sm btn-danger ml-1" title="Kirchengemeinde löschen"
                                                    @click="deleteCity(row)">
                                                <span class="mdi mdi-delete"></span>
                                            </button>
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
import Card from "../../../components/Ui/cards/card";
import CardBody from "../../../components/Ui/cards/cardBody";
import FakeTable from "../../../components/Ui/FakeTable";
import {
    Dataset,
    DatasetItem,
    DatasetSearch,
} from 'vue-dataset';
import DatasetInfo from "../../../components/Ui/dataset/DatasetInfo";
import DatasetShow from "../../../components/Ui/dataset/DatasetShow";
import DatasetPager from "../../../components/Ui/dataset/DatasetPager";


export default {
    name: "CityIndex",
    components: {FakeTable, CardBody, Card,
        Dataset,
        DatasetItem,
        DatasetInfo,
        DatasetPager,
        DatasetSearch,
        DatasetShow
    },
    props: ['cities'],
    methods: {
        deleteCity(city) {
            if (confirm('Willst du die Kirchengemeinde wirklich komplett löschen?')) {
                this.$inertia.delete(route('city.destroy', {city: city.name}));
            }
        }
    }
}
</script>

<style scoped>
.row.even {
    background-color: #ececec;
}
</style>
