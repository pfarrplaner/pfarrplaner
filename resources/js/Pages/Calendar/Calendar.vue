<template>
    <admin-layout enable-control-sidebar="true" :title="title(date)" no-padding no-content-header>
        <template #navbar-left>
            <calendar-nav-top :date="new Date(date)" :years="years" @collapseall="toggleCollapse"
                              :orientation="orientation" :targetMode="targetMode" :target="target"
                              @toggle-target-mode="toggleTargetMode"
                              @navigateTo="navigateTo"
            />
        </template>
        <template #control-sidebar>
            <calendar-nav-control-sidebar :date="new Date(date)" :cities="cities" @set-setting="setSetting"/>
        </template>
        <template slot="after-flash">
            <div class="alert alert-info" v-if="loading > 0"><span class="mdi mdi-spin mdi-loading"></span> Daten für {{ loading }} Orte werden geladen ...</div>
        </template>
        <div class="d-none d-md-inline calendar-full-container">
            <calendar-pane-horizontal v-if="orientation == 'horizontal'"
                                      :date="date" :days="myDays" :cities="cityList" :services="services" :years="years"
                                      :key="collapseKey"
                                      :targetMode="targetMode" :target="target"
                                      :absences="absences" :can-create="canCreate"/>
            <calendar-pane-vertical v-else
                                    :date="date" :days="myDays" :cities="cityList" :services="services" :years="years"
                                    :key="collapseKey"
                                    :targetMode="targetMode" :target="target"
                                    :absences="absences" :can-create="canCreate "/>
        </div>
        <div class="d-inline d-md-none">
            <!-- mobile version -->
            <calendar-pane-mobile :date="date" :days="days" :cities="cityList" :services="services" :years="years"
                                    :absences="absences" :can-create="canCreate" :loading="loading"/>
        </div>
        <modal v-if="showTargetModeModal" title="Person(en) schnell eintragen"
               @close="setTarget"
               close-button-label="Aktivieren"
               @cancel="showTargetModeModal = false; targetMode = false;">
            <people-select :people="people" v-model="target.people" label="Folgende Person(en) eintragen" />
            <form-selectize label="Für folgenden Dienst eintragen" v-model="target.ministry" :options="myMinistries" />
            <form-check label="Bestehende Einträge überschreiben" v-model="target.exclusive" />
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
    props: ['date', 'days', 'cities',  'years', 'absences', 'canCreate', 'services', 'people', 'ministries'],
    provide() {
        return {
            settings: this.$page.props.settings || {},
        }
    },
    data() {
        var myDays = {};

        for (let dayIndex in this.days) {
            myDays[this.days[dayIndex].date] = {
                ...this.days[dayIndex],
                id: this.days[dayIndex].date,
                hasMine: false,
            }
        }

        var myMinistries = [
            { id: 'P', 'name': 'Pfarrer*in'},
            { id: 'O', 'name': 'Organist*in'},
            { id: 'M', 'name': 'Mesner*in'},
        ];
        for(const ministryKey in this.ministries) {
            myMinistries.push({id: this.ministries[ministryKey], name: this.ministries[ministryKey]});
        }

        return {
            myDays: myDays,
            collapseKey: Math.random()*9999999,
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
        this.cities.forEach(city => {
            this.loading++;
            city['loading'] = city.id;
            axios.get(route('cal.city', {date: moment(this.date).format('YYYY-MM'), city: city.id}))
            .then(response => {
                city['loading'] = false;
                this.myServices[city.id] = response.data;
                this.loading--;
                this.$forceUpdate();
            });
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
            this.myDays.forEach((day,dayIndex) => {
                this.myDays[dayIndex].collapsed = e;
            });
            this.collapseState = e;
            this.collapseKey = Math.random()*9999999;
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
        navigateTo(targetDate) {

        }
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
