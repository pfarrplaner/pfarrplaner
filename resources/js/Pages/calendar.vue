<template>
    <admin-layout enable-control-sidebar="true">
        <template #navbar-left>
            <calendar-nav-top :date="new Date(date)" :years="years"></calendar-nav-top>
        </template>
        <template #control-sidebar>
            <calendar-nav-control-sidebar :date="new Date(date)" :cities="cities"/>
        </template>
        <calendar-pane-horizontal v-if="orientation == 'horizontal'"
                                  :date="date" :days="days" :cities="cityList" :services="services" :years="years"
                                  :absences="absences" :can-create="canCreate"/>
        <calendar-pane-vertical v-else
                                :date="date" :days="days" :cities="cityList" :services="services" :years="years"
                                :absences="absences" :can-create="canCreate "/>
    </admin-layout>
</template>

<script>
import moment from 'moment';
import EventBus from "../plugins/EventBus";
import {CalendarNewSortOrderEvent} from "../events/CalendarNewSortOrderEvent";
import {CalendarNewOrientationEvent} from "../events/CalendarNewOrientationEvent";

export default {
    props: ['date', 'days', 'cities', 'services', 'years', 'absences', 'canCreate'],
    data() {
        return {
            cityList: this.cities,
            orientation: vm.$children[0].page.props.settings.calendar_view,
        }
    },
    mounted() {
        EventBus.listen(CalendarNewSortOrderEvent, this.sortHandler);
        EventBus.listen(CalendarNewOrientationEvent, this.orientationHandler);
    },
    methods: {
        title: function (d) {
            return moment(d).locale('de-DE').format('MMMM YYYY');
        },
        toggleDayColumn: function () {
            alert('hi');
        },
        sortHandler: function (e) {
            this.cityList = e.list;
        },
        orientationHandler: function (e) {
            console.log('orientation change received');
            this.orientation = e.orientation;
            console.log('new orientation: ' + this.orientation);
        },
    },
    computed: {
        storedDays() {
            return this.$store.days;
        }
    }
}
</script>
<style scoped>
th, td {
    vertical-align: top;
}
</style>
