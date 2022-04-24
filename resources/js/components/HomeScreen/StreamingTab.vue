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
    <div class="streaming-tab">
        <div v-if="count == 0" class="alert alert-info">Zur Zeit gibt es keine Gottesdienste, die gestreamt werden
            könnten.
        </div>
        <div v-else>
            <div v-if="Object.values(streams).length">
                <fake-table :columns="[2,4,6]" :headers="['Ort', 'Aktiver Stream', 'Links']"
                            collapsed-header="Aktive Streams" class="mb-4">
                    <div v-for="localStreams in streams"
                         class="row mt-3 py-1" :class="{'stripe-odd': (serviceIndex % 2 == 0)}">
                        <div class="col-md-2">{{ localStreams.city }}</div>
                        <div class="col-md-4">
                            <checked-process-item :check="localStreams.streams.length == 1" positive="1 aktiver Stream"
                                                  :negative="localStreams.streams.length == 0 ? 'Kein aktiver Stream' : 'Mehrere aktive Streams'">
                            </checked-process-item>
                            <div v-for="stream in localStreams.streams" class="my-1">
                                <div>{{ stream.service.titleText }}</div>
                                <div>{{ moment(stream.service.date).locale('de').format('LLLL') }}</div>
                                <div>{{ stream.service.locationText }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div v-if="localStreams.streams.length == 1" class="mb-1">
                                <a class="btn btn-light" :href="localStreams.streams[0].service.youtube_url" target="_blank"
                                   title="Gehe zum Livestream auf YouTube"><span class="mdi mdi-youtube"></span>
                                    <span class="d-none d-md-inline">Video</span>
                                </a>
                                <a class="btn btn-light" v-if="dashboardUrl(localStreams.streams[0].service)" :href="dashboardUrl(localStreams.streams[0].service)" target="_blank"
                                   title="Gehe zum Live Dashboard auf YouTube"><span class="mdi mdi-video"></span>
                                </a>
                                <a class="btn btn-light" :href="route('broadcast.refresh', localStreams.streams[0].service.id)"
                                   title="Beschreibung auf YouTube erneuern"><span class="mdi mdi-sync"></span>
                                </a>
                            </div>
                            <nav-button type="light" icon="mdi mdi-movie-open-check-outline" force-icon
                                        @click="openTroubleShooter(localStreams.troubleShooter)"
                                        title="Streaming-Troubleshooter öffnen">Streaming-Troubleshooter</nav-button>
                        </div>
                    </div>
                </fake-table>

            </div>
            <fake-table :columns="[2,4,6]" :headers="['Gottesdienst', 'Informationen zum Gottesdienst', '']"
                        collapsed-header="Gottesdienste">
                <div v-for="(service,serviceIndex) in services" :key="serviceIndex" class="row mt-3 p-1"
                     :class="{'stripe-odd': (serviceIndex % 2 == 0)}">
                    <div class="col-md-2">
                        <div>{{ moment(service.date).locale('de-DE').format('LL') }}</div>
                        <div>{{ service.timeText }}</div>
                        <div>{{ service.locationText }}</div>
                    </div>
                    <div class="col-md-4">
                        <details-info :service="service"/>
                    </div>
                    <div class="col-md-6" :key="service.youtube_url+service.creatingStream+service.id">
                        <div v-if="!service.youtube_url">
                            <div v-if="service.city.google_access_token && (!service.creatingStream)" class="form-class">
                                <button class="btn btn-light" @click.prevent="createLivestream(serviceIndex)"><span class="mdi mdi-youtube"></span> Jetzt auf YouTube anlegen</button>
                            </div>
                            <div v-if="service.creatingStream" :key="service.creatingStream">
                                <div class="alert alert-info"><span class="mdi mdi-spin mdi-loading"></span> Ein Livestream wird angelegt. Bitte warten...</div>
                            </div>
                        </div>
                        <div v-else>
                            <a class="btn btn-light" :href="service.youtube_url" target="_blank"
                               title="Gehe zum Livestream auf YouTube"><span class="mdi mdi-youtube"></span>
                                <span class="d-none d-md-inline">Video</span>
                            </a>
                            <a class="btn btn-light" v-if="dashboardUrl(service)" :href="dashboardUrl(service)" target="_blank"
                               title="Gehe zum Live Dashboard auf YouTube"><span class="mdi mdi-video"></span>
                            </a>
                            <a class="btn btn-light" :href="route('broadcast.refresh', service.id)"
                               title="Beschreibung auf YouTube erneuern"><span class="mdi mdi-sync"></span>
                            </a>
                            <button class="btn btn-danger" @click.prevent="deleteBroadcast(service)"
                                    title="Livestream löschen"><span class="mdi mdi-delete"></span></button>
                        </div>
                    </div>
                </div>
            </fake-table>
        </div>
    </div>
</template>

<script>
import FakeTable from "../Ui/FakeTable";
import DetailsInfo from "../Service/DetailsInfo";
import CheckedProcessItem from "../Ui/elements/CheckedProcessItem";
import NavButton from "../Ui/buttons/NavButton";

export default {
    name: "StreamingTab",
    components: {NavButton, CheckedProcessItem, DetailsInfo, FakeTable},
    props: {
        title: String, description: String, user: Object, settings: Object, services: Array, count: Number, streams: Array,
    },
    data() {
        this.services.forEach(service => {
            service.creatingStream = false;
        });
        return {};
    },
    methods: {
        deleteBroadcast(service) {
            this.$inertia.delete(route('broadcast.delete', service.id), {preserveState: false});
        },
        dashboardUrl(service) {
            if (!service.city.youtube_channel_url) return null;
            if (!service.youtube_url) return null;
            var videoId = service.youtube_url.replace('https://youtu.be/', '').replace('https://www.youtube.com/watch?v=', '');
            return 'https://studio.youtube.com/video/'+videoId+'/livestreaming';
        },
        createLivestream(serviceIndex) {
            let service = this.services[serviceIndex];
            let myServiceIndex = serviceIndex;
            this.services[myServiceIndex].creatingStream = true;
            this.$forceUpdate();
            axios.get(route('broadcast.create', {service: service.id, json: 1}))
                .then(response => { return response.data })
                .then(data => {
                    this.services[myServiceIndex].creatingStream = false;
                    this.services[myServiceIndex].youtube_url = data.url;
                    this.$forceUpdate();
                });
        },
        openTroubleShooter(url) {
            window.open(url);
        },
    }
}
</script>

<style scoped>
    .mdi.mdi-youtube {
        color: red;
    }
</style>
