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
    <div class="mb-1">
        <h6>Reihenfolge</h6>
        <draggable :list="list1" group="cities" @start="drag=true" @end="drag=false" class="sortable-cities" @change="handleChange">
            <div v-for="city in list1" :key="city.id" class="sortable-city"><span class="mdi mdi-church"></span>
                {{ city.name }}
            </div>
        </draggable>
        Nicht anzeigen:<br/>
        <draggable :list="list2" group="cities" @start="drag=true" @end="drag=false" class="sortable-cities">
            <div v-for="city in list2" :key="city.id" class="sortable-city"><span class="mdi mdi-church"></span>
                {{ city.name }}
            </div>
        </draggable>
    </div>
</template>

<script>
import draggable from 'vuedraggable'
import EventBus from "../../../plugins/EventBus";
import {CalendarNewSortOrderEvent} from '../../../events/CalendarNewSortOrderEvent';

export default {
    components: {
        draggable,
    },
    props: ['cities'],
    data() {
        return {
            list1: Object.values(this.cities),
            list2: Object.values(window.vm.$children[0].$page.props.currentUser.data.hiddenCities),
            user: window.vm.$children[0].$page.props.currentUser.data,
        }
    },
    beforeMount() {
    },
    methods: {
        handleChange() {
            EventBus.publish(new CalendarNewSortOrderEvent(this.list1));

            var ids = [];
            this.list1.forEach(function(city){
                ids.push(city.id);
            });
            axios.post(route('setting.set', {user: this.user.id, key: 'sorted_cities'}), {
                value: ids.join(',')
            });
        }
    }
}
</script>

<style scoped>
.sortable-cities {
    min-height: 50px;
}

.sortable-city {
    width: 100%;
    border: solid 1px gray;
    list-style: none;
    padding: 5px;
    margin: 1px;
    border-radius: 3px;
    cursor: move !important;
}
</style>
