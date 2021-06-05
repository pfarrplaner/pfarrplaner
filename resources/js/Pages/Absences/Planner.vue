<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
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
    <admin-layout :title="'Urlaubsplaner '+moment(start).locale('de').format('MMMM YYYY')">
        <template #navbar-left>
            <absence-nav :year="year" :month="month" :years="years" />
        </template>
        <div v-if="loadingUsers" class="alert alert-info"><span class="fa fa-spin fa-spinner"></span> Lade anzuzeigende Benutzer...</div>
        <div v-if="loadingDates" class="alert alert-info"><span class="fa fa-spin fa-spinner"></span> Lade Einträge für {{ loadingDates }} Benutzer...</div>
        <div class="table-responsive tbl-absences">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th></th>
                    <th v-for="(day,index,key) in myDays" :key="key" :index="index"
                        class="cal-cell text-center" :class="dayClass(day, false)">
                        <div>{{ moment(day.date).locale('de').format('dd') }}</div>
                        <div>{{ moment(day.date).locale('de').format('DD') }}</div>
                    </th>
                </tr>
                </thead>
                    <tr v-for="user in users" v-if="userDays[user.id]">
                        <th>{{ user.name }}
                            <a v-if="user.canEdit" class="btn btn-sm btn-success"
                               :href="route('absences.create', {year: year, month: month, user: user.id})"><span
                                class="fa fa-plus"></span></a>
                        </th>
                        <td v-for="(day,index,key) in myDays" :key="key" :index="index"
                            class="cal-cell" :class="userDays[user.id][day.day].duration ? absenceClass(user, day) : dayClass(user, day)"
                            v-if="userDays[user.id][day.day].show"
                            :title="userDays[user.id][day.day].duration ? absenceTitle(user, userDays[user.id][day.day].absence) : (user.canEdit ? 'Klicken, um neuen Eintrag anzulegen' : '')"
                            :colspan="colspan(user,day)"
                            @click="edit(user,day,userDays[user.id][day.day].absence)" >
                            <div v-if="userDays[user.id][day.day].duration"
                                class="absence" :class="{editable: user.canEdit}">
                                <b>{{ userDays[user.id][day.day].absence.user.name }}</b> ({{ userDays[user.id][day.day].absence.reason }})<br />
                                <small v-if="userDays[user.id][day.day].absence.replacementText">V: {{ userDays[user.id][day.day].absence.replacementText }}</small>
                            </div>
                        </td>
                    </tr>
                <tbody>
                </tbody>
            </table>
        </div>

    </admin-layout>
</template>

<script>
import AbsenceNav from "./AbsenceNav";
export default {
    name: "Planner",
    components: {AbsenceNav},
    props: ['start', 'end', 'year', 'month', 'months', 'years', 'now', 'holidays', 'days'],
    mounted() {
        axios.get(route('planner.users')).then(response => {
            var users = response.data;
            this.loadingUsers = false;
            this.$forceUpdate();
            users.forEach(user => {
                this.loadingDates++;
                axios.get(route('planner.days', {user: user.id, date: moment(this.start).format('YYYY-MM')}))
                .then(response => {
                    this.userDays[user.id] = response.data;
                    this.loadingDates--;
                });
            });
            this.users = users;
        });
    },
    data() {
        var myDays = [];
        Object.entries(this.days).forEach(day => myDays.push(day[1]));
        return {
            myDays: myDays,
            users: [],
            userDays: [],
            loadingUsers: true,
            loadingDates: 0,
        }
    },
    methods: {
        dayClass(user, day) {
            var prefix = user.canEdit ? 'editable ' : '';
            if (moment(day.date).isoWeekday() == 7) return prefix+'sunday';
            if (day.holiday) return prefix+'vacation';
            return prefix+'day'
        },
        edit(user, day, absence) {
            if (user.canEdit) {
                if (absence) {
                    this.$inertia.visit(route('absences.edit', {absence: absence.id}));
                } else {
                    this.$inertia.visit(route('absences.create', {year: this.year, month: this.month, day: day.day, user:user.id}));
                }
            }
        },
        absenceClass(user, day) {
            if (this.userDays[user.id][day.day].absence.replacing) return 'replacing';
            if (user.canEdit) return 'absent editable';
            return 'absent';
        },
        absenceTitle(user, absence) {
            return absence.reason+' ('+moment(absence.from).format('DD.MM.YYYY')+' - '
                +moment(absence.to).format('DD.MM.YYYY')+') V:'+absence.replacementText+(user.canEdit ? ' --> Klicken, um zu bearbeiten' : '');
        },
        colspan(user, day) {
            if(undefined == this.userDays[user.id]) return 31;
            return this.userDays[user.id][day.day].duration || 1;
        },
    }
}
</script>

<style scoped>
    .absent {
        background-color: orange;
    }
    .replacing {
        background-color: lightblue;
    }
    .sunday {
        background-color: lightpink;
        color: red;
    }
    .vacation {
        background-color: #e9fce9;
    }
    .editable {
        cursor: pointer;
        height: 100%;
    }
    .editable:hover {
        background-color: darkorange;
    }
    .day.editable:hover, .sunday.editable:hover, .vacation.editable:hover {
        background-color: #218838;
    }
</style>
