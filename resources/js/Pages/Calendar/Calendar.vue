<template>
    <admin-layout enable-control-sidebar="true" :title="title(date)">
        <template #navbar-left>
            <calendar-nav-top :date="new Date(date)" :years="years"></calendar-nav-top>
        </template>
        <template #control-sidebar>
            <calendar-nav-control-sidebar :date="new Date(date)" :cities="cities"/>
        </template>
        <div class="alert alert-info" v-if="loading > 0"><span class="fa fa-spin fa-spinner"></span> Daten für {{ loading }} Orte werden geladen ...</div>
        <div class="d-none d-md-inline">
            <calendar-pane-horizontal v-if="orientation == 'horizontal'"
                                      :date="date" :days="days" :cities="cityList" :services="services" :years="years"
                                      :absences="absences" :can-create="canCreate"/>
            <calendar-pane-vertical v-else
                                    :date="date" :days="days" :cities="cityList" :services="services" :years="years"
                                    :absences="absences" :can-create="canCreate "/>
        </div>
        <div class="d-inline d-md-none">
            <!-- mobile version -->
            <calendar-pane-mobile :date="date" :days="days" :cities="cityList" :services="services" :years="years"
                                    :absences="absences" :can-create="canCreate" :loading="loading"/>
        </div>
    </admin-layout>
</template>

<script>
import moment from 'moment';
import EventBus from "../../plugins/EventBus";
import {CalendarNewSortOrderEvent} from "../../events/CalendarNewSortOrderEvent";
import {CalendarNewOrientationEvent} from "../../events/CalendarNewOrientationEvent";
import CalendarPaneMobile from "../../components/Calendar/Pane/Mobile";

export default {
    components: {CalendarPaneMobile},
    props: ['date', 'days', 'cities',  'years', 'absences', 'canCreate', 'services'],
    data() {
        return {
            cityList: this.cities,
            orientation: vm.$children[0].page.props.settings.calendar_view,
            myServices: this.services,
            loading: 0,
        }
    },
    created() {
        this.cities.forEach(city => {
            this.loading++;
            axios.get(route('cal.city', {date: moment(this.date).format('YYYY-MM'), city: city.id}))
            .then(response => {
                this.myServices[city.id] = response.data;
                this.loading--;
                this.$forceUpdate();
            });
        });
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
