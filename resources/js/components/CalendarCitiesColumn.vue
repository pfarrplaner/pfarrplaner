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
    <tbody>
    <tr v-for="city in cities" v-bind:key="city.id">
        <td class="no-print">{{ city.name }}</td>
        <td v-for="day in days" v-bind:key="day.id">
            <calendar-day-events-by-city :day="day" :city="city"></calendar-day-events-by-city>
        </td>
    </tr>
    </tbody>
</template>

<script>
    import CalendarDayEventsByCity from "./CalendarDayEventsByCity";

    export default {
        name: "CalendarCitiesColumn",
        components: {CalendarDayEventsByCity},
        props: [
            'days'
        ],
        data() {
            return {
                cities: [],
            };
        },

        created() {
            this.getData();
        },

        methods: {
            getData(api_url) {
                api_url = api_url || '/api/cities';
                fetch(api_url)
                    .then(response => response.json())
                    .then(response => {
                        this.cities = response;
                    })
                    .catch(err => console.log(err));
            },
        }
    };</script>

<style scoped>

</style>
