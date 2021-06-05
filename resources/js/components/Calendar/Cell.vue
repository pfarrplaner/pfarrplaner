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
    <td valign="top" v-bind:class="{
            now: false, // TODO: next day
            limited: limited, // DAY_TYPE_LIMITED
            collapsed: this.day.collapsed,
            'for-city': isForCity,
        }"
        @click="clickHandler()"
    >
        <div class="celldata">
            <calendar-service v-for="(service,index) in services" :service="service" :key="service.id" :index="index"/>
            <a v-if="canCreate && user.writableCities.includes(city.id)"
               class="btn btn-success btn-sm btn-add-day"
               title="Neuen Gottesdiensteintrag hinzufügen"
               :href="route('services.add', {date: day.id, city: city.id})"><span
                class="fa fa-plus" title="Neuen Gottesdienst hinzufügen"></span><span class="d-none d-md-inline"> Neuer GD</span></a>
        </div>
    </td>
</template>
<script>
import EventBus from "../../plugins/EventBus";
import { CalendarToggleDayColumnEvent} from "../../events/CalendarToggleDayColumnEvent";

export default {
    props: ['city', 'day', 'services', 'canCreate', 'uncollapsed'],
    data: function() {
        return {
            user: window.vm.$children[0].page.props.currentUser.data,
            limited: this.day.day_type == 1,
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
    },
    methods: {
        clickHandler: function() {
            if (this.limited) {
                console.log('toggle collapse');
                this.$emit('collapse', {day: this.day, state: !this.day.collapsed});
                this.$forceUpdate();
            }
        },
    }
}
</script>
