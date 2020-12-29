<template>
    <div class="calendar-month calendar-vertical">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th class="no-print text-left">Kirchengemeinde<!-- // TODO: slave mode --></th>
                <th v-for="city in cities">{{ city.name }}</th>
            </tr>
            </thead>
            <tbody>
                <tr v-for="day,index in days">
                    <calendar-day-header
                        :day="day"
                        :key="day.id"
                        :absences="absences[day.id]"
                    />
                    <calendar-cell v-for="(city,index) in cities" :day="day" :key="city.id" :services="services[city.id][day.id]" :city="city" :can-create="canCreate"/>
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
