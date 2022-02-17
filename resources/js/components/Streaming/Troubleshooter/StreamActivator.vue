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
    <div class="stream-activator">
        <h3>{{ title }}</h3>
        <div v-if="broadcasts.length == 0" class="alert alert-warning">
            Diese Liste enthält aktuell keine Livestreams.
        </div>
        <table class="table table-striped table-hover">
            <thead></thead>
            <tbody>
            <tr v-for="(broadcast,key,index) in broadcasts" title="Klicken, um auszuwählen" v-if="!broadcast.service"
                @click="activateBroadcast(broadcast)">
                <td>
                    {{ moment (broadcast.snippet.scheduledStartTime).locale('de-DE').format('LLL') }}<br />
                    {{ broadcast.snippet.title }}
                </td>
                <td>
                    <button class="btn btn-success" @click="activateBroadcast(broadcast)" title="Diesen Livestream aktivieren">
                        <span class="mdi mdi-check"></span> Aktivieren
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    name: "StreamActivator",
    props: ['broadcasts', 'title', 'city'],
    methods: {
        activateBroadcast(broadcast) {
            this.$emit('close');
            this.$inertia.post(route('streaming.troubleshooter.activateBroadcast', {city: this.city.name.toLowerCase(), broadcast: broadcast.id}), { preserveState: false});
        },

    }

}
</script>

<style scoped>

</style>
