<template>
    <div class="calendar-month calendar-vertical">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th class="no-print text-left city-title"><!-- // TODO: slave mode --></th>
                <th v-for="city in cities" class="city-title">
                    <span class="mdi mdi-arrow-down-circle pr-2"></span>
                    {{ city.name }}
                    <nav-button class="btn-xs" type="success" force-no-text force-icon
                                icon="mdi mdi-plus" :title="'Gottesdienst für '+city.name+' hinzufügen'"
                                @click="addService(city)" />
                </th>
            </tr>
            </thead>
            <tbody>
                <tr v-for="day,index in myDays">
                    <calendar-day-header
                        :day="day"
                        :key="day.id"
                        :scroll-to-date="scrollToDate"
                        :absences="absences[day.id]"  />
                    <calendar-cell v-for="(city,index) in cities" :day="day" :key="city.id"
                                   :services="getServices(city,day)" :city="city" :can-create="canCreate"
                                    />
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>

import NavButton from "../../Ui/buttons/NavButton";
export default {
    components: {NavButton},
    props: ['date', 'days', 'cities', 'services', 'years', 'absences', 'canCreate', 'collapseState'],
    data() {
        var myDays = this.days;
        var scrollToDate = null;

        for (let dayId in myDays) {
            myDays[dayId].index = dayId;
            if (moment(myDays[dayId].date) <= moment()) scrollToDate = myDays[dayId].date;

        }

        return {
            myDays: myDays,
            scrollToDate,
        }
    },
    methods: {
        title: function (d) {
            return moment(d).locale('de-DE').format('MMMM YYYY');
        },
        getServices(city, day) {
            if (this.services[city.id] == undefined) return [];
            if (this.services[city.id][day.id] == undefined) return [];
            return this.services[city.id][day.id];
        },
        addService(city) {
            this.$inertia.get(route('service.create', {city: city.id, date: moment(this.date).format('YYYY-MM-DD')}));
        }
    }
}
</script>
<style scoped>
    .city-title {
        position: sticky;
        top: 58px;
        background-color: #f4f6f9;
    }

    .btn-xs {
        padding: 0.75em 1em;
    }

</style>
