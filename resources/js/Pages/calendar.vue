<template>
    <div>
        <h1>Kalender für {{ date | calendarTitle }}</h1>
        <table border="1">
            <thead>
                <tr>
                    <th></th>
                    <th v-for="day in days" v-bind="day.id">
                        {{ day.date }}
                    </th>
                </tr>
            </thead>
            <tr v-for="city in cities" v-bind="city.id">
                <td>{{ city.name }}</td>
                <th v-for="day in days" v-bind="day.id">
                </th>
            </tr>
        </table>
    </div>
</template>

<script>
export default {
    props: ['date', 'days', 'cities']
}
</script>
