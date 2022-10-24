<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2022 Christoph Fischer, https://christoph-fischer.org
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
    <admin-layout title="Geburtstagsbesuche eingetragen">
        <h2>Die folgenden Termine wurden in deinen Kalender eingetragen</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Datum, Uhrzeit</th>
                <th>Ziel</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(event,eventKey) in events" :key="event">
                <td>{{ moment(event.startDate).locale('de').format('DD.MM.YYYY') }} {{ moment(event.startDate).locale('de').format('LT') }} - {{ moment(event.endDate).locale('de').format('LT') }}</td>
                <td>
                    <div class="text-bold">{{ event.title }}</div>
                    <div>{{ event.location }}</div>
                </td>
            </tr>
            </tbody>
        </table>
    </admin-layout>
</template>

<script>
export default {
    name: "Results",
    props: ['events']
}
</script>

<style scoped>

</style>
