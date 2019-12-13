<template>
    <div v-if="loading"><span class="fa fa-spin fa-spinner"></span></div>
    <div v-else>
        <calendar-service v-for="service in services" v-bind:key="service" :serviceId="service"></calendar-service>
        <a class="btn btn-success btn-sm btn-add-day" title="Neuen Gottesdiensteintrag hinzufügen"
           :href="serviceAddRoute()"><span class="fa fa-plus"></span></a>

    </div>
</template>

<script>
    import CalendarDayEventsByCity from "./CalendarDayEventsByCity";
    import CalendarService from "./CalendarService";
    export default {
        name: "CalendarDayEventsByCity",
        components: {CalendarService},
        props: [
            'day',
            'city'
        ],
        data() {
            return {
                services: [],
                loading: true,
            };
        },

        created() {
            this.getData();
        },

        methods: {
            getData(api_url) {
                api_url = api_url || '/api/servicesByDayAndCity/'+this.day.id+'/'+this.city.id;
                fetch(api_url)
                    .then(response => response.json())
                    .then(response => {
                        this.services = response;
                        this.loading = false;
                    })
                    .catch(err => console.log(err));
            },
            serviceAddRoute: function() {
                return '/services/add/'+this.day.id+'/'+this.city.id;
            }
        }
    };</script>

<style scoped>

</style>