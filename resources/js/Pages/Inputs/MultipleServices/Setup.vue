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
    <admin-layout title="Mehrere Gottesdienste anlegen">
        <template slot="navbar-left">
            <nav-button type="primary" title="Gottesdienste anlegen" icon="mdi mdi-table"
                        @click="createServices">Anlegen
            </nav-button>
        </template>
        <div class="row">
            <div class="col-md-6">
                <location-select label="Gottesdienste an folgendem Ort anlegen" :locations="locations"
                                 v-model="setup.location" @input="serviceList" use-input />
            </div>
            <div class="col-md-6">
                <form-input label="Abweichender Titel" v-model="setup.title"  @input="serviceList"/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <form-date-picker v-model="setup.from" name="from" label="Von" iso-date @input="serviceList" />
            </div>
            <div class="col-md-3">
                <form-date-picker v-model="setup.to" name="to" label="Bis" iso-date @input="serviceList" />
            </div>
            <div class="col-md-3">
                <form-selectize label="Wochentag" :options="weekDays" v-model="setup.weekDay"  @input="serviceList"/>
            </div>
            <div class="col-md-3 ">
                <form-group label="Rhythmus">
                    <div class="form-inline">Jede <input class="mx-1 form-control" type="number" v-model="setup.rhythm"
                                                         size="4"  @input="serviceList"/>. Woche
                    </div>
                </form-group>
            </div>
        </div>
        <div v-if="!computing">
            <hr/>
            <dataset v-slot="{ ds }" ref="ds"
                     :ds-data="services"
                     ds-sort-by="date">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover d-md-table">
                                <thead>
                                <tr>
                                    <th>Datum</th>
                                    <th>Uhrzeit</th>
                                    <th>Kirche</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <dataset-item tag="tbody">
                                    <template #default="{ row, rowIndex }">
                                        <tr>
                                            <td>{{ row.date.format('DD.MM.YYYY') }}</td>
                                            <td>
                                                <form-input type="time" v-model="row.time" :key="computeCounter" />
                                            </td>
                                            <td>
                                                {{ row.locationText }}
                                            </td>
                                            <td>
                                                <nav-button class="btn-sm" type="danger" icon="mdi mdi-delete"
                                                            force-no-text force-icon title="Reihe löschen"
                                                            @click="deleteRow(rowIndex)"></nav-button>
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
import FormGroup from "../../../components/Ui/forms/FormGroup";
import FormSelectize from "../../../components/Ui/forms/FormSelectize";
import NavButton from "../../../components/Ui/buttons/NavButton";
import LocationSelect from "../../../components/Ui/elements/LocationSelect";
import FormInput from "../../../components/Ui/forms/FormInput";
import {Dataset, DatasetItem, DatasetSearch} from "vue-dataset";
import DatasetInfo from "../../../components/Ui/dataset/DatasetInfo";
import DatasetPager from "../../../components/Ui/dataset/DatasetPager";
import DatasetShow from "../../../components/Ui/dataset/DatasetShow";
import FormBibleReferenceInput from "../../../components/Ui/forms/FormBibleReferenceInput";
import FormDatePicker from "../../../components/Ui/forms/FormDatePicker";

export default {
    name: "Setup",
    components: {
        FormDatePicker,
        FormBibleReferenceInput,
        FormInput, LocationSelect, NavButton, FormSelectize, FormGroup,
        Dataset,
        DatasetItem,
        DatasetInfo,
        DatasetPager,
        DatasetSearch,
        DatasetShow
    },
    props: ['cities', 'locations'],
    mounted() {
        this.serviceList();
    },
    computed: {
        location() {
            return this.locations.filter(item => {
                return item.id==this.setup.location;
            })[0];
        },
        startDate() {
            return moment(this.setup.from);
        },
        endDate() {
            return typeof this.setup.to == 'Object' ? this.setup.to : moment(this.setup.to);
        },
    },
    data() {
        return {
            myDatePickerConfig: {
                locale: 'de',
                format: 'DD.MM.YYYY',
                showClear: true,
            },
            showEntries: 25,
            computing: false,
            computeCounter: 0,
            setup: {
                from: moment().startOf('year').toISOString(),
                to: moment().endOf('year').toISOString(),
                location: this.locations.length ? this.locations[0].id : null,
                title: '',
                rhythm: 1,
                weekDay: 0,
            },
            weekDays: [
                {id: 0, name: 'Sonntag'},
                {id: 1, name: 'Montag'},
                {id: 2, name: 'Dienstag'},
                {id: 3, name: 'Mittwoch'},
                {id: 4, name: 'Donnerstag'},
                {id: 5, name: 'Freitag'},
                {id: 6, name: 'Samstag'},
            ],
            services: [],
        }
    },
    methods: {
        showTable() {
            this.$inertia.post(route('inputs.input', 'offerings'), this.setup);
        },
        serviceList() {
            if (!this.setup.from) return;
            if (!this.setup.to) return;
            if (!this.setup.rhythm) return;
            if (isNaN(this.setup.weekDay)) return;
            if (this.setup.weekDay < 0) return;
            if (this.setup.weekDay > 6) return;

            this.computing = true;
            this.services = [];
            // find first instance
            let current = moment(this.startDate);
            while (current.format('d') != this.setup.weekDay) current.add(1, 'day');

            while (current <= this.endDate) {
                this.services.push(
                    {
                        date: moment(current),
                        time: this.location.default_time ? this.location.default_time.substr(0,5) : '',
                        locationText: this.location.name,
                        location: this.location.id,
                    }
                );
                current = current.add(7 * this.setup.rhythm, 'days');
            }
            this.computing = false;
            this.computeCounter++;
            this.$forceUpdate();
            this.$nextTick(function() {
                if (this.$refs.ds) this.$refs.ds.setActive(1);
            });
        },
        createServices() {
            this.$inertia.post(route('inputs.save', 'multipleServices'), {services: this.services, title: this.setup.title});
        },
        deleteRow(index) {
            this.services.splice(index, 1);
        }
    }
}
</script>

<style scoped>

</style>
