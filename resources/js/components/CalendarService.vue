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
    <div v-if="loading"><span class="fa fa-spin fa-spinner"></span></div>
    <div v-else class="service-entry editable" style="cursor: pointer;"
         title="Klicken, um diesen Eintrag zu bearbeiten" onclick="">
        <template v-if="null != service.special_location">
            <div class="service-time">
                {{ service.timeText }} HI
            </div>
            <span class="separator">|</span>
            <div class="service-location">{{ service.special_location }}</div>
        </template>
        <template v-else>
            <div class="service-time">
                {{ service.timeText }} HO
            </div>
            <span class="separator">|</span>
            <div class="service-location">{{ service.location.name }}</div>
        </template>
        <img v-if="service.cc" src="/img/cc.png" :title="ccInfo()"/>
        <div class="service-team service-pastor">
            <span class="designation">P: </span>
            <span v-if="service.need_predicant" class="need-predicant">Prädikant benötigt</span>
            <span v-for="person in service.pastors">{{ person.name }} </span>
        </div>
        <div class="service-team service-organist">
            <span class="designation">O: </span>
            <span v-for="person in service.organists">{{ person.name }} </span>
        </div>
        <div class="service-team service-sacristan">
            <span class="designation">M: </span>
            <span v-for="person in service.sacristans">{{ person.name }} </span>
        </div>
        <div class="service-description" v-html="service.descriptionText"></div>
        <div class="service-description">
            <span v-if="service.baptisms.length > 0" :title="service.baptismsText"><span
                class="fa fa-droplet"></span>{{ service.baptims.length }}</span>
        </div>
    </div>
</template>

<script>
export default {
    name: "CalendarService",
    props: [
        'serviceId'
    ],
    data() {
        return {
            service: [],
            loading: true,
        }
    },
    created() {
        this.getData();
    },

    methods: {
        getData(api_url) {
            api_url = api_url || '/api/service/' + this.serviceId;
            fetch(api_url)
                .then(response => response.json())
                .then(response => {
                    this.service = response;
                    this.loading = false;
                })
                .catch(err => console.log(err));
        },
        ccInfo: function () {
            return 'Parallel Kinderkirche (' + service.cc_location + ') zum Thema ' + service.cc_lesson + ': ' + service.cc_staff;
        }
    }
}
</script>

<style scoped>

</style>
