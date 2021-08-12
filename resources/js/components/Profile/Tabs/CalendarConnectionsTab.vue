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
    <div class="calendar-connections-tab">
        <div v-if="calendarConnections.length == 0" class="alert alert-info">Du hast noch keine externen Kalender verbunden.</div>
        <div class="mb-2">
            <inertia-link class="btn btn-light" title="Neuen Kalender verbinden"
                          :href="route('calendarConnection.create')">
                <span class="d-inline d-md-none fa fa-plus"></span><span class="d-none d-md-inline">Neue Verbindung anlegen</span>
            </inertia-link>
        </div>
        <fake-table v-if="calendarConnections.length >0"
                    :columns="[5,5,2]" collapsed-header="Kalender"
                    :headers="['Titel', 'Typ', '']" >
            <div class="row p-1" v-for="(calendarConnection, key) in calendarConnections" :key="key">
                <div class="col-md-5">{{ calendarConnection.title }}</div>
                <div class="col-md-5">{{ connectionType(calendarConnection) }}</div>
                <div class="col-md-2 text-right">
                    <button class="btn btn-light" @click="editConnection(calendarConnection)"
                            title="Verbindung bearbeiten">
                        <span class="fa fa-edit"></span>
                    </button>
                    <button class="btn btn-danger" @click="deleteConnection(calendarConnection)"
                            title="Verbindung löschen">
                        <span class="fa fa-trash"></span>
                    </button>
                </div>
            </div>
        </fake-table>
    </div>
</template>

<script>
import FakeTable from "../../Ui/FakeTable";
export default {
    name: "CalendarConnectionsTab",
    components: {FakeTable},
    props: ['user', 'calendarConnections'],
    methods: {
        connectionType(calendarConnection) {
            if (!calendarConnection.connection_string) return 'Unbekannt';
            var prefix = calendarConnection.connection_string.substr(0,3);
            if (prefix=='exc') return 'Exchange-Kalender';
            if (prefix=='sps') return 'Sharepoint-Ordner';
            if (prefix=='spp') return 'Persönlicher Sharepoint-Ordner';
            return 'Unbekannt';
        },
        editConnection(calendarConnection) {
            this.$inertia.get(route('calendarConnection.edit', calendarConnection.id));
        },
        deleteConnection(calendarConnection) {
            if (confirm('Willst du diese Verbindung wirklich löschen?')) {
                this.$inertia.delete(route('calendarConnection.destroy', calendarConnection.id));
            }
        },
    }
}
</script>

<style scoped>

</style>
