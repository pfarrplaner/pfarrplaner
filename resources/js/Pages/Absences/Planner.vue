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
    <admin-layout :title="'Urlaubsplaner '+moment(start).locale('de').format('MMMM YYYY')" no-content-header>
        <template #navbar-left>
            <absence-nav :year="year" :month="month" :years="years"/>
        </template>
        <div v-if="loadingUsers" class="alert alert-info"><span class="fa fa-spin fa-spinner"></span> Lade anzuzeigende
            Benutzer...
        </div>
        <div v-if="loadingDates" class="alert alert-info"><span class="fa fa-spin fa-spinner"></span> Lade Einträge für
            {{ loadingDates }} Benutzer...
        </div>
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
                <tbody v-for="(category,categoryIndex) in sections">
                <tr v-if="categoryIndex > 0" @click="toggleRow(category)" class="category-header">
                    <th :colspan="1+myDays.length" class="p-1">
                        <span class="fa" :class="openSections[category] ? 'fa-chevron-down' : 'fa-chevron-right'"/>
                        <span class="fa fa-users"></span>
                        {{ category }}
                    </th>
                </tr>
                <tr v-for="(user,userIndex) in users[category]" v-if="openSections[category] && userDays[user.id]"
                    :key="category+userIndex+'_'+(openSections['category'] ? 'show' : 'hide')">
                    <th class="user-name pl-2"><span v-if="user.first_name && user.last_name"><span
                        class="text-bold">{{ user.last_name }}</span>, {{ user.first_name }}</span>
                        <span v-else>{{ user.name }}</span>
                        <span v-if="(category == 'Mitarbeitende' || category == 'Ausgeblendete Mitarbeitende') && (!user.show_vacations_with_services) && isPastor"
                              class="fa eye-toggle" :class="user.pinned ? 'fa-eye' : 'fa-eye-slash'"
                              :title="user.pinned ? 'Klicken, wenn diese Person ausgeblendet werden soll' : 'Klicken, wenn diese Person immer angezeigt werden soll'"
                              @click="togglePinned(user)"
                        />
                        <a v-if="user.canEdit" class="btn btn-sm btn-success"
                           :href="route('absence.create', {year: year, month: month, user: user.id})"><span
                            class="fa fa-plus"></span></a>
                    </th>
                    <td v-for="(day,index,key) in myDays" :key="key" :index="index"
                        class="cal-cell"
                        :class="userDays[user.id][day.day].duration ? absenceClass(user, day) : dayClass(user, day)"
                        v-if="userDays[user.id][day.day].show"
                        :title="userDays[user.id][day.day].duration ? absenceTitle(user, userDays[user.id][day.day].absence) : (user.canEdit   ? 'Klicken, um neuen Eintrag anzulegen' : '')"
                        :colspan="colspan(user,day)"
                        @click="edit(user,day,userDays[user.id][day.day].absence)">
                        <div v-if="userDays[user.id][day.day].duration"
                             class="absence" :class="{editable: user.canEdit}">
                            <b>{{ userDays[user.id][day.day].absence.user.name }}</b>
                            <span
                                v-if="user.canEdit || userDays[user.id][day.day].absence.canEdit || userDays[user.id][day.day].absence.replacing">
                                ({{ userDays[user.id][day.day].absence.reason }})<br/>
                                <small v-if="userDays[user.id][day.day].absence.replacementText">
                                    V: {{ userDays[user.id][day.day].absence.replacementText }}</small>
                            </span>
                        </div>
                    </td>
                </tr>
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
    props: ['start', 'end', 'year', 'month', 'months', 'years', 'now', 'holidays', 'days', 'sectionConfig', 'pinList'],
    mounted() {
        axios.get(route('planner.users')).then(response => {
            var users = this.sortUsers(response.data);
            this.loadingUsers = false;
            this.$forceUpdate();
            for (const category in users) {
                users[category].forEach(user => {
                    this.loadingDates++;
                    axios.get(route('planner.days', {user: user.id, date: moment(this.start).format('YYYY-MM')}))
                        .then(response => {
                            this.userDays[user.id] = response.data;
                            this.loadingDates--;
                        });
                });
            }
            this.users = users;
        });
    },
    data() {
        var myDays = [];
        Object.entries(this.days).forEach(day => myDays.push(day[1]));

        return {
            myDays,
            users: {},
            userDays: [],
            loadingUsers: true,
            loadingDates: 0,
            sections: [],
            openSections: this.sectionConfig || {
                'Eigenes Konto': true,
                'Pfarrer*innen': true,
                'Mitarbeitende': true,
                'Ausgeblendete Mitarbeitende': false,
            },
            pinnedUsers: this.pinList,
            isPastor: this.$page.props.currentUser.data.isPastor,
        }
    },
    methods: {
        dayClass(user, day) {
            var prefix = user.canEdit ? 'editable ' : '';
            if (moment(day.date).isoWeekday() == 7) return prefix + 'sunday';
            if (day.holiday) return prefix + 'vacation';
            return prefix + 'day'
        },
        edit(user, day, absence) {
            if (user.canEdit || absence.canEdit) {
                if (absence) {
                    this.$inertia.visit(route('absence.edit', {absence: absence.id}));
                } else {
                    this.$inertia.visit(route('absence.create', {
                        year: this.year,
                        month: this.month,
                        day: day.day,
                        user: user.id
                    }));
                }
            }
        },
        absenceClass(user, day) {
            let absenceStatus = ' absence-status-' + this.userDays[user.id][day.day].absence.workflow_status;
            let editable = ' not-editable';
            if (user.canEdit) editable = ' editable';
            if (this.userDays[user.id][day.day].absence.canEdit) editable = ' editable';
            if (this.userDays[user.id][day.day].absence.sick_days && (this.userDays[user.id][day.day].absence.canEdit || user.canEdit)) editable = ' sick editable';
            if (this.userDays[user.id][day.day].absence.replacing) return 'replacing' + editable;
            return 'absent' + absenceStatus + editable;
        },
        absenceTitle(user, absence) {
            if ((!user.canEdit) && (!absence.canEdit) && (!absence.replacing)) return absence.user.name + ' (' + moment(absence.from).format('DD.MM.YYYY') + ' - '
                + moment(absence.to).format('DD.MM.YYYY') + ')';
            let statusText = '';
            if (absence.workflow_status == 0) statusText = ' Status: Warte auf Überprüfung. ';
            if (absence.workflow_status == 1) statusText = ' Status: Warte auf Genehmigung. ';
            if (absence.workflow_status == 10) statusText = ' Status: Warte auf Genehmigung. ';
            return absence.reason + ' (' + moment(absence.from).format('DD.MM.YYYY') + ' - '
                + moment(absence.to).format('DD.MM.YYYY') + ') V:' + statusText + absence.replacementText + (user.canEdit ? ' --> Klicken, um zu bearbeiten' : '');
        },
        colspan(user, day) {
            if (undefined == this.userDays[user.id]) return 31;
            return this.userDays[user.id][day.day].duration || 1;
        },
        sortUsers(users) {
            let sortedUsers = {};
            let usedIds = [];
            let key = '';
            let pinStatus = true;

            // first: own user
            users.forEach(user => {
                if (user.id == this.$page.props.currentUser.data.id) {
                    sortedUsers['Eigenes Konto'] = [];
                    this.sections.push('Eigenes Konto');
                    sortedUsers['Eigenes Konto'].push(user);
                    usedIds.push(user.id);
                }
            });

            if (this.$page.props.currentUser.data.isPastor) {
                sortedUsers['Pfarrer:innen'] = [];
                this.sections.push('Pfarrer:innen');
                users.forEach(user => {
                    if (usedIds.includes(user.id)) return;
                    if (user.isPastor) {
                        sortedUsers['Pfarrer:innen'].push(user);
                        usedIds.push(user.id);
                    }
                    ;
                });
                sortedUsers['Mitarbeitende'] = [];
                this.sections.push('Mitarbeitende');
                users.forEach(user => {
                    if (usedIds.includes(user.id)) return;
                    if (user.show_absences_with_services || this.pinnedUsers.includes(String(user.id))) {
                        user.pinned = true;
                        sortedUsers['Mitarbeitende'].push(user);
                        usedIds.push(user.id);
                    }
                });
                key = 'Ausgeblendete Mitarbeitende';
                sortedUsers[key] = [];
                this.sections.push(key);
                pinStatus = false;
            } else {
                this.sections.push('Mitarbeitende');
                sortedUsers['Mitarbeitende'] = [];
                key = 'Mitarbeitende';
            }

            users.forEach(user => {
                if (usedIds.includes(user.id)) return;
                user.pinned = pinStatus;
                sortedUsers[key].push(user);
            });


            return sortedUsers;
        },
        toggleRow(category) {
            this.openSections[category] = !this.openSections[category];
            this.$forceUpdate();
            // save setting
            axios.post(route('setting.set', {
                user: this.$page.props.currentUser.data.id,
                key: 'planner_open_sections',
                value: this.openSections,
            }));
        },
        togglePinned(user) {
            user.pinned = !user.pinned;
            if (user.pinned) {
                this.users['Ausgeblendete Mitarbeitende'] = this.users['Ausgeblendete Mitarbeitende'].filter((x) => x.id !== user.id);
                this.pinnedUsers.push(user.id);
                this.users['Mitarbeitende'].push(user);
                this.users['Mitarbeitende'].sort(function (a, b) {
                    return a.sortName < b.sortName ? -1 : 1;
                });
            } else {
                this.users['Mitarbeitende'] = this.users['Mitarbeitende'].filter((x) => x.id !== user.id);
                this.pinnedUsers = this.pinnedUsers.filter((x) => x.id !== user.id);
                this.users['Ausgeblendete Mitarbeitende'].push(user);
                this.users['Ausgeblendete Mitarbeitende'].sort(function (a, b) {
                    return a.sortName < b.sortName ? -1 : 1;
                });
            }
            this.$forceUpdate();
            // save setting
            axios.post(route('setting.set', {
                user: this.$page.props.currentUser.data.id,
                key: 'planner_pinned_users',
                value: this.pinnedUsers,
            }));
        }
    }
}
</script>

<style scoped>
.absent {
    background-color: orange;
}

.tbl-absences .table th,
.tbl-absences .table td {
    padding: 1px;
}

.tbl-absences .table th.user-name {
    padding-left: .25rem;
    font-size: .9em;
    font-weight: normal;
}


.tbl-absences .cal-cell.absent.absence-status-0,
.tbl-absences .cal-cell.absent.absence-status-1,
.tbl-absences .cal-cell.absent.absence-status-10 {
    background-color: papayawhip !important;
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

.not-editable {
    cursor: not-allowed;
}

.editable {
    cursor: pointer;
    height: 100%;
}

.sick.editable {
    background-color: lightcoral;
}

.sick.replacing {
    background-color: lightblue;
}


.editable:hover {
    background-color: darkorange;
}

.day.editable:hover, .sunday.editable:hover, .vacation.editable:hover {
    background-color: #218838;
}

tr.category-header,
tr.category-header th {
    cursor: pointer;
}

.fa.fa-eye {
    color: gray;
}

.fa.fa-eye-slash {
    color: lightgray;
}

.fa.eye-toggle {
    cursor: pointer;
}

</style>
