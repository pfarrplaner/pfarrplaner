<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
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
    <main class="py-1">
        <div class="calendar-month">

            <h1 class="print-only">Bla</h1>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th class="no-print">Kirchengemeinde</th>
                    <th v-for="day in days"
                        v-bind:key="day.id"
                        v-html="formatDate(day.date)"
                        :class="renderClassAttribute(day)"
                        :title="renderDayTitle(day)"
                        :data-day="day.id"
                    ></th>
                </tr>
                </thead>
                <calendar-cities-column :days="days"></calendar-cities-column>
            </table>
        <hr/>
        </div>
    </main>

</template>

<script>
    export default {
        name: "CalendarMonth",
        props: [
            'year',
            'month',
        ],
        data() {
            return {
                days: [],
            };
        },

        created() {
            this.getData();
        },

        methods: {
            getData(api_url) {
                api_url = api_url || '/api/calendar/month/'+this.year+'/'+this.month;
                fetch(api_url)
                    .then(response => response.json())
                    .then(response => {
                        this.days = response.days;
                    })
                    .catch(err => console.log(err));
            },
            formatDate: function(dateString) {
                var m = moment(dateString);
                if (m.day() > 0) {
                    return '<span class="special-weekday">'+m.format('dd')+'.</span>, '+m.format('DD.MM.YYYY');
                } else {
                    return m.format('DD.MM.YYYY');
                }
            },
            renderClassAttribute: function(day) {
                return 'hide_buttons '
                    + (day.day_type == 1 ? 'limited collapsed ' : '');
            },
            renderDayTitle: function(day) {
                return (day.day_type == 1 ? moment(day.date).format('DD.MM.YYYY')+ ' (Klicken, um Ansicht umzuschalten)' : '');
            }
        }
    };
</script>

<style scoped>

</style>
