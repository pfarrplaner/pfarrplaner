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
        <card>
            <card-body>
                <fake-table :columns="[10,2]" :headers="['Kirchengemeinde', '']" collapsed-header="Kirchengemeinde" striped="1">
                    <div class="row pt-1 pb-1" v-for="(city,cityIndex) in cities" :class="{ even: cityIndex % 2 != 0}">
                        <div class="col-md-10">{{ city.name }}</div>
                        <div class="col-md-2 text-right">
                                <inertia-link v-if="city.canEdit" class="btn btn-primary" title="Kirchengemeinde bearbeiten" :href="route('city.edit', {city: city.name})">
                                    <span class="fa fa-edit"></span>
                                </inertia-link>
                                <button v-if="city.canDelete" class="btn btn-danger" title="Kirchengemeinde löschen" @click="deleteCity(city)">
                                    <span class="fa fa-trash"></span>
                                </button>
                        </div>
                    </div>
                </fake-table>
            </card-body>
        </card>
    </admin-layout>
</template>

<script>
import Card from "../../../components/Ui/cards/card";
import CardBody from "../../../components/Ui/cards/cardBody";
import FakeTable from "../../../components/Ui/FakeTable";
export default {
    name: "CityIndex",
    components: {FakeTable, CardBody, Card},
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
