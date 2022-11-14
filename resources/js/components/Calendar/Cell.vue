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
    <td valign="top"
        class="calendar-cell"
        v-bind:class="{
            now: false, // TODO: next day
        }"
    >
        <div class="celldata">
            <div v-if="city.loading" class="city-loading"><span class="mdi mdi-spin mdi-loading"></span></div>
            <calendar-service v-for="(service,index) in services" :service="service" :key="service.id" :index="index"
                              :targetMode="targetMode" :target="target"
            />
        </div>
    </td>
</template>
<script>
import EventBus from "../../plugins/EventBus";
import { CalendarToggleDayColumnEvent} from "../../events/CalendarToggleDayColumnEvent";

export default {
    props: ['city', 'day', 'services', 'targetMode', 'target'],
}
</script>
<style scoped>
    .calendar-cell {
        padding: 0;
    }

    .city-loading {
        text-align: center;
        background-color: transparent !important;
        color: lightgray !important;
        font-size: 3em !important;
    }



</style>
