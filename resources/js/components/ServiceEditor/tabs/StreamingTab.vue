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
        <div v-if="(!(myService.youtube_url)) && (!creatingStream)" class="alert alert-info">
            Diesem Gottesdienst ist noch kein Livestream zugeordnet.
            <div v-if="myService.city.google_access_token" class="form-class">
                <button class="btn btn-light" @click.prevent="createLivestream"><span class="fab fa-youtube"></span> Jetzt auf YouTube anlegen</button>
            </div>
            <hr />
            <form-input label="YouTube-Adresse" help="Möglichkeit zur manuellen Eingabe eines YouTube-Links"
                        v-model="myService.youtube_url" />
        </div>
        <div v-if="creatingStream">
            <div class="alert alert-info"><span class="fa fa-spinner fa-spin"></span> Ein Livestream wird angelegt. Bitte warten...</div>
        </div>
        <div v-if="myService.youtube_url">
            <div class="row">
                <div class="col-md-6">
                    <form-input name="youtube_url" label="YouTube-URL" v-model="myService.youtube_url" />
                    <div v-if="myService.city.google_access_token">
                        <form-input name="cc_streaming_url" label="URL zu einem parallel gestreamten Kindergottesdienst" v-model="myService.cc_streaming_url" />
                        <form-input name="offering_url" label="URL zu einer Seite für Onlinespenden" v-model="myService.offering_url" />
                        <form-input name="meeting_url" label="URL zu einer Seite für ein 'virtuelles Kirchencafé'" v-model="myService.meeting_url" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group" v-if="service.youtube_url">
                        <iframe width="560" height="315" :src="embedUrl" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe><br />
                        <form-group>
                            <a class="btn btn-light" :href="myService.youtube_url" title="Öffnet das YouTube-Video in einem neuen Fenster" target="_blank"><span class="fab fa-youtube"></span> Zum Video</a>
                            <a v-if="myService.city.google_access_token" class="btn btn-light" :href="dashboardUrl" title="Öffnet das  Live-Dashboard zum Stream in einem neuen Fenster" target="_blank"><span class="fa fa-video"></span> Zum Live-Dashboard</a>
                        </form-group>
                    </div>
                </div>
            </div>
            <div class="row" v-if="myService.city.google_access_token">
                <div class="col-md-6">
                    <form-textarea name="youtube_prefix_description" label="Einleitender Text für die Beschreibung auf YouTube" v-model="myService.youtube_prefix_description" />
                </div>
                <div class="col-md-6">
                    <form-textarea name="youtube_postfix_description" label="Ergänzender Text für die Beschreibung auf YouTube" v-model="myService.youtube_postfix_description" />
                </div>
            </div>
        </div>
        <hr />
        <div class="row">
            <div class="col-md-6">
                <form-input name="recording_url" label="URL zu einer Audioaufzeichnung des Gottesdiensts" v-model="myService.recording_ucrl" />
            </div>
            <div class="col-md-6">
                <audio v-if="myService.recording_url" :src="myService.recording_url" controls />
            </div>
        </div>
    </div>
</template>

<script>
import FormInput from "../../Ui/forms/FormInput";
import FormTextarea from "../../Ui/forms/FormTextarea";
import FormRadioGroup from "../../Ui/forms/FormRadioGroup";
import FormCheck from "../../Ui/forms/FormCheck";
import FormGroup from "../../Ui/forms/FormGroup";

export default {
    name: "StreamingTab",
    components: {
        FormGroup,
        FormCheck,
        FormRadioGroup,
        FormTextarea,
        FormInput,
    },
    props: {
        service: Object,
    },
    computed: {
        videoId() {
            var url = this.myService.youtube_url;
            if (!url) return '';
            if (url.substr(-1) == '/') url = url.substr(0, -1);
            var tmp = url.split('/');
            console.log('id', tmp[tmp.length-1]);
            return tmp[tmp.length-1];
        },
        embedUrl() {
            if (!this.videoId) return '';
            return 'https://www.youtube.com/embed/'+this.videoId;
        },
        dashboardUrl() {
            if (!this.videoId) return '';
            return 'https://studio.youtube.com/video/'+this.videoId+'/livestreaming';
        }
    },
    data() {
        var myService = this.service;
        if (!(myService.offering_url)) myService.offering_url = this.service.city.default_offering_url;
        return {
            myService: myService,
            creatingStream: false,
        }
    },
    methods: {
        createLivestream() {
            this.creatingStream = true;
            axios.get(route('broadcast.create', {service: this.service.id, json: 1}))
            .then(response => { return response.data })
            .then(data => {
                this.creatingStream = false;
                this.myService.youtube_url = data.url;
            });
        }
    }
}
</script>

<style scoped>
    span.fab.fa-youtube {
        color: red;
    }
</style>
