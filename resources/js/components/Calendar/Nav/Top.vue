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
    <div class="button-row no-print btn-toolbar" role="toolbar">
        <div class="btn-group mr-2" role="group">
            <button class="btn btn-default"
                    v-if="numericDate > 201801"
                    @click.prevent.stop="$emit('navigate', moment(date).subtract(1, 'months').format('YYYY-MM'))"
                    title="Einen Monat zurück">
                <span class="mdi mdi-chevron-left"></span>
            </button>
            <button class="btn btn-default" @click.prevent.stop="today">
                <span class="mdi mdi-calendar-today"></span><span class="d-none d-md-inline"> Gehe zu Heute </span>
            </button>

            <!-- TODO month / year dropdown -->
            <div class="btn-group" role="group">
                <button id="btnGroupDrop1" type="button" class="btn btn-default dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ moment(date).locale('de-DE').format('MMMM') }}
                </button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                    <a class="dropdown-item" href="#"
                       @click.prevent.stop="$emit('navigate', moment(date).format('YYYY')+'-01')">Januar</a>
                    <a class="dropdown-item" href="#"
                       @click.prevent.stop="$emit('navigate', moment(date).format('YYYY')+'-02')">Februar</a>
                    <a class="dropdown-item" href="#"
                       @click.prevent.stop="$emit('navigate', moment(date).format('YYYY')+'-03')">März</a>
                    <a class="dropdown-item" href="#"
                       @click.prevent.stop="$emit('navigate', moment(date).format('YYYY')+'-04')">April</a>
                    <a class="dropdown-item" href="#"
                       @click.prevent.stop="$emit('navigate', moment(date).format('YYYY')+'-05')">Mai</a>
                    <a class="dropdown-item" href="#"
                       @click.prevent.stop="$emit('navigate', moment(date).format('YYYY')+'-06')">Juni</a>
                    <a class="dropdown-item" href="#"
                       @click.prevent.stop="$emit('navigate', moment(date).format('YYYY')+'-07')">Juli</a>
                    <a class="dropdown-item" href="#"
                       @click.prevent.stop="$emit('navigate', moment(date).format('YYYY')+'-08')">August</a>
                    <a class="dropdown-item" href="#"
                       @click.prevent.stop="$emit('navigate', moment(date).format('YYYY')+'-09')">September</a>
                    <a class="dropdown-item" href="#"
                       @click.prevent.stop="$emit('navigate', moment(date).format('YYYY')+'-10')">Oktober</a>
                    <a class="dropdown-item" href="#"
                       @click.prevent.stop="$emit('navigate', moment(date).format('YYYY')+'-11')">November</a>
                    <a class="dropdown-item" href="#"
                       @click.prevent.stop="$emit('navigate', moment(date).format('YYYY')+'-12')">Dezember</a>
                </div>
            </div>
            <div class="btn-group" role="group">
                <button id="btnGroupDrop2" type="button" class="btn btn-default dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ moment(date).format('YYYY') }}
                </button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop2">
                    <a v-for="year in years" class="dropdown-item" href="#"
                       @click.prevent.stop="$emit('navigate', year+'-'+moment(date).format('MM'))"
                       :key="year">{{ year }}
                    </a>
                </div>
            </div>
            <button class="btn btn-default"
                    v-if="numericDate > 201801"
                    @click.prevent.stop="$emit('navigate', moment(date).add(1, 'months').format('YYYY-MM'))"
                    title="Einen Monat weiter">
                <span class="mdi mdi-chevron-right"></span>
            </button>
        </div>
        <nav-button class="mr-2"
                    :type="targetMode ? 'warning' : 'default'"
                    :icon="targetMode ? (target.exclusive ? 'mdi mdi-account-convert-outline': 'mdi mdi-account-arrow-down-outline') : 'mdi mdi-target-account'"
                    :force-no-text="!targetMode"
                    force-icon
                    :title="'Benutzer schnell zuordnen'+(targetMode ? ' ('+targetTitle()+(target.exclusive ? ', überschreiben' : '')+')' : '')"
                    @click="$emit('toggle-target-mode', !targetMode)">
            {{ targetTitle() }}
        </nav-button>

        <a class="btn btn-default" :href="route('reports.setup', {report: 'ministryRequest'})"
           title="Dienstanfrage per E-Mail senden"><span class="mdi mdi-email"></span> <span class="d-none d-md-inline">Anfrage senden...</span></a>

    </div>

</template>

<script>
import EventBus from "../../../plugins/EventBus";
import {CalendarToggleDayColumnEvent} from "../../../events/CalendarToggleDayColumnEvent";
import NavButton from "../../Ui/buttons/NavButton";

export default {
    components: {NavButton},
    data() {
        return {
            slave: false,
            allColumnsOpen: false,
            numericDate: parseInt(moment(this.date).format('YYYYMM')),
        }
    },
    props: {
        date: {type: Date},
        years: {type: Array},
        orientation: {type: String},
        targetMode: {type: Boolean},
        target: {type: Object},
    },
    methods: {
        monthLink: function (month) {
            return route('calendar', {
                date: this.date.getFullYear() + '-' + month
            });
        },
        yearLink: function (year) {
            return route('calendar', {
                date: year + '-' + (this.date.getUTCMonth() + 1)
            });
        },
        toggleColumns() {
            this.allColumnsOpen = !this.allColumnsOpen;
            this.$emit('collapseall', this.allColumnsOpen);
        },
        today() {
            if ((this.orientation == 'vertical') && (moment(this.date).format('YYYYMM') == moment().format('YYYYMM'))) {
                var el = document.getElementsByClassName('scroll-to-me');
                if (el) {
                    el[0].parentElement.scrollIntoView();
                    window.scroll(0, window.scrollY - 84);
                }
            } else {
                this.$emit('navigate', moment().format('YYYY-MM'));
            }
        },
        targetTitle() {
            if (!this.targetMode) return '';
            let people = [];
            this.target.people.forEach(person => people.push(person.name));
            return this.target.ministry + ': ' + people.join(', ');
        }
    }
}
</script>

<style scoped>

</style>
