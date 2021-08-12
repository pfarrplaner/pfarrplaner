<template>
    <div class="calendar-month calendar-vertical">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th class="no-print text-left city-title"><!-- // TODO: slave mode --></th>
                <th v-for="city in cities" class="city-title"><span class="fa fa-arrow-alt-circle-down pr-2"></span>{{ city.name }}</th>
            </tr>
            </thead>
            <tbody>
                <tr v-for="day,index in myDays">
                    <calendar-day-header
                        :day="day"
                        :key="day.id"
                        :scroll-to-date="scrollToDate"
                        :absences="absences[day.id]" @collapse="changeCollapseState" />
                    <calendar-cell v-for="(city,index) in cities" :day="day" :key="city.id"
                                   :services="getServices(city,day)" :city="city" :can-create="canCreate"
                                   @collapse="changeCollapseState" />
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>

export default {
    props: ['date', 'days', 'cities', 'services', 'years', 'absences', 'canCreate', 'collapseState'],
    data() {
        var myDays = this.days;
        var scrollToDate = null;

        myDays.forEach((day,dayId) => {
            myDays[dayId].collapsed = (day.day_type == 1 ? (this.collapseState == null ? true: !this.collapseState) : false);
            myDays[dayId].index = dayId;
            myDays[dayId].hasMine = false;
            myDays[dayId].initalized = false;

            if (moment(day.date) <= moment()) scrollToDate = day.date;
        });

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
            if (this.collapseState == null) {
                if (this.services[city.id][day.id].length > 0) {
                    var hasMine = false;
                    this.services[city.id][day.id].forEach(service => {
                        hasMine = hasMine || service.isMine;
                    });
                    this.myDays.forEach((aDay,aDayIndex) => {
                        if (aDay.id == day.id) {
                            this.myDays[aDayIndex].hasMine = true;
                            if ((!aDay.initialized) && (aDay.day_type ==1)) {
                                this.myDays[aDayIndex].collapsed = false;
                                this.myDays[aDayIndex].initialized = true;
                            }
                        }
                    });
                }
            }
            return this.services[city.id][day.id];
        },
        changeCollapseState(e) {
            this.myDays[e.day.index].collapsed = e.state;
            this.$forceUpdate();
        },
    }
}
</script>
<style scoped>
    .city-title {
        position: sticky;
        top: 58px;
        background-color: #f4f6f9;
    }
</style>
