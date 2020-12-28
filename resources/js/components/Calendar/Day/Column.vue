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
    <td valign="top" v-bind:class="{
            now: false, // TODO: next day
            limited: day.day_type == 1, // DAY_TYPE_LIMITED
            collapsed: collapsed,
            'for-city': isForCity,
        }"
        @click="clickHandler()"
    >
        <div class="celldata">
            <calendar-service v-for="(service,index) in services" :service="service" :key="service.id" :index="index"/>
        </div>
    </td>
</template>
<script>
import EventBus from "../../../plugins/EventBus";
import { CalendarToggleDayColumnEvent} from "../../../events/CalendarToggleDayColumnEvent";

export default {
    props: ['city', 'day', 'services'],
    data: function() {
        return {
            collapsed: this.hasMine ? false : (this.day.day_type == 1),
            initialCollapse: this.hasMine ? false : (this.day.day_type == 1),
            initialHasMine: this.hasMine,
            initialDayTypeBool: this.day.day_type == 1,
        }
    },
    mounted() {
        EventBus.listen(CalendarToggleDayColumnEvent, this.toggleHandler);
        if ((this.hasMine) && (this.day.day_type == 1)) {
            EventBus.publish(new CalendarToggleDayColumnEvent(this.day, false));
        }
    },
    computed: {
        isForCity() {
            var found = false;
            var city = this.city;
            this.day.cities.forEach(function(thisCity){
                if (thisCity.id == city.id) found = true;
            });
            return found;
        },
        hasMine() {
            if (undefined == this.services) return undefined;
            var found = false;
            this.services.forEach(function(service){
                found = found || service.isMine;
            });
            return found;
        }
    },
    methods: {
        clickHandler: function() {
            EventBus.publish(new CalendarToggleDayColumnEvent(this.day, !this.collapsed));
        },
        toggleHandler: function(e) {
            if ((null === e.day) || (e.day.id == this.day.id)) this.collapsed = e.state;
        }
    }
}
</script>
