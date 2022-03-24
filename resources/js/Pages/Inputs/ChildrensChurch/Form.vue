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
    <admin-layout title="Plan für die Kinderkirche">
        <template slot="after-flash">
            <div v-if="(serviceSlugs.length - serviceLoaded) > 0" class="alert alert-info">
                Daten für {{ serviceSlugs.length - serviceLoaded }} Gottesdienste werden geladen...
            </div>
            <div v-if="saving" class="alert alert-warning">Änderungen werden gespeichert ... <span
                class="mdi mdi-spin mdi-loading"></span></div>
            <div v-if="saved" class="alert alert-success"><span class="mdi mdi-check"></span> Änderungen wurden
                automatisch gespeichert.
            </div>
        </template>
        <div v-if="(serviceSlugs.length - serviceLoaded) == 0">
            <dataset v-slot="{ ds }"
                     :ds-data="services"
                     ds-sort-by="date">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover d-md-table">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Gottesdienst</th>
                                    <th>Ort der Kinderkirche</th>
                                    <th>Abweichend Uhrzeit</th>
                                    <th>Lektion</th>
                                    <th>Mitarbeiter</th>
                                </tr>
                                </thead>
                                <dataset-item tag="tbody">
                                    <template #default="{ row, rowIndex }">
                                        <tr>
                                            <td>
                                                <form-check v-model="row.cc" title="Kinderkirche findet statt " @input="saveService(row.slug, row)"/>
                                            </td>
                                            <td :class="row.cc ? '' : 'text-muted'">
                                                {{ moment(row.date).format('DD.MM.YYYY') }}, {{
                                                    row.timeText
                                                }}<br/>
                                                {{ row.locationText }}
                                                <div>
                                                    <span class="badge badge-secondary">{{ row.city.name }}</span>
                                                </div>
                                                <div>
                                                    <nav-button type="primary" icon="mdi mdi-pencil" force-icon
                                                                class="btn-sm"
                                                                force-no-text title="Gottesdienst bearbeiten (in neuem Tab)"
                                                                @click="editService(row)">Bearbeiten</nav-button>

                                                </div>
                                            </td>
                                            <td v-if="!row.cc" colspan="4" class="text-muted">
                                                Es findet keine Kinderkirche statt.
                                            </td>
                                            <td v-if="row.cc">
                                                <form-input v-model="row.cc_location" @input="serviceChanged(component, row)"/>
                                            </td>
                                            <td v-if="row.cc">
                                                <form-input v-model="row.cc_alt_time" @input="serviceChanged(component, row)" />
                                            </td>
                                            <td v-if="row.cc">
                                                <form-input v-model="row.cc_lesson" @input="serviceChanged(component, row)" />
                                            </td>
                                            <td v-if="row.cc">
                                                <form-input v-model="row.cc_staff" @input="serviceChanged(component, row)" />
                                            </td>
                                        </tr>
                                    </template>
                                    <template #noDataFound>
                                        <div class="col-md-12 pt-2">
                                            <p class="text-center">No results found</p>
                                        </div>
                                    </template>
                                </dataset-item>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-md-row flex-column justify-content-between align-items-center border-top pt-2">
                    <dataset-info class="mb-2 mb-md-0"/>
                    <dataset-show class="mb-2 mb-md-0" :ds-show-entries="showEntries"/>
                    <dataset-pager/>
                </div>
            </dataset>
        </div>
    </admin-layout>
</template>

<script>
import PeopleSelect from "../../../components/Ui/elements/PeopleSelect";
import NavButton from "../../../components/Ui/buttons/NavButton";
import {Dataset, DatasetItem, DatasetSearch} from "vue-dataset";
import DatasetInfo from "../../../components/Ui/dataset/DatasetInfo";
import DatasetPager from "../../../components/Ui/dataset/DatasetPager";
import DatasetShow from "../../../components/Ui/dataset/DatasetShow";
import FormCheck from "../../../components/Ui/forms/FormCheck";
import FormInput from "../../../components/Ui/forms/FormInput";
import __ from 'lodash';



export default {
    name: "Form",
    components: {
        FormInput,
        FormCheck,
        NavButton, PeopleSelect,
        Dataset,
        DatasetItem,
        DatasetInfo,
        DatasetPager,
        DatasetSearch,
        DatasetShow
    },
    props: ['serviceSlugs', 'users', 'ministries', 'teams'],
    created() {
        this.serviceSlugs.forEach(slug => {
            if (slug) {
                axios.get(route('service.data', slug)).then(response => {
                    this.services.push(response.data);
                    this.serviceLoaded++;
                });
            } else {
                this.serviceLoaded++;
            }
        })
    },
    data() {
        return {
            serviceLoaded: 0,
            services: [],
            saving: false,
            saved: false,
            showEntries: 25,
            component: this,
        }
    },
    methods: {
        serviceChanged: __.debounce((component, service) => {
            component.saveService(service.slug, service);
        }, 1000),
        saveService(slug, service) {
            this.saved = false;
            this.saving = true;
            this.$forceUpdate();
            console.log('updating service ' + slug);

            axios.post(route('inputs.save', 'childrensChurch'), service).then(response => {
                console.log('updated service ' + response.data);
                this.saving = false;
                this.saved = true;
                this.$forceUpdate();
            });
        },
        editService(service) {
            window.open(route('service.edit', service.slug));
        },
    }

}
</script>

<style scoped>
.alert.alert-success {
    animation: fadeOut 5s forwards;
}
</style>
