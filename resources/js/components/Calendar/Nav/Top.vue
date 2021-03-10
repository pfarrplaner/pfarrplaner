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
    <div class="button-row no-print btn-toolbar" role="toolbar">
        <div class="btn-group mr-2" role="group">
            <inertia-link class="btn btn-default"
                          v-if="numericDate > 201801"
                          :href="route('calendar', { date: moment(date).subtract(1, 'months').format('YYYY-MM') })"
                          title="Einen Monat zurück">
                <span class="fa fa-backward"></span>
            </inertia-link>
            <inertia-link class="btn btn-default"
                          :href="route('calendar', { date: moment().format('YYYY-MM') })"
                          title="Gehe zum aktuellen Monat">
                <span class="fa fa-calendar-day"></span><span class="d-none d-md-inline"> Gehe zu Heute </span>
            </inertia-link>

            <!-- TODO month / year dropdown -->
            <div class="btn-group" role="group">
                <button id="btnGroupDrop1" type="button" class="btn btn-default dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ moment(date).locale('de-DE').format('MMMM') }}
                </button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                    <inertia-link class="dropdown-item" :href="monthLink(1)">Januar</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(2)">Februar</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(3)">März</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(4)">April</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(5)">Mai</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(6)">Juni</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(7)">Juli</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(8)">August</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(9)">September</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(10)">Oktober</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(11)">November</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(12)">Dezember</inertia-link>
                </div>
            </div>
            <div class="btn-group" role="group">
                <button id="btnGroupDrop2" type="button" class="btn btn-default dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ moment(date).format('YYYY') }}
                </button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop2">
                    <inertia-link v-for="year in years" class="dropdown-item"
                                  :key="year" :year="year" :href="yearLink(year)">{{ year }}
                    </inertia-link>
                </div>
            </div>


            <inertia-link class="btn btn-default"
                          :href="route('calendar', { date: moment(date).add(1, 'months').format('YYYY-MM') })"
                          title="Einen Monat weiter">
                <span class="fa fa-forward"></span>
            </inertia-link>
        </div>
        <div class="btn-group mr-2" role="group" v-if="!slave">
            <a class="btn btn-default btn-toggle-limited-days"
               @click.prevent="toggleColumns"
               title="Alle ausgeblendeten Tage einblenden"><span
                :class="{
                   'fa': true,
                   'fa-square': !allColumnsOpen,
                   'fa-check-square': allColumnsOpen,
                }"></span></a>
        </div>
        <div class="btn-group mr-2" role="group">
            <a class="btn btn-success"
               :href="route('days.add', {year: date.getFullYear(), month: date.getMonth()+1})"
               title="Angezeigte Tage ändern"><span
                class="fa fa-calendar-plus"></span><span class="d-none d-md-inline"> Tage</span></a>
        </div>
        <a class="btn btn-default" :href="route('reports.setup', {report: 'ministryRequest'})"
           title="Dienstanfrage per E-Mail senden"><span class="fa fa-envelope"></span> Anfrage senden...</a>

    </div>

</template>

<script>
import EventBus from "../../../plugins/EventBus";
import {CalendarToggleDayColumnEvent} from "../../../events/CalendarToggleDayColumnEvent";

export default {
    data() {
        return {
            slave: false,
            allColumnsOpen: false,
            numericDate: parseInt(moment(this.date).format('YYYYMM')),
        }
    },
    props: {
        'date': {type: Date}, 'years': {type: Object}
    },
    methods: {
        monthLink: function (month) {
            return route('calendar', {
                date: this.date.getFullYear() + '-' + month
            });
        },
        yearLink: function (year) {
            return route('calendar', {
                date: year + '-' + (this.date.getUTCMonth() + 1)
            });
        },
        toggleColumns() {
            this.allColumnsOpen = !this.allColumnsOpen;
            EventBus.publish(new CalendarToggleDayColumnEvent(null, !this.allColumnsOpen));
        }
    }
}
</script>

<style scoped>

</style>
