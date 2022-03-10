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
    <admin-layout :title="myLocation ? myLocation.name+' bearbeiten' : 'Kirche / Gottesdienstort bearbeiten'">
        <template slot="navbar-left">
            <save-button @click="saveLocation"/>
            <nav-button title="Bereich hinzufügen"
                        class="ml-1" type="success" icon="mdi mdi-plus">Bereich hinzufügen</nav-button>
            <nav-button @click="addRow" title="Reihe hinzufügen"
                        class="ml-1" type="success" icon="mdi mdi-plus">Reihe hinzufügen</nav-button>
        </template>
        <template slot="tab-headers">
            <tab-headers>
                <tab-header id="home" :active-tab="activeTab" title="Allgemeines"/>
                <tab-header id="seating" :active-tab="activeTab" title="Sitzplätze"/>
            </tab-headers>
        </template>
        <tabs>
            <tab id="home" :active-tab="activeTab">
                <form-input name="name" label="Name" v-model="myLocation.name"/>
                <form-selectize name="city_id" label="Kirchengemeinde" v-model="myLocation.city_id" :options="cities"/>
                <form-input name="default_time" label="Gottesdienst um" v-model="myLocation.default_time"/>
                <location-select name="alternate_location_id"
                                 label="Wenn parallel Kindergottesdienst stattfindet, dann normalerweise hier"
                                 v-model="myLocation.alternate_location_id" :locations="alternateLocations"/>
                <form-input name="at_text" label="Ortsangabe, wenn ein Gottesdienst hier stattfindet"
                            v-model="myLocation.at_text"/>
                <form-input name="general_location_name" label="Allgemeine Ortsangabe"
                            v-model="myLocation.general_location_name" placeholder="z.B. in Tailfingen"/>
                <form-textarea name="instructions" label="'Wichtige Informationen für Besucher"
                               v-model="myLocation.instructions"/>
            </tab>
            <tab id="seating" :active-tab="activeTab">
                <dataset v-slot="{ ds }"
                         :ds-data="seatingRows"
                         ds-sort-by="title"
                         :ds-search-in="['title', 'sectionTitle']"
                         :ds-search-as="{}"
                >
                    <dataset-search ds-search-placeholder="Suchen..." ref="search" autofocus/>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover d-md-table">
                                    <thead>
                                    <tr>
                                        <th>Bereich</th>
                                        <th>Bezeichnung</th>
                                        <th>Sitzplätze</th>
                                        <th>Teilung möglich</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <dataset-item tag="tbody">
                                        <template #default="{ row, rowIndex }">
                                            <tr>
                                                <td>
                                                    <SeatingLabel :value="row.seating_section" />
                                                    <div class="seating-section-buttons">
                                                        <nav-button class="btn-sm"
                                                                    type="light" icon="mdi mdi-pencil" title="Bereich bearbeiten"
                                                                    @click="editSection(row.seating_section)" force-icon force-no-text/>
                                                        <nav-button class="btn-sm"
                                                                    type="danger" icon="mdi mdi-delete" title="Reihe löschen"
                                                                    @click="deleteSection(row.seating_section)" force-icon force-no-text/>
                                                    </div>
                                                </td>
                                                <td>
                                                    <SeatingLabel :value="row" />
                                                </td>
                                                <td>{{ row.seats }}</td>
                                                <td>{{ row.split }}</td>
                                                <td class="text-right">
                                                    <nav-button type="light" icon="mdi mdi-pencil" title="Reihe bearbeiten"
                                                                @click="editRow(row)" force-icon force-no-text/>
                                                    <nav-button type="danger" icon="mdi mdi-delete"
                                                                title="Reihe löschen"
                                                                @click="deleteRow(row)" force-icon force-no-text/>
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
            </tab>
        </tabs>
    </admin-layout>
</template>

<script>
import {Dataset, DatasetItem, DatasetSearch} from "vue-dataset";
import DatasetInfo from "../../../components/Ui/dataset/DatasetInfo";
import DatasetPager from "../../../components/Ui/dataset/DatasetPager";
import DatasetShow from "../../../components/Ui/dataset/DatasetShow";
import TabHeaders from "../../../components/Ui/tabs/tabHeaders";
import TabHeader from "../../../components/Ui/tabs/tabHeader";
import Tabs from "../../../components/Ui/tabs/tabs";
import Tab from "../../../components/Ui/tabs/tab";
import FormInput from "../../../components/Ui/forms/FormInput";
import FormSelectize from "../../../components/Ui/forms/FormSelectize";
import LocationSelect from "../../../components/Ui/elements/LocationSelect";
import FormTextarea from "../../../components/Ui/forms/FormTextarea";
import SaveButton from "../../../components/Ui/buttons/SaveButton";
import NavButton from "../../../components/Ui/buttons/NavButton";
import SeatingLabel from "../../../components/LocationEditor/SeatingLabel";

export default {
    name: "LocationEditor",
    components: {
        SeatingLabel,
        NavButton,
        SaveButton, FormTextarea, LocationSelect, FormSelectize, FormInput, Tab, Tabs, TabHeader, TabHeaders,
        Dataset,
        DatasetItem,
        DatasetInfo,
        DatasetPager,
        DatasetSearch,
        DatasetShow
    },
    props: ['cities', 'location', 'alternateLocations', 'activeTab', 'seatingRows'],
    data() {
        let myLocation = this.location;
        if (myLocation.default_time) myLocation.default_time = myLocation.default_time.substr(0, 5);

        for (let rowKey in this.seatingRows) {
            this.seatingRows[rowKey]['sectionTitle'] = this.seatingRows[rowKey].seating_section.title;
        }

        return {
            myLocation,
        }
    },
    methods: {
        saveLocation() {
            this.myLocation.alternate_location_id = String(this.myLocation.alternate_location_id) || '';
            if (this.myLocation.id) {
                this.$inertia.patch(route('location.update', this.myLocation.id), this.myLocation);
            } else {
                this.$inertia.post(route('locations.store'), this.myLocation);
            }
        },
        addSection() {},
        editSection(section) {
            this.$inertia.get(route('seatingSection.edit', section.id));
        },
        deleteSection(section) {
            if (!confirm('Willst du diesen Bereich wirklich aus dem Sitzplan löschen?')) return;
            this.$inertia.delete(route('seatingSection.destroy', section.id));
        },
        addRow() {
            this.$inertia.get(route('seatingRow.create', this.myLocation.id));
        },
        editRow(row) {
            this.$inertia.get(route('seatingRow.edit', row.id));
        },
        deleteRow(row) {
            if (!confirm('Willst du diese Reihe wirklich aus dem Sitzplan löschen?')) return;
            this.$inertia.delete(route('seatingRow.destroy', row.id));
        },
    }
}
</script>

<style scoped>
    .seating-section-title, .seating-row-title {
        font-size: 1em;
        font-weight: normal;
    }

    .seating-section-buttons {
        display: none;
    }

    td:hover .seating-section-buttons {
        display: block;
    }
</style>
