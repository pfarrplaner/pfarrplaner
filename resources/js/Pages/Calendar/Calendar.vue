<template>
    <admin-layout enable-control-sidebar="true" :title="title(date)" no-padding no-content-header :key="calendarState">
        <template #navbar-left>
            <calendar-nav-top :date="new Date(myDate)" :years="years" @collapseall="toggleCollapse"
                              :orientation="orientation" :targetMode="targetMode" :target="target"
                              :people-loaded="peopleLoaded"
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
        <div v-if="daysLoaded">
            <div class="d-none d-md-inline calendar-full-container">
                <calendar-pane-horizontal v-if="orientation == 'horizontal'"
                                          :date="myDate" :days="myDays" :cities="cityList" :services="services"
                                          :years="years"
                                          :key="collapseKey"
                                          :targetMode="targetMode" :target="target"
                                          :absences="absences" :can-create="canCreate"/>
                <calendar-pane-vertical v-else
                                        :date="myDate" :days="myDays" :cities="cityList" :services="services"
                                        :years="years"
                                        :key="collapseKey"
                                        :targetMode="targetMode" :target="target"
                                        :absences="absences" :can-create="canCreate "/>
            </div>
            <div class="d-inline d-md-none">
                <!-- mobile version -->
                <calendar-pane-mobile :date="myDate" :days="days" :cities="cityList" :services="services" :years="years"
                                      :absences="absences" :can-create="canCreate" :loading="loading"/>
            </div>
        </div>
        <div v-else class="month-loading">
            <div><span class="mdi mdi-spin mdi-loading"></span></div>
            <div>Kalender wird geladen</div>
        </div>
        <modal v-if="showTargetModeModal" title="Person(en) schnell eintragen"
               @close="setTarget" :key="peopleLoaded"
               close-button-label="Aktivieren"
               @cancel="showTargetModeModal = false; targetMode = false;">
            <div v-if="!peopleLoaded" class="text-small text-muted"><span class="mdi mdi-spin mdi-loading"></span>
                Personenliste wird geladen...
            </div>
            <div v-if="peopleLoaded" :key="myPeople.length">
                <people-select :people="myPeople" :teams="myTeams" v-model="target.people"
                               label="Folgende Person(en) eintragen" :key="myPeople.length"/>
            </div>
            <div v-if="!ministriesLoaded" class="text-small text-muted"><span class="mdi mdi-spin mdi-loading"></span>
                Dienste werden geladen...
            </div>
            <div v-if="ministriesLoaded" :key="myMinistries.length">
                <form-selectize label="Für folgenden Dienst eintragen" v-model="target.ministry" :options="myMinistries"
                                :key="myMinistries.length" />
            </div>
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
    props: ['date', 'days', 'cities', 'years', 'absences', 'canCreate', 'services', 'ministries'],
    provide() {
        return {
            settings: this.$page.props.settings || {},
        }
    },
    data() {
        return {
            apiToken: this.$page.props.currentUser.data.api_token,
            calendarState: Math.random().toString(36).substr(2, 9),
            myDate: this.date,
            myDays: this.initDays(this.days),
            daysLoaded: true,
            collapseKey: Math.random() * 9999999,
            cityList: this.cities,
            orientation: vm.$children[0].page.props.settings.calendar_view,
            settings: window.vm.$children[0].$page.props.settings || {},
            myServices: this.services,
            loading: 0,
            targetMode: false,
            showTargetModeModal: false,
            peopleLoaded: 0,
            myPeople: [],
            myTeams: [],
            ministriesLoaded: false,
            myMinistries: [],
            target: {
                people: [],
                ministry: this.$page.props.currentUser.data.isPastor ? 'P' : null,
                exclusive: false,
            }
        }
    },
    created() {
        if (undefined === this.settings.show_cc_details) this.settings.show_cc_details = 0;
        this.loadServicesByCityAndMonth();

        axios.get(route('api.people.select', {
            api_token: this.apiToken,
        })).then(response => {
            this.myPeople = response.data.users;
            this.myTeams = response.data.teams;
            this.target.people = this.myPeople.filter(person => person.id == this.$page.props.currentUser.data.id)
            this.peopleLoaded = 1;
        });

        axios.get(route('api.ministries.list', {
            api_token: this.apiToken,
        })).then(response => {
            this.myMinistries = [
                {id: 'P', 'name': 'Pfarrer*in'},
                {id: 'O', 'name': 'Organist*in'},
                {id: 'M', 'name': 'Mesner*in'},
            ];
            for (const ministryKey in response.data) {
                this.myMinistries.push({id: response.data[ministryKey].category, name: response.data[ministryKey].category});
            }
            this.ministriesLoaded = true;
        });


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
            this.daysLoaded = false;
            axios.get(route('api.calendar.navigate', {
                api_token: this.apiToken,
                date: targetDate,
            })).then(response => {
                this.myDays = this.initDays(response.data.days);
                this.absences = response.data.absences;
                this.daysLoaded = true;
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

.month-loading {
    font-size: 3em;
    font-width: bold;
    color: lightgray;
    text-align: center;
    padding-top: 25vh;
}
</style>
