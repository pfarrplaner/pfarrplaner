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
    <div class="missing-entries-tab">
        <div v-if="count == 0" class="alert alert-info">Zur Zeit gibt es keine fehlenden Einträge.</div>
        <fake-table v-else :columns="[2,4,4,2]" :headers="['Gottesdienst', 'Details', 'Fehlende Daten', '']"
                    collapsed-header="Fehlende Daten">
            <div v-for="(service,serviceIndex) in missing" class="row mb-3 p-1" :class="{'stripe-odd': (serviceIndex % 2 == 0)}"
                :key="serviceIndex">
                <div class="col-md-2">
                    <basic-info :service="service" />
                </div>
                <div class="col-md-4">
                    <details-info :service="service" />
                </div>
                <div class="col-md-4">
                    <span v-for="(ministry,ministryIndex) in missingMinistries(service)" :key="serviceIndex+'_missing_'+ministryIndex"
                          class="badge badge-danger">{{ ministry }}</span>
                </div>
                <div class="col-md-2 text-right">
                    <inertia-link class="btn btn-primary" :href="route('service.edit', service.slug)"
                       title="Eintrag bearbeiten"><span class="mdi mdi-pencil"></span></inertia-link>
                </div>
            </div>
        </fake-table>
    </div>
</template>

<script>
import FakeTable from "../Ui/FakeTable";
import DetailsInfo from "../Service/DetailsInfo";
import BasicInfo from "../Service/BasicInfo";
export default {
    methods: {
        missingMinistries(service) {
            let missingMinistryItems = [];
            this.config.ministries.forEach(ministry => {
                let examine = null;
                switch (ministry) {
                    case 'Pfarrer*in':
                        examine = service.pastors;
                        break;
                    case 'Organist*in':
                        examine = service.organists;
                        break;
                    case 'Mesner*in':
                        examine = service.sacristans;
                        break;
                    default:
                        examine = service.ministriesByCategory[ministry];
                }
                if (!examine) missingMinistryItems.push(ministry);
            });
            return missingMinistryItems;
        }
    },
    name: "MissingEntriesTab",
    components: {BasicInfo, DetailsInfo, FakeTable},
    props: ['title', 'description', 'user', 'settings', 'missing', 'count', 'config']

}
</script>

<style scoped>

</style>
