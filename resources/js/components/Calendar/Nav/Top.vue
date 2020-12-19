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
               :href="route('cal.index', { date: moment(date).subtract(1, 'months').format('YYYY-MM') })"
               title="Einen Monat zurück">
                <span class="fa fa-backward"></span>
            </inertia-link>
            <inertia-link class="btn btn-default"
               :href="route('cal.index', { date: moment().format('YYYY-MM') })"
               title="Gehe zum aktuellen Monat">
                <span class="fa fa-calendar-day"></span><span class="d-none d-md-inline"> Gehe zu Heute</span></inertia-link>

            <!-- TODO month / year dropdown -->
            <div class="btn-group" role="group">
                <button id="btnGroupDrop1" type="button" class="btn btn-default dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ moment(date).locale('de-DE').format('MMMM') }}
                </button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                    <inertia-link class="dropdown-item" :href="monthLink(date, 1)">Januar</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(date, 2)">Februar</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(date, 3)">März</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(date, 4)">April</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(date, 5)">Mai</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(date, 6)">Juni</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(date, 7)">Juli</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(date, 8)">August</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(date, 9)">September</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(date, 10)">Oktober</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(date, 11)">November</inertia-link>
                    <inertia-link class="dropdown-item" :href="monthLink(date, 12)">Dezember</inertia-link>
                </div>
            </div>
            <div class="btn-group" role="group">
                <button id="btnGroupDrop2" type="button" class="btn btn-default dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ moment(date).format('YYYY') }}
                </button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop2">
                    <inertia-link href="" v-for="year in years" class="dropdown-item" :key="year" :year="year">{{ year }}</inertia-link>
                </div>
            </div>


            <inertia-link class="btn btn-default"
               :href="route('cal.index', { date: moment(date).add(1, 'months').format('YYYY-MM') })"
               title="Einen Monat weiter">
                <span class="fa fa-forward"></span>
            </inertia-link>
        </div>
        <div class="btn-group mr-2" role="group" v-if="!slave">
            <a class="btn btn-default btn-toggle-limited-days"
               href="#"
               title="Alle ausgeblendeten Tage einblenden"><span
                class="fa @if(Session::get('showLimitedDays') === true) fa-check-square @else fa-square @endif"></span></a>
        </div>
        <!-- TODO: filtered locations -->
    </div>

</template>

<script>
export default {
    data: {
        slave: false,
    },
    props: ['date', 'years'],
    methods: {
        monthLink: function(date, month) {
            return moment(date).set({'year': date.year, 'month': month, day: 1}).format('YYYY-MM');
        }
    }
}
</script>

<style scoped>

</style>
