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
    <div class="service-team">
        <span class="designation">{{ category }}: </span>
        <span v-if="predicant" class="need-predicant">Prädikant benötigt</span>
        <span v-for="person,index in participants"><span :class="{me: person.id == user.id}">{{ formatName(person) }}</span><span v-if="index<participants.length-1"> | </span></span>
    </div>
</template>

<script>
import EventBus from "../../../plugins/EventBus";
import {CalendarNewNameFormatEvent} from "../../../events/CalendarNewNameFormatEvent";

export default {
    props: ['participants', 'category', 'predicant'],
    data() {
        return {
            nameFormat: vm.$children[0].page.props.settings.calendar_name_format,
            user: vm.$children[0].page.props.currentUser.data,
        }
    },
    mounted() {
        EventBus.listen(CalendarNewNameFormatEvent, this.handeNameFormatChange);
    },
    methods: {
        formatName(person) {
            switch(parseInt(this.nameFormat)) {
                case 1:
                    if (person.last_name=='') return person.name;
                    return [person.title, person.last_name].join(' ').trim();
                case 2:
                    if (person.last_name=='') return person.name;
                    if (person.first_name=='') return person.name;
                    return [person.title, person.first_name.substr(0,1)+'.', person.last_name].join(' ').trim();
                case 3:
                    if (person.last_name=='') return person.name;
                    if (person.first_name=='') return person.name;
                    return [person.title, person.first_name, person.last_name].join(' ').trim();
                default:
                    return '[['+this.nameFormat+']]';
            }
        },
        handeNameFormatChange(e) {
            this.nameFormat = e.format;
        }
    }
}
</script>

<style scoped>
    .me {
        font-weight: bold;
    }
</style>
