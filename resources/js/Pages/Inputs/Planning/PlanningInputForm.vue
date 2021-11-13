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
    <admin-layout title="Planungstabelle">
        <div v-if="(serviceSlugs.length - serviceLoaded) > 0" class="alert alert-info">
            Daten für {{ serviceSlugs.length - serviceLoaded }} Gottesdienste werden geladen...
        </div>
        <div v-if="saving" class="alert alert-warning">Änderungen werden gespeichert ... <span class="fa fa-spin fa-spinner"></span></div>
        <div v-if="saved" class="alert alert-success"><span class="fa fa-check"></span> Änderungen wurden automatisch gespeichert.</div>
        <fake-table :columns="[3, 9]" collapsed-header="Gottesdienste"
                    :headers="['Gottesdienst', 'Diensteinteilung']"></fake-table>
        <div v-for="serviceSlug in serviceSlugs" class="row">
            <div class="col-md-3 pb-1 px-1 pl-2 border-bottom" v-if="services[serviceSlug]">
                {{ moment(services[serviceSlug].day.date).format('DD.MM.YYYY') }}, {{
                    services[serviceSlug].timeText
                }}<br/>
                {{ services[serviceSlug].locationText }}
            </div>
            <div class="col-md-9 pb-1 px-1 border-bottom" v-if="services[serviceSlug]">
                <div class="row">
                    <div v-for="(ministry,ministryKey,ministryIndex) in ministries" class="col-12" :class="'col-md-'+columnWidth">
                            <people-select :label="ministry" :people="users" :teams="teams" :include-teams-from-city="city"
                                           v-model="services[serviceSlug].ministries[ministryKey]"
                                           @input="saveService(serviceSlug, services[serviceSlug])" />
                    </div>
                </div>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import FakeTable from "../../../components/Ui/FakeTable";
import PeopleSelect from "../../../components/Ui/elements/PeopleSelect";

export default {
    name: "PlanningInputForm",
    components: {PeopleSelect, FakeTable},
    props: ['serviceSlugs', 'users', 'ministries', 'teams', 'city'],
    mounted() {
        var myMinistries = [];
        Object.keys(this.ministries).forEach(ministry => {
            if (!['P','O','M','A'].includes(ministry)) myMinistries.push(ministry);
        })
        this.serviceSlugs.forEach(slug => {
            if (slug) {
                axios.get(route('service.data', slug)).then(response => {
                    var record = {
                        ministries: {},
                        slug: response.data.slug,
                        day: response.data.day,
                        locationText: response.data.locationText,
                        timeText: response.data.timeText,
                    };
                    for (const ministryKey in this.ministries) {
                        if (!['P','O','M','A'].includes(ministryKey)) {
                            record['ministries'][ministryKey] = response.data.ministriesByCategory[ministryKey] || [];
                        } else {
                            record['ministries'][ministryKey] = response.data[this.basicMinistryKeys[ministryKey]] || [];
                        }
                    }

                    this.services[response.data.slug] = record;
                    this.serviceLoaded++;
                });
            } else {
                this.serviceLoaded++;
            }
        })
    },
    data() {
        let columnWidth = 4;
        let ministryCount = Object.keys(this.ministries).length;
        if (ministryCount == 1) columnWidth = 12;
        if (ministryCount == 2) columnWidth = 6;

        return {
            basicMinistryKeys: {P: 'pastors', O: 'organists', M: 'sacristans', A: 'otherParticipants'},
            serviceLoaded: 0,
            services: {},
            columnWidth: columnWidth,
            saving: false,
            saved: false,
        }
    },
    methods: {
        saveService(slug, service) {
            this.saved = false;
            this.saving = true;
            this.$forceUpdate();
            console.log('updating service '+slug);

            axios.post(route('inputs.save', 'planning'), service).then(response => {
                console.log('updated service '+response.data);
                this.saving = false;
                this.saved = true;
                this.$forceUpdate();
            });
        }
    }

}
</script>

<style scoped>
    .alert.alert-success {
        animation: fadeOut 5s forwards;
    }
</style>
