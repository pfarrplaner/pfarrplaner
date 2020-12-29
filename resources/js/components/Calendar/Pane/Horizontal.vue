<template>
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
                    :absences="absences[day.id]"/>
            </tr>
            </thead>
            <tbody>
                <tr v-for="city in cities">
                    <th>{{ city.name }}</th>
                    <calendar-cell v-for="(day,index) in days" :day="day" :key="day.id" :index="index" :services="services[city.id][day.id]" :city="city" :can-create="canCreate"/>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>

export default {
    props: ['date', 'days', 'cities', 'services', 'years', 'absences', 'canCreate'],
    methods: {
        title: function (d) {
            return moment(d).locale('de-DE').format('MMMM YYYY');
        },
    }
}
</script>
