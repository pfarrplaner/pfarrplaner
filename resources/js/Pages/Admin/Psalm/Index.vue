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
    <admin-layout title="Psalmen">
        <template v-slot:navbar-left>
            <nav-button type="success" icon="mdi mdi-plus" title="Psalm hinzufügen"
                        :href="route('psalm.create')">Neuer Psalm</nav-button>
        </template>
            <dataset
                 v-slot="{ ds }"
                 :ds-data="myPsalms"
                 ds-sort-by="title"
                 :ds-search-in="['title']"
                 :ds-search-as="{
                     title: searchFullTitle,
                 }">
            <div class="row mb-3" :data-page-count="ds.dsPagecount">
                <div class="col-md-6 mb-2 mb-md-0">
                    <dataset-search ds-search-placeholder="Suchen..." ref="search" autofocus />
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
                                <th>Referenz</th>
                                <th>Titel</th>
                                <th></th>
                            </tr>
                            </thead>
                            <dataset-item tag="tbody">
                                <template #default="{ row, rowIndex }">
                                    <tr>
                                        <td >{{ String((row.songbook_abbreviation || row.songbook || '')+' '+(row.reference || '')).trim() }}</td>
                                        <td>{{ row.title }}</td>
                                        <td class="text-right">
                                            <nav-button class="btn-sm"
                                                        type="primary" icon="mdi mdi-pencil" title="Psalm bearbeiten"
                                                        force-icon force-no-text :href="route('psalm.edit', row.id)" />
                                            <nav-button class="btn-sm"
                                                        type="danger" icon="mdi mdi-delete" title="Psalm löschen"
                                                        force-icon force-no-text @click="deletePsalm(row)" />
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

import {Dataset, DatasetItem, DatasetSearch,} from 'vue-dataset';
import DatasetInfo from "../../../components/Ui/dataset/DatasetInfo";
import DatasetShow from "../../../components/Ui/dataset/DatasetShow";
import DatasetPager from "../../../components/Ui/dataset/DatasetPager";
import NavButton from "../../../components/Ui/buttons/NavButton";


export default {
    name: "Index",
    props: ['psalms'],
    data() {
        return {
            myPsalms: this.psalms,
        }
    },
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
        deletePsalm(psalm) {
            if (!confirm('Willst du wirklich den kompletten Psalm löschen?')) return;
            this.$inertia.delete(route('psalm.destroy', psalm.id));
        },
        searchFullTitle(value, searchString, rowData) {
            return String((rowData.songbook_abbreviation || rowData.songbook || '')+' '+(rowData.reference || '')+' '+(rowData.title || ''))
                .trim().toLowerCase().includes(searchString.toLowerCase());
        },
    }
}
</script>

<style scoped>

</style>
