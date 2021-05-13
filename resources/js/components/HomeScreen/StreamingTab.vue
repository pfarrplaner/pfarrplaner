<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
  - @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
  - @link https://github.com/potofcoffee/pfarrplaner
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
    <div class="streaming-tab">
        <div v-if="count == 0" class="alert alert-info">Zur Zeit gibt es keine Gottesdienste, die gestreamt werden
            könnten.
        </div>
        <fake-table v-else :headers="[2,4,6]" :columns="['Gottesdienst', 'Informationen zum Gottesdienst', '']"
                    collapsed-header="Gottesdienste">
            <div v-for="(service,serviceIndex) in services" :key="serviceIndex" class="row mt-3 p-1"
                 :class="{'stripe-odd': (serviceIndex % 2 == 0)}">
                <div class="col-md-2">
                    <div>{{ moment(service.day.date).locale('de-DE').format('LL') }}</div>
                    <div>{{ service.timeText }}</div>
                    <div>{{ service.locationText }}</div>
                </div>
                <div class="col-md-4">
                    <details-info :service="service"/>
                </div>
                <div class="col-md-6">
                    <a v-if="!service.youtube_url" class="btn btn-primary" title="Neuen Livestream für diesen Gottesdienst anlegen"
                       :href="route('broadcast.create', service.id)">
                        Livestream anlegen
                    </a>
                    <div v-else>
                        <a class="btn btn-light" :href="service.youtube_url" target="_blank"
                           title="Gehe zum Livestream auf YouTube"><span class="fab fa-youtube"></span>
                            <span class="d-none d-md-inline">Video</span>
                        </a>
                        <a class="btn btn-light" :href="route('broadcast.refresh', service.id)"
                           title="Beschreibung auf YouTube erneuern"><span class="fa fa-sync"></span>
                        </a>
                        <button class="btn btn-danger" @click.prevent="deleteBroadcast(service)"
                                title="Livestream löschen"><span class="fa fa-trash"></span></button>
                    </div>
                </div>
            </div>
        </fake-table>
    </div>
</template>

<script>
import FakeTable from "../Ui/FakeTable";
import DetailsInfo from "../Service/DetailsInfo";

export default {
    name: "StreamingTab",
    components: {DetailsInfo, FakeTable},
    props: {
        title: String, description: String, user: Object, settings: Object, services: Array, count: Number,
    },
    methods: {
        deleteBroadcast(service) {
            this.$inertia.delete(route('broadcast.delete', service.id), {preserveState: false});
        }
    }
}
</script>

<style scoped>
    .fab.fa-youtube {
        color: red;
    }
</style>
