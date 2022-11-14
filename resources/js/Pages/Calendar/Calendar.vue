<template>
    <admin-layout enable-control-sidebar="true" :title="title(date)" no-padding no-content-header :key="calendarState">
        <template #navbar-left>
            <calendar-nav-top :date="new Date(myDate)" :years="years" @collapseall="toggleCollapse"
                              :orientation="orientation" :targetMode="targetMode" :target="target"
                              @toggle-target-mode="toggleTargetMode"
                              @navigate="navigateTo"
            />
        </template>
        <template #control-sidebar>
            <calendar-nav-control-sidebar :date="new Date(date)" :cities="cities" @set-setting="setSetting"/>
        </template>
        <template slot="after-flash">
            <div class="alert alert-info" v-if="loading > 0"><span class="mdi mdi-spin mdi-loading"></span> Daten für
                {{ loading }} Orte werden geladen ...
            </div>
        </template>
        <div class="d-none d-md-inline calendar-full-container">
            <calendar-pane-horizontal v-if="orientation == 'horizontal'"
                                      :date="myDate" :days="myDays" :cities="cityList" :services="services"
                                      :years="years"
                                      :key="collapseKey"
                                      :targetMode="targetMode" :target="target"
                                      :absences="absences" :can-create="canCreate"/>
            <calendar-pane-vertical v-else
                                    :date="myDate" :days="myDays" :cities="cityList" :services="services" :years="years"
                                    :key="collapseKey"
                                    :targetMode="targetMode" :target="target"
                                    :absences="absences" :can-create="canCreate "/>
        </div>
        <div class="d-inline d-md-none">
            <!-- mobile version -->
            <calendar-pane-mobile :date="myDate" :days="days" :cities="cityList" :services="services" :years="years"
                                  :absences="absences" :can-create="canCreate" :loading="loading"/>
        </div>
        <modal v-if="showTargetModeModal" title="Person(en) schnell eintragen"
               @close="setTarget"
               close-button-label="Aktivieren"
               @cancel="showTargetModeModal = false; targetMode = false;">
            <people-select :people="people" v-model="target.people" label="Folgende Person(en) eintragen"/>
            <form-selectize label="Für folgenden Dienst eintragen" v-model="target.ministry" :options="myMinistries"/>
            <form-check label="Bestehende Einträge überschreiben" v-model="target.exclusive"/>
        </modal>
    </admin-layout>
</template>

<script>
import moment from 'moment';
import EventBus from "../../plugins/EventBus";
import {CalendarNewSortOrderEvent} from "../../events/CalendarNewSortOrderEvent";
import {CalendarNewOrientationEvent} from "../../events/CalendarNewOrientationEvent";
import CalendarPaneMobile from "../../components/Calendar/Pane/Mobile";
import Modal from "../../components/Ui/modals/Modal";
import PeopleSelect from "../../components/Ui/elements/PeopleSelect";
import FormSelectize from "../../components/Ui/forms/FormSelectize";
import FormCheck from "../../components/Ui/forms/FormCheck";

export default {
    components: {FormCheck, FormSelectize, PeopleSelect, Modal, CalendarPaneMobile},
    props: ['date', 'days', 'cities', 'years', 'absences', 'canCreate', 'services', 'people', 'ministries'],
    provide() {
        return {
            settings: this.$page.props.settings || {},
        }
    },
    data() {
        var myMinistries = [
            {id: 'P', 'name': 'Pfarrer*in'},
            {id: 'O', 'name': 'Organist*in'},
            {id: 'M', 'name': 'Mesner*in'},
        ];
        for (const ministryKey in this.ministries) {
            myMinistries.push({id: this.ministries[ministryKey], name: this.ministries[ministryKey]});
        }

        return {
            apiToken: this.$page.props.currentUser.data.api_token,
            calendarState: Math.random().toString(36).substr(2, 9),
            myDate: this.date,
            myDays: this.initDays(this.days),
            collapseKey: Math.random() * 9999999,
            cityList: this.cities,
            orientation: vm.$children[0].page.props.settings.calendar_view,
            settings: window.vm.$children[0].$page.props.settings || {},
            myServices: this.services,
            loading: 0,
            targetMode: false,
            showTargetModeModal: false,
            myMinistries,
            target: {
                people: this.people.filter(person => person.id == this.$page.props.currentUser.data.id),
                ministry: this.$page.props.currentUser.data.isPastor ? 'P' : null,
                exclusive: false,
            }
        }
    },
    created() {
        if (undefined === this.settings.show_cc_details) this.settings.show_cc_details = 0;
        this.loadServicesByCityAndMonth();
    },
    mounted() {
        EventBus.listen(CalendarNewSortOrderEvent, this.sortHandler);
        EventBus.listen(CalendarNewOrientationEvent, this.orientationHandler);
    },
    methods: {
        title: function (d) {
            return moment(d).locale('de-DE').format('MMMM YYYY');
        },
        sortHandler: function (e) {
            this.cityList = e.list;
        },
        orientationHandler: function (e) {
            this.orientation = e.orientation;
        },
        toggleCollapse(e) {
            this.myDays.forEach((day, dayIndex) => {
                this.myDays[dayIndex].collapsed = e;
            });
            this.collapseState = e;
            this.collapseKey = Math.random() * 9999999;
        },
        setSetting(setting) {
            this.settings[setting.key] = setting.value;
            this.$forceUpdate();
        },
        toggleTargetMode(e) {
            if (!e) {
                this.targetMode = false;
                return;
            }
            this.showTargetModeModal = true;
        },
        setTarget() {
            this.showTargetModeModal = false;
            this.targetMode = true;
        },
        initDays(dayData) {
            var myDays = {};

            for (let dayIndex in dayData) {
                myDays[dayData[dayIndex].date] = {
                    ...dayData[dayIndex],
                    id: dayData[dayIndex].date,
                    hasMine: false,
                }
            }
            return myDays;
        },
        loadServicesByCityAndMonth() {
            this.cities.forEach(city => {
                this.loading++;
                city['loading'] = city.id;
                axios.get(route('api.calendar.byCityAndMonth', {
                    api_token: this.apiToken,
                    city: city.id,
                    date: moment(this.myDate).format('YYYY-MM'),
                })).then(response => {
                    city['loading'] = false;
                    this.myServices[city.id] = response.data;
                    this.loading--;
                    this.$forceUpdate();
                });
            });
        },
        navigateTo(targetDate) {
            this.myDate = targetDate;
            axios.get(route('api.calendar.navigate', {
                api_token: this.apiToken,
                date: targetDate,
            })).then(response => {
                console.log(response.data);
                this.myDays = this.initDays(response.data.days);
                this.changeState();
                this.loadServicesByCityAndMonth();
            });
        },
        changeState() {
            this.calendarState = Math.random().toString(36).substr(2, 9);
        },
    },
    computed: {
        storedDays() {
            return this.$store.days;
        }
    }
}
</script>
<style scoped>
th, td {
    vertical-align: top;
}

.calendar-full-container {
    font-size: .9em;
}
</style>
