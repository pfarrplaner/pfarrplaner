<template>
    <div class="calendar-month calendar-horizontal">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th class="no-print text-left"></th>
                <calendar-day-header
                    v-for="(day,index) in myDays"
                    :day="day"
                    :key="'_day_'+index"
                    :index="index"
                    :absences="absences[day.id]"
                />
            </tr>
            </thead>
            <tbody>
                <tr v-for="city in cities">
                    <th class="city-title">
                        <nav-button class="btn-xs mt-3" type="success" force-no-text force-icon
                                    icon="mdi mdi-plus" :title="'Gottesdienst für '+city.name+' hinzufügen'"
                                    @click="addService(city)" />
                        <span class="pt-2">{{ city.name }}</span><span class="mdi mdi-arrow-down-circle pt-2"></span>
                    </th>
                    <calendar-cell v-for="(day,index) in myDays" :day="day"
                                   :targetMode="targetMode" :target="target"
                                   :key="city.loading+'_'+day.date+'_'+city.id"
                                   :index="index" :services="getServices(city,day)"
                                   :city="city" :can-create="canCreate" />
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>

import NavButton from "../../Ui/buttons/NavButton";
export default {
    components: {NavButton},
    props: ['date', 'days', 'cities', 'services', 'years', 'absences', 'canCreate','collapseState', 'targetMode', 'target'],
    data() {
        var myDays = this.days;

        for (let dayId in myDays) {
            myDays[dayId].index = dayId;
        }
        return {
            myDays: myDays,
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
            this.$inertia.get(route('service.create', {city: city.id, date: this.date}));
        }
    },
    computed: {
        serviceCount() {
            return this.services.length;
        }
    }
}
</script>

<style scoped>
    table.table thead th {
        vertical-align: top;
    }

    .city-title {
        writing-mode: sideways-lr;
        padding: 1px;
    }

    .btn-xs.mt-3 {
        padding: 1em 0.75em;
    }

</style>
