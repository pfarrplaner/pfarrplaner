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
    <admin-layout title="Ausgabeformat wählen">
        <dataset v-slot="{ ds }"
                 :ds-data="reports"
                 ds-sort-by="name"
                 :ds-search-in="['title', 'description', 'group']">
            <div class="row mb-3" :data-page-count="ds.dsPagecount">
                <div class="col-md-6 mb-2 mb-md-0">
                    <dataset-search ds-search-placeholder="Suchen..." ref="search" autofocus/>
                </div>
                <div class="col-md-5 text-right">
                    <dataset-show class="float-right" :ds-show-entries="showEntries"/>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <dataset-item class="form-row mb-3">
                        <template #default="{ row, rowIndex }">
                            <div class="col-md-4">
                                <a @click.prevent.stop="createReport(row)" href="#" class="report-link">
                                    <card class="mb-2 report-card" title="Klicken, um dieses Ausgabeformat zu wählen"
                                          @click="createReport(row)">
                                        <card-header :style="{backgroundColor: row.backgroundColor, color: row.color}">
                                            {{ row.group }}
                                        </card-header>
                                        <card-body>
                                            <h3 class="card-title text-truncate mb-2" :title="`Index: ${rowIndex}`">
                                                <span :class="row.icon"></span> {{ row.title }}
                                            </h3>
                                            <p class="card-text text-truncate mb-0">{{ row.description }}</p>
                                        </card-body>
                                    </card>
                                </a>
                            </div>
                        </template>
                        <template #noDataFound>
                            <div class="col-md-12 pt-2">
                                <p class="text-center">No results found</p>
                            </div>
                        </template>
                    </dataset-item>
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
import NavButton from "../../components/Ui/buttons/NavButton";
import CardBody from "../../components/Ui/cards/cardBody";
import Card from "../../components/Ui/cards/card";
import {Dataset, DatasetItem, DatasetSearch} from "vue-dataset";
import DatasetInfo from "../../components/Ui/dataset/DatasetInfo";
import DatasetPager from "../../components/Ui/dataset/DatasetPager";
import DatasetShow from "../../components/Ui/dataset/DatasetShow";
import CardHeader from "../../components/Ui/cards/cardHeader";

export default {
    name: "Index",
    props: ['reports'],
    components: {
        CardHeader,
        NavButton, CardBody, Card,
        Dataset,
        DatasetItem,
        DatasetInfo,
        DatasetPager,
        DatasetSearch,
        DatasetShow
    },
    data() {
        return {
            showEntries: 25,
        }
    },
    methods: {
        createReport(report) {
            console.log('createReport', report);
            if (report.inertia) {
                this.$inertia.get(route('reports.setup', report.key));
            } else {
                window.location.href = route('reports.setup', report.key);
            }
        }
    }
}
</script>

<style scoped>
.report-card {
    cursor: pointer;
}

.report-card:hover {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.report-card .card-body h3 {
    font-weight: bold;
}

a.report-link, a.report-link:hover {
    text-decoration: none;
    color: black;
}
</style>
