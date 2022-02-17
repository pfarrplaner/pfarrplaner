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
    <div v-if="loading"><span class="mdi mdi-spin mdi-loading"></span></div>
    <div v-else>
        <calendar-service v-for="service in services" v-bind:key="service" :serviceId="service"></calendar-service>
        <a class="btn btn-success btn-sm btn-add-day" title="Neuen Gottesdiensteintrag hinzufügen"
           :href="serviceAddRoute()"><span class="mdi mdi-plus"></span></a>

    </div>
</template>

<script>
    import CalendarDayEventsByCity from "./CalendarDayEventsByCity";
    import CalendarService from "./CalendarService";
    export default {
        name: "CalendarDayEventsByCity",
        components: {CalendarService},
        props: [
            'day',
            'city'
        ],
        data() {
            return {
                services: [],
                loading: true,
            };
        },

        created() {
            this.getData();
        },

        methods: {
            getData(api_url) {
                api_url = api_url || route('api.services.byDayAndCity', {day: this.day.id, city: this.city.id});
                fetch(api_url)
                    .then(response => response.json())
                    .then(response => {
                        this.services = response;
                        this.loading = false;
                    })
                    .catch(err => console.log(err));
            },
            serviceAddRoute: function() {
                return route('services.add', {day: this.day.id, city: this.city.id});
            }
        }
    };</script>

<style scoped>

</style>
