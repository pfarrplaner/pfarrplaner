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
    <admin-layout :title="today.format('ddd, DD. MMMM YYYY')+' ('+city.name+')'" no-content-header>
        <template slot="navbar-left">
            <a class="btn btn-light" :href="route('calendar', {date: today.format('YYYY-MM')})">
                <span class="fa fa-calendar"></span> {{ today.format('MMMM YYYY') }}
            </a>
        </template>
        <table class="table calendar-month">
            <thead>
            <tr>
                <th>
                    <div class="card card-effect">
                        <div :class="{'card-header': 1, 'day-header-So': today.format('E') == 7}">
                            {{ today.format('dddd') }}
                        </div>
                        <div class="card-body">
                            {{ today.format('D') }}
                        </div>
                        <div class="liturgy">
                            <div class="liturgy-sermon" v-if="day.liturgy.perikope">
                                <div :class="day.liturgy.litColor" class="liturgy-color" :title="day.liturgy.feastCircleName"></div>
                                <a :href="day.liturgy.currentPerikopeLink" target="_blank">
                                    {{ day.liturgy.currentPerikope }}
                                </a>
                            </div>
                        </div>
                        <div class="card-footer day-name" :title="day.liturgy.litProfileGist" v-if="day.liturgy.title">
                            {{day.liturgy.title}}
                        </div>
                    </div>
                </th>
            </tr>
            <tr>
                <th class="text-center">
                    {{ city.name }}
                    <div v-if="(absences.length) && $can('urlaub-lesen')" class="my-1">
                        <div class="vacation mr-1" v-for="absence in absences" :absence="absence"
                             :title="absence.user.name+': '+absence.reason+' ('+absence.durationText+') '+replacementText(absence)">
                            <span class="fa fa-globe-europe"></span> {{ absence.user.last_name }}</div>
                    </div>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <calendar-cell :day="day" :services="services" :city="city" :can-create="canCreate" :uncollapsed="true"/>
            </tr>
            </tbody>
        </table>
    </admin-layout>
</template>

<script>
export default {
    name: "SingleDay",
    props: ['services', 'day', 'city', 'absences', 'canCreate'],
    data() {
        return {
            today: moment(this.day.date).locale('de'),
        }
    },
    methods: {
        back() {
            window.history.back();
        },
        replacementText: function (absence) {
            return absence.replacementText ? '[V: '+absence.replacementText+']' : '';
        },
    }
}
</script>

<style scoped>

</style>
