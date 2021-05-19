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
                <tr v-for="day,index in myDays">
                    <calendar-day-header
                        :day="day"
                        :key="day.id"
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

        myDays.forEach((day,dayId) => {
            myDays[dayId].collapsed = (day.day_type == 1 ? (this.collapseState == null ? true: !this.collapseState) : false);
            myDays[dayId].index = dayId;
            myDays[dayId].hasMine = false;
            myDays[dayId].initalized = false;
        });

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
            console.log('changeCollapseState', e);
            this.myDays[e.day.index].collapsed = e.state;
            this.$forceUpdate();
        },
    }
}
</script>
