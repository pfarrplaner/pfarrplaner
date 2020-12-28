<template>
    <admin-layout>
        <template #navbar-left>
            <calendar-nav-top :date="new Date(date)" :years="years"></calendar-nav-top>
        </template>
        <div class="calendar-month calendar-horizontal">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th class="no-print text-left">Kirchengemeinde<!-- // TODO: slave mode --></th>
                    <calendar-day-header
                        v-for="(day,index) in days"
                        :day="day"
                        :key="day.id"
                        :index="index"
                        @toggle-day-column="toggleDayColumn" />
                </tr>
                </thead>
                <tbody>
                <calendar-city-row
                    v-for="(city,index) in cities"
                    :city="city"
                    :index="index"
                    :key="city.id"
                    :services="services[city.id]"
                    :days="days"
                    @toggle-day-column="toggleDayColumn" />
                </tbody>
            </table>
        </div>
    </admin-layout>
</template>

<script>
import moment from 'moment';

export default {
    props: ['date', 'days', 'cities', 'services', 'years'],
    data() {
        return {
        }
    },
    methods: {
        title: function (d) {
            return moment(d).locale('de-DE').format('MMMM YYYY');
        },
        toggleDayColumn: function() {
            alert('hi');
        }
    },
    computed: {
        storedDays() {
            return this.$store.days;
        }
    }
}
</script>
