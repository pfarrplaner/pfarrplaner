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
        'editable': service.isEditable,
        'mine': service.isMine,
        'highlighted': 0,
        'funeral': service.funerals.length > 0,
        'hidden': service.hidden}"
        :title="service.isEditable ? clickTitle(service) : null"
         @click.stop="service.isEditable ? edit(service) : null"
        :data-day="service.day.id"
    >
        <div :class="{'service-time': 1,  'service-special-time': isSpecialTime(service)}">
            {{ service.time }} Uhr
        </div>
        <span class="separator">|</span>
        <div :class="{'service-location': 1, 'service-special-location': isSpecialLocation(service)}">
            {{ isSpecialLocation(service) ?  service.special_location : service.location.name }}
        </div>
        <img v-if="service.cc" src="/img/cc.png" :title="ccTitle(service)">
        <span v-if="service.youtube_url">
            <a :href="service.youtube_url" target="_blank" class="youtube-link" title="Zum Youtube-Video"><span class="fab fa-youtube"></span></a>
            <a v-if="service.city.youtube_channel_url" :href="service.liveDashboardUrl" target="_blank" class="youtube-livedashboard-link" title="Zum LiveDashboard"><span class="fa fa-video"></span></a>
        </span>
        <div v-if="(service.titleText != 'Gottesdienst') && (service.titleText != 'GD') && (service.funerals.length == 0)" class="service-description">{{ service.titleText }}</div>

        <div class="service-description" v-if="service.funerals.length > 0">
            <span class="fa fa-cross"></span>
            <calendar-service-funeral v-for="(funeral, index) in service.funerals" :key="funeral.id" :funeral="funeral" trailer=", " :trail="index > 0"/>
        </div>
        <div class="service-description" v-html="service.descriptionText"></div>
        <div class="service-description" v-if="service.internal_remarks">
            <span class="fa fa-eye-slash" title="Anmerkung nur für den internen Gebrauch"></span> {{ service.internal_remarks }}
        </div>

        <calendar-service-participants :participants="service.pastors" category="P" :predicant="service.need_predicant" />
        <calendar-service-participants :participants="service.organists" category="O" :predicant="0" />
        <calendar-service-participants :participants="service.sacristans" category="M" :predicant="0" />
        <calendar-service-participants v-for="participants,ministry in service.ministriesByCategory" :key="ministry"
                                       :participants="participants" :category="ministry" :predicant="0" />
        <div v-if="hasPermission('gd-kasualien-lesen') || hasPermission('gd-kasualien-nur-statistik')">
            <div class="service-description" v-if="service.baptisms.length > 0">
                <span class="fa fa-water" :title="hasPermission('gd-kasualien-lesen') ? service.baptismsText : ''"></span> {{ service.baptisms.length }}
            </div>
        </div>
    </div>
</template>
<script>


export default {
    props: ['service'],
    computed: {
    },
    methods: {
        isSpecialTime: function(service) {
            if (null == service.location) return true;
            if (null == service.location.default_time) return true;
            if ('' == service.location.default_time) return true;
            return service.time != service.location.default_time.substr(0,5);
        },
        isSpecialLocation: function(service) {
            return service.location == null;
        },
        ccTitle: function(service) {
            return 'Parallel Kinderkirche ('+service.cc_location+') zum Thema "'+service.cc_lesson+'": '+service.cc_staff;
        },
        clickTitle: function (service) {
            return service.isEditable ? 'Klicken, um diesen Eintrag zu bearbeiten (#'+service.id+')' : 'Nicht bearbeitbar';
        },
        redirect: function(url) {
            window.location.href=url;
        },
        edit: function(service) {
            this.$inertia.visit(route('service.edit', service.id));
        },
    }
}

</script>

<style scoped>
    .service-entry.editable {
        cursor: pointer;
    }
    .youtube-link {
        color: red;
    }
    .youtube-livedashboard-link {
        color: darkgray;
    }
</style>
