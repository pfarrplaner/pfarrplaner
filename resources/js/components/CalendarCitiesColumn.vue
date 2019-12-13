<template>
    <tbody>
    <tr v-for="city in cities" v-bind:key="city.id">
        <td class="no-print">{{ city.name }}</td>
        <td v-for="day in days" v-bind:key="day.id">
            <calendar-day-events-by-city :day="day" :city="city"></calendar-day-events-by-city>
        </td>
    </tr>
    </tbody>
</template>

<script>
    import CalendarDayEventsByCity from "./CalendarDayEventsByCity";

    export default {
        name: "CalendarCitiesColumn",
        components: {CalendarDayEventsByCity},
        props: [
            'days'
        ],
        data() {
            return {
                cities: [],
            };
        },

        created() {
            this.getData();
        },

        methods: {
            getData(api_url) {
                api_url = api_url || '/api/cities';
                fetch(api_url)
                    .then(response => response.json())
                    .then(response => {
                        this.cities = response;
                    })
                    .catch(err => console.log(err));
            },
        }
    };</script>

<style scoped>

</style>