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
        <div class="row">
            <div class="col-md-6">
                 <form-input name="youtube_url" label="YouTube-URL" v-model="myService.youtube_url" />
                 <form-input name="cc_streaming_url" label="URL zu einem parallel gestreamten Kindergottesdienst" v-model="myService.cc_streaming_url" />
                 <form-input name="offering_url" label="URL zu einer Seite für Onlinespenden" v-model="myService.offeringUrl" />
                 <form-input name="meeting_url" label="URL zu einer Seite für ein 'virtuelles Kirchencafé'" v-model="myService.offeringUrl" />
            </div>
            <div class="col-md-6">
                <div class="form-group" v-if="service.youtube_url">
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/tE4Jh2Fqwbc" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe><br />
                    <small>Diesem Gottesdienst ist bereits ein Livestream zugeordnet.</small>
                    <form-group>
                        <a class="btn btn-light" href="#" title="Zum Video auf YouTube" target="_blank"><span class="fab fa-youtube" style="color:red;"></span> Zum Video</a>
                    </form-group>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <form-textarea name="youtube_prefix_description" label="Einleitender Text für die Beschreibung auf YouTube" v-model="myService.youtube_prefix_description" />
            </div>
            <div class="col-md-6">
                <form-textarea name="youtube_postfix_description" label="Ergänzender Text für die Beschreibung auf YouTube" v-model="myService.youtube_postfix_description" />
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
        disabled() {
            return (this.myService.cc == false) || (this.myService.cc == 0);
        }
    },
    data() {
        return {
            myService: this.service,
        }
    },
    methods: {}
}
</script>

<style scoped>

</style>
