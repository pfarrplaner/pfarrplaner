<template>
    <div class="calendar-month calendar-horizontal">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th class="no-print text-left"></th>
                <calendar-day-header
                    v-for="(day,index) in myDays"
                    :day="day"
                    :key="'day_'+day.id+'_'+(day.collapsed ? 'closed' : 'open')"
                    :index="index"
                    :absences="absences[day.id]"
                    @collapse="changeCollapseState"
                />
            </tr>
            </thead>
            <tbody>
                <tr v-for="city in cities">
                    <th class="city-title"><span class="pt-2">{{ city.name }}</span><span class="mdi mdi-arrow-down-circle pt-2"></span></th>
                    <calendar-cell v-for="(day,index) in myDays" :day="day"
                                   :key="'day_'+day.id+'_'+(day.collapsed ? 'closed' : 'open')"
                                   :index="index" :services="getServices(city,day)"
                                   :city="city" :can-create="canCreate"
                                   @collapse="changeCollapseState"
                    />
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>

export default {
    props: ['date', 'days', 'cities', 'services', 'years', 'absences', 'canCreate','collapseState'],
    data() {
        var myDays = this.days;

        myDays.forEach((day,dayId) => {
            myDays[dayId].collapsed = (day.day_type == 1 ? (this.collapseState == null ? true: !this.collapseState) : false);
            myDays[dayId].index = dayId;
            myDays[dayId].hasMine = false;
            myDays[dayId].initialized = false;
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
            this.myDays[e.day.index].collapsed = e.state;
            this.$forceUpdate();
        },
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

</style>
