<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
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
    <div :class="{
        'service-entry': 1,
        'editable': myService.isEditable,
        'mine': myService.isMine,
        'highlighted': 0,
        'possible-target': targetMode,
        'funeral': myService.funerals.length > 0,
        'reloading': loading,
        'hidden': myService.hidden}"
         :title="myService.isEditable ? clickTitle(service) : null"
         @click.stop="myService.isEditable ? edit(service, $event) : null"
    >
        <div v-if="loading" class="text-center"><span class="mdi mdi-spin mdi-loading"></span></div>
        <div v-else>
            <div :class="{'service-time': 1,  'service-special-time': isSpecialTime(service)}">
                {{ myService.timeText }}
            </div>
            <span class="separator">|</span>
            <div :class="{'service-location': 1, 'service-special-location': isSpecialLocation(service)}">
                {{ isSpecialLocation(service) ? myService.special_location : myService.location.name }}
            </div>
            <img v-if="(!settings.show_cc_details) && (myService.cc)" src="/img/cc.png" :title="ccTitle(service)">
            <span v-if="myService.youtube_url">
            <a :href="myService.youtube_url" target="_blank" class="youtube-link" title="Zum Youtube-Video"><span
                class="mdi mdi-youtube"></span></a>
            <a v-if="myService.city.youtube_channel_url" :href="myService.liveDashboardUrl" target="_blank"
               class="youtube-livedashboard-link" title="Zum LiveDashboard"><span class="mdi mdi-video"></span></a>
        </span>
            <controlled-access v-if="myService.controlled_access" :service="service"/>
            <div
                v-if="(myService.titleText != 'Gottesdienst') && (myService.titleText != 'GD') && (myService.funerals.length == 0)"
                class="service-description">{{ myService.titleText }}
            </div>

            <div class="service-description" v-if="myService.funerals.length > 0">
                <span class="mdi mdi-grave-stone"></span>
                <calendar-service-funeral v-for="(funeral, index) in myService.funerals" :key="funeral.id"
                                          :funeral="funeral" trailer=", " :trail="index > 0"/>
            </div>
            <div class="service-description" v-html="myService.descriptionText"></div>
            <div class="service-description" v-if="myService.internal_remarks">
                <span class="mdi mdi-eye-off" title="Anmerkung nur für den internen Gebrauch"></span>
                {{ myService.internal_remarks }}
            </div>

            <calendar-service-participants :participants="myService.pastors" category="P"
                                           :predicant="myService.need_predicant"/>
            <calendar-service-participants :participants="myService.organists" category="O" :predicant="0"/>
            <calendar-service-participants :participants="myService.sacristans" category="M" :predicant="0"/>
            <calendar-service-participants v-for="participants,ministry in myService.ministriesByCategory"
                                           :key="ministry"
                                           :participants="participants" :category="ministry" :predicant="0"/>
            <div v-if="hasPermission('gd-kasualien-lesen') || hasPermission('gd-kasualien-nur-statistik')">
                <div class="service-description" v-if="myService.baptisms.length > 0">
                    <span class="mdi mdi-water"
                          :title="hasPermission('gd-kasualien-lesen') ? myService.baptismsText : ''"></span>
                    {{ myService.baptisms.length }}
                </div>
            </div>
            <div v-if="settings.show_cc_details && (myService.cc)">
                <hr/>
                <img src="/img/cc.png" :title="ccTitle(service)">
                Kinderkirche: {{ myService.cc_lesson }} ({{ myService.cc_staff }})
            </div>
        </div>
    </div>
</template>
<script>


import ControlledAccess from "./Element/ControlledAccess";

export default {
    components: {ControlledAccess},
    props: ['service', 'targetMode', 'target'],
    inject: ['settings'],
    data() {
        return {
            myService: this.service,
            apiToken: this.$page.props.currentUser.data.api_token,
            loading: false,
        }
    },
    methods: {
        isSpecialTime: function (service) {
            if (null == service.location) return true;
            if (null == service.location.default_time) return true;
            if ('' == service.location.default_time) return true;
            return service.time != service.location.default_time.substr(0, 5);
        },
        isSpecialLocation: function (service) {
            return service.location == null;
        },
        ccTitle: function (service) {
            return 'Parallel Kinderkirche (' + service.cc_location + ') zum Thema "' + service.cc_lesson + '": ' + service.cc_staff;
        },
        clickTitle: function (service) {
            if (this.targetMode && service.isEditable) {
                let people = [];
                this.target.people.forEach(person => people.push(person.name));
                return 'Klicken für ' + this.target.ministry + ': ' + people.join(', ');
            }
            return service.isEditable ? 'Klicken, um diesen Eintrag zu bearbeiten (#' + service.id + ')' : 'Nicht bearbeitbar';
        },
        redirect: function (url) {
            window.location.href = url;
        },
        edit: function (service, clickEvent) {
            console.log('service clicked');
            if (this.targetMode) {
                let peopleIds = [];
                this.target.people.forEach(person => peopleIds.push(person.id));
                this.loading = true;

                axios.post(route('api.service.assign', service.id), {
                    api_token: this.apiToken,
                    ministry: this.target.ministry,
                    users: peopleIds,
                    exclusive: this.target.exclusive,
                }).then(response => {
                    this.myService = response.data.service;
                    this.loading = false;
                });
                return;
            }
            if (clickEvent.ctrlKey) {
                window.open(route('service.edit', service.slug), '_blank');
            } else {
                this.$inertia.visit(route('service.edit', service.slug));
            }
        },
    }
}

</script>

<style scoped>
.service-entry.reloading {
    border-color: lightgoldenrodyellow;
    background-color: transparent !important;
    color: #ffc107 !important;
    font-size: 3em !important;
}

.service-entry.editable.possible-target:not(.mine) {
    background-color: lightgoldenrodyellow;
}

.service-entry.editable.possible-target:hover {
    background-color: #ffc107;
    box-shadow: 0 0 0 .2rem lightgoldenrodyellow;
    border: 0;
}


.service-entry.editable {
    cursor: pointer;
}

.service-entry.editable.possible-target {
    cursor: crosshair;
}

.youtube-link {
    color: red;
}

.youtube-livedashboard-link {
    color: darkgray;
}
</style>
