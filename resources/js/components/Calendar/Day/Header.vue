<template>
    <th
        v-bind:class="{
            now: false, // TODO: next day
            limited: day.day_type == 1, // DAY_TYPE_LIMITED
            collapsed: this.day.collapsed,
        }"
        @click="clickHandler()"
        :title="day.day_type == 1 ? today().format('DD.MM.YYYY')+' (Klicken, um Ansicht umzuschalten)' : ''"
        :data-day="day.id">
        <div class="day-header-collapse-hover">{{ today().format('dddd, DD.') }}</div>
        <div class="card card-effect">
            <div :class="{'card-header': 1, 'day-header-So': today().format('E') == 7}">
                {{ today().format('dddd') }}
            </div>
            <div class="card-body">
                {{ today().format('D') }}
            </div>
            <div class="liturgy">
                <div class="liturgy-sermon" v-if="day.liturgy.perikope">
                    <div :class="day.liturgy.litColor" class="liturgy-color" :title="day.liturgy.feastCircleName"></div>
                    <a :href="day.liturgy.currentPerikopeLink" target="_blank">
                        {{ day.liturgy.currentPerikope }}
                    </a>
                </div>
            </div>
            <div class="card-footer day-name" :title="day.liturgy.litProfileGist" v-if="day.liturgy.title">
                {{day.liturgy.title}}
            </div>
        </div>
        <div v-if="hasPermission('urlaub-lesen')">
        <div class="vacation mr-1" v-for="absence in absences" :absence="absence"
             :title="absence.user.name+': '+absence.reason+' ('+absence.durationText+') '+replacementText(absence)">
            <span class="fa fa-globe-europe"></span> {{ absence.user.last_name }}</div>
        </div>
    </th>
</template>

<script>
import moment from "moment";
import EventBus from "../../../plugins/EventBus";
import { CalendarToggleDayColumnEvent} from "../../../events/CalendarToggleDayColumnEvent";

export default {
    props: ['day', 'index', 'absences'],
    data: function() {
        return {
            limited: this.day.day_type == 1,
        }
    },
    methods: {
        moment: function (d) {
            return moment(d).locale('de-DE');
        },
        today: function () {
            return moment(this.day.date).locale('de-DE');
        },
        clickHandler: function() {
            console.log('toggle collapse');
            this.$emit('collapse', {day: this.day, state: !(this.day.collapsed)});
            this.$forceUpdate();
        },
        replacementText: function (absence) {
            return absence.replacementText ? '[V: '+absence.replacementText+']' : '';
        },
    },
}
</script>

<style scoped>
    .liturgy-color.white {
        background-color: white;
        border-color: darkgray;
    }
    .liturgy-color.black {
        background-color:black;
    }
    .liturgy-color.green {
        background-color: darkgreen;
    }
    .liturgy-color.purple {
        background-color: rebeccapurple;
    }

</style>
