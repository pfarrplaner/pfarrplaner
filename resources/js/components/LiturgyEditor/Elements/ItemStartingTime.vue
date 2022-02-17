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
    <div>
        <small><span class="mdi" :class="(start == '00:00') ? 'mdi-timer' : 'mdi-clock'"></span>
            {{ formatTime(startingTime(), (start == '00:00')) }} {{ (start == '00:00') ? '' : 'Uhr' }}
        </small>
    </div>
</template>

<script>

import ItemTextStats from "./ItemTextStats";
import Vue from "vue";

export default {
    name: "ItemStartingTime",
    props: ['service', 'item', 'start'],
    methods: {
        startingTime() {
            let found = false;
            let seconds = this.countStarter();
            this.service.liturgy_blocks.forEach(block => {
                block.items.forEach(item => {
                    found = found || (this.item.id == item.id);
                    if (!found) {
                        seconds += this.itemTime(item);
                    }
                });
            });
            return seconds;
        },
        countStarter() {
            let t = this.start || this.service.time;
            if (t.length == 5) t += ':00';
            console.log('count starter', t, this.parseTimeString(t));
            return this.parseTimeString(t);
        },
        itemTime(item) {
            if (item.data.time) return this.parseTimeString(item.data.time);

            let itemTextStatsClass = Vue.extend(ItemTextStats);
            let itemTextStatsInstance = new itemTextStatsClass({
                propsData: {
                    item: item,
                    service: this.service,
                }
            });
            return itemTextStatsInstance.speechTimeInSeconds();
        },
        parseTimeString(s) {
            let t = s.split(':');
            switch (t.length) {
                case 3:
                    return (3600*parseInt(t[0], 10))+(60*parseInt(t[1], 10))+parseInt(t[2], 10);
                case 2:
                    return (60*parseInt(t[0],10))+parseInt(t[1],10);
            }
            return parseInt(t[0],10);
        },
        formatTime(sec_num, showSeconds) {
            var hours = Math.floor(sec_num / 3600);
            var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
            var seconds = sec_num - (hours * 3600) - (minutes * 60);

            var hoursText = '';
            if (hours) {
                hoursText = hours+':';
            }
            if (minutes < 10) {
                minutes = "0" + minutes;
            }
            if (seconds < 10) {
                seconds = "0" + seconds;
            }
            return hoursText + minutes + (showSeconds ? ':' + seconds : '');
        },
    }
}
</script>

<style scoped>
    .fa {
        color: lightgray;
    }
</style>
