<template>
    <th
        v-bind:class="{
            now: false, // TODO: next day
            limited: day.day_type == 1, // DAY_TYPE_LIMITED
            collapsed: day.day_type == 1,
            'not-for-city': false, // TODO: not_for_city
        }"
        :title="day.day_type == 1 ? today().format('d.m.Y')+' (Klicken, um Ansicht umzuschalten)' : ''"
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
    </th>
</template>

<script>
import moment from "moment";

export default {
    props: ['day', 'index'],
    methods: {
        moment: function (d) {
            return moment(d).locale('de-DE');
        },
        today: function () {
            return moment(this.day.date).locale('de-DE');
        }
    }
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
