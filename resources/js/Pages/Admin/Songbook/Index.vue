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
    <admin-layout title="Liederbücher">
        <dataset v-slot="{ ds }"
                 :ds-data="songbooks"
                 ds-sort-by="name"
                 :ds-search-in="['name', 'code']">
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
                                <th></th>
                                <th>Kürzel</th>
                                <th>Titel</th>
                                <th></th>
                            </tr>
                            </thead>
                            <dataset-item tag="tbody">
                                <template #default="{ row, rowIndex }">
                                    <tr>
                                        <td>
                                            <img v-if="row.image"
                                                 :src="route('image', {path: row.image.replace('attachments/', '')})"
                                                 class="img-fluid" style="max-width: 3em;" />
                                        </td>
                                        <td>{{ row.code }}</td>
                                        <td><div class="text-bold">{{ row.name }}</div>
                                            <div class="text-small">{{ row.description }}</div>
                                        </td>
                                        <td class="text-right">
                                            <nav-button type="primary" icon="mdi mdi-pencil" title="Liederbuch bearbeiten"
                                                        force-icon force-no-text :href="route('songbook.edit', row.id)" />
                                            <nav-button type="danger" icon="mdi mdi-delete" title="Liederbuch löschen"
                                                        force-icon force-no-text @click="deleteSongbook(row)" />
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
    props: ['songbooks'],
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
        deleteSongbook(songbook) {
            if (!confirm('Willst du wirklich das komplette Liederbuch löschen?')) return;
            this.$inertia.delete(route('songbook.destroy', songbook.id));
        }
    }
}
</script>

<style scoped>

</style>
