<template>
    <main class="py-1">
        <div class="calendar-month">

            <h1 class="print-only">Bla</h1>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th class="no-print">Kirchengemeinde</th>
                    <th v-for="day in days"
                        v-bind:key="day.id"
                        v-html="formatDate(day.date)"
                        :class="renderClassAttribute(day)"
                        :title="renderDayTitle(day)"
                        :data-day="day.id"
                    ></th>
                </tr>
                </thead>
                <calendar-cities-column :days="days"></calendar-cities-column>
            </table>
        <hr/>
        </div>
    </main>

</template>

<script>
    export default {
        name: "CalendarMonth",
        props: [
            'year',
            'month',
        ],
        data() {
            return {
                days: [],
            };
        },

        created() {
            this.getData();
        },

        methods: {
            getData(api_url) {
                api_url = api_url || '/api/calendar/month/'+this.year+'/'+this.month;
                fetch(api_url)
                    .then(response => response.json())
                    .then(response => {
                        this.days = response.days;
                    })
                    .catch(err => console.log(err));
            },
            formatDate: function(dateString) {
                var m = moment(dateString);
                if (m.day() > 0) {
                    return '<span class="special-weekday">'+m.format('dd')+'.</span>, '+m.format('DD.MM.YYYY');
                } else {
                    return m.format('DD.MM.YYYY');
                }
            },
            renderClassAttribute: function(day) {
                return 'hide_buttons '
                    + (day.day_type == 1 ? 'limited collapsed ' : '');
            },
            renderDayTitle: function(day) {
                return (day.day_type == 1 ? moment(day.date).format('DD.MM.YYYY')+ ' (Klicken, um Ansicht umzuschalten)' : '');
            }
        }
    };
</script>

<style scoped>

</style>