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
    <div>
        <div class="mb-1">
            <a class="btn btn-primary" :href="route('calendar', {
                date: moment(date).format('YYYY-MM'), slave: 1
            })"
               target="_blank"
               title="Öffnet ein weiteres Fenster mit einer Kalenderansicht, die der hier dargestellten automatisch folgt.">
                <span class="mdi mdi-monitor"></span> 2. Bildschirm anzeigen
            </a>
        </div>
        <hr class="mb-2">
        <h6>Anordnung</h6>
        <div class="mb-1">
            <input name="orientation" value="horizontal" type="radio" v-model="orientation" @change="handleOrientationChange"> Horizontale
            Ansicht <br/>
            <small>(Tage als Spalten)</small>
            <br/>
            <input name="orientation" value="vertical" type="radio" v-model="orientation" @change="handleOrientationChange"> Vertikale
            Ansicht <br/>
            <small>(Tage als Zeilen)</small>
            <br/>
        </div>
        <hr class="mb-2">
        <calendar-control-city-sort :cities="cities" />
        <hr class="mb-2">
        <form-check label="Details zur Kinderkirche anzeigen" v-model="mySettings.show_cc_details" @input="setSetting('show_cc_details', $event)"/>
    </div>
</template>

<script>
import EventBus from "../../../plugins/EventBus";
import draggable from 'vuedraggable'
import {CalendarNewOrientationEvent} from "../../../events/CalendarNewOrientationEvent";
import {CalendarNewNameFormatEvent} from "../../../events/CalendarNewNameFormatEvent";
import FormCheck from "../../Ui/forms/FormCheck";

export default {
    components: {
        FormCheck,
        draggable,
    },
    props: ['date', 'cities'],
    inject: ['settings'],
    data() {
        return {
            orientation: window.vm.$children[0].$page.props.settings.calendar_view,
            nameFormat: window.vm.$children[0].$page.props.settings.calendar_name_format,
            user: window.vm.$children[0].$page.props.currentUser.data,
            mySettings: this.settings,
        }
    },
    methods: {
        handleOrientationChange() {
            EventBus.publish(new CalendarNewOrientationEvent(this.orientation));
            axios.post(route('setting.set', {user: this.user.id, key: 'calendar_view'}), {
                value: this.orientation
            });
        },
        setSetting(key, value) {
            this.$emit('setSetting', {key, value});
            axios.post(route('setting.set', {user: this.user.id, key: key}), {
                value: value,
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
