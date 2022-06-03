<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2022 Christoph Fischer, https://christoph-fischer.org
  - @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
  - @link https://github.com/pfarrplaner/pfarrplaner
  - @version git: $Id$
  -
  - Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
  -
  - Pfarrplaner is based on the Laravel framework (https://laravel.com).
  - This file may contain code created by Laravel's scaffolding functions.
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU General Public License as published by
  - the Free Software Foundation, either version 3 of the License, or
  - (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU General Public License for more details.
  -
  - You should have received a copy of the GNU General Public License
  - along with this program.  If not, see <http://www.gnu.org/licenses/>.
  -->

<template>
    <admin-layout title="Dienstanfrage senden">
        <template v-slot:navbar-left>
            <nav-button type="primary" icon="mdi mdi-email-fast" title="Anfrage absenden"
                        :disabled="(selectedServices.length == 0) && (recipients.length == 0)"
                        @click="sendMessage">Senden</nav-button>
        </template>
        <div class="row">
            <div class="col-md-6">
                <form-selectize label="Kirchengemeinde" name="city" v-model="myCity"
                                :options="cities" />
            </div>
            <div class="col-md-6">
                <location-select name="locations" :locations="locations" label="Auf folgende Orte beschränken"
                                 v-model="myLocations" multiple @set-location="setLocation" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <form-date-picker v-model="from" name="start" label="Gottesdienste von" />
            </div>
            <div class="col-md-6">
                <form-date-picker v-model="to" name="end" label="Bis" />
            </div>
        </div>
        <form-selectize label="Anfrage für folgenden Dienst senden"
                        v-model="myMinistry" name="ministry"
                        :options="myMinistries" />

        <tab-headers>
            <tab-header :active-tab="activeTab" id="services" title="Gottesdienste" :count="selectedServices.length"/>
            <tab-header :active-tab="activeTab" id="recipients" title="Empfänger" :count="recipients.length"/>
        </tab-headers>
        <tabs>
            <tab :active-tab="activeTab" id="services">
                <div v-if="servicesLoading"><span class="mdi mdi-spin mdi-loading"></span> Gottesdienstliste wird geladen...</div>
                <fake-table v-if="services.length > 0"
                            collapsed-header="Folgende Gottesdienste anfragen"
                            :columns="[1,6,5]" :headers="['', 'Zeit', 'Ort']">
                    <div v-for="service in services" class="row py-1">
                        <div class="col-md-1">
                            <input type="checkbox" v-model="service.checked">
                        </div>
                        <div class="col-md-6">
                            {{ moment(service.date).locale('de').format('LLLL') }}<br />
                            {{ service.titleText }}
                        </div>
                        <div class="col-md-5">
                            {{ service.locationText }}
                        </div>
                    </div>
                </fake-table>
            </tab>
            <tab :active-tab="activeTab" id="recipients">
                <div v-if="usersLoading"><span class="mdi mdi-spin mdi-loading"></span> Mitarbeiterliste wird geladen...</div>
                <div v-if="(!usersLoading)">
                    <form-selectize label="Empfänger" :options="users" v-model="recipients" multiple :key="usersLoaded"/>
                    <form-textarea name="message" label="Zusätzliche Nachricht" v-model="message" />
                </div>
            </tab>
        </tabs>
    </admin-layout>

</template>

<script>

import FormInput from "../../../components/Ui/forms/FormInput";
import FormSelectize from "../../../components/Ui/forms/FormSelectize";
import FormDatePicker from "../../../components/Ui/forms/FormDatePicker";
import LocationSelect from "../../../components/Ui/elements/LocationSelect";
import FakeTable from "../../../components/Ui/FakeTable";
import PeopleSelect from "../../../components/Ui/elements/PeopleSelect";
import TabHeaders from "../../../components/Ui/tabs/tabHeaders";
import TabHeader from "../../../components/Ui/tabs/tabHeader";
import Tabs from "../../../components/Ui/tabs/tabs";
import Tab from "../../../components/Ui/tabs/tab";
import FormTextarea from "../../../components/Ui/forms/FormTextarea";
import NavButton from "../../../components/Ui/buttons/NavButton";
export default {
    name: "Setup",
    props: ['cities', 'ministries', 'locations', 'users'],
    components: {
        NavButton,
        FormTextarea,
        Tab,
        Tabs,
        TabHeader,
        TabHeaders, PeopleSelect, FakeTable, LocationSelect, FormDatePicker, FormSelectize, FormInput},
    computed: {
        selectedServices() {
            let s = [];
            for (let key in this.services) {
                if (this.services[key].checked) s.push(this.services[key].id);
            }
            return s;
        },
    },
    mounted() {
        this.getServices();
        this.getRecipients();
    },
    data() {
        let myMinistries = [];
        for (let id in this.ministries) {
            myMinistries.push({ id: this.ministries[id], name: this.ministries[id] });
        }

        return {
            from: moment().date(1).add(1, "month"),
            to: moment().date(1).add(2, "month").subtract(1, 'day'),
            myMinistries,
            myMinistry: null,
            myLocations: [],
            myCity: (this.cities.length ? this.cities[0].id : null),
            services: [],
            apiToken: this.$page.props.currentUser.data.api_token,
            usersLoading: false,
            usersLoaded: 0,
            servicesLoading: false,
            recipients: [],
            emailAddresses: {},
            activeTab: 'services',
            message: '',
        }
    },
    watch: {
        async from() {
            await this.getServices();
        },
        async to() {
            await this.getServices();
        },
        async myCity() {
            await this.getServices();
            await this.getRecipients();
        },
        async myLocations() {
            await this.getServices();
        },
        async myMinistry() {
            await this.getRecipients();
        }
    },
    methods: {
        queryDate(d) {
            return typeof d == 'object' ? d.format('YYYY-MM-DD') : moment(d, 'DD.MM.YYYY').format('YYYY-MM-DD');
        },
        async getServices() {
            if (!(this.myCity && this.from && this.to)) return [];
            this.servicesLoading = true;
            this.services = await axios.post(route('api.report.step', {
                api_token:this.apiToken,
                report: 'ministryRequest',
                step: 'services',
                city: this.myCity,
                locations: this.myLocations,
                start: this.queryDate(this.from),
                end: this.queryDate(this.to),
            })).then(response => {
                for (let key in response.data) {
                    response.data[key]['checked'] = true;
                }
                this.servicesLoading = false;
                return response.data;
            });
        },
        async getRecipients() {
            if (!(this.myCity && this.myMinistry)) return [];
            this.usersLoading = true;
            this.$forceUpdate();
            this.recipients = await axios.post(route('api.report.step', {
                api_token:this.apiToken,
                report: 'ministryRequest',
                step: 'recipients',
                city: this.myCity,
                ministry: this.myMinistry,
            })).then(response => {
                this.usersLoading = false;
                this.usersLoaded++;
                this.$forceUpdate();
                return response.data;
            });
            this.usersLoaded++;
            this.$forceUpdate();
        },
        setLocation(e) {
            this.myLocations = e;
        },
        sendMessage() {
            let record = {
                services : [],
                recipients: this.recipients,
                address : {},
                ministry: this.myMinistry,
                text: this.message,
            };
            this.services.forEach(service => {
                if (service.checked) record.services.push(service.id)
            });
            this.recipients.forEach(recipient => {
                let user = this.users.filter(u => {
                    return u.id === recipient;
                })[0];
                if (user) {
                    if (!user.email) {
                        let address = window.prompt('Bitte gib eine E-Mailadresse für '+user.name+' an.');
                        if (address) {
                            record.address[recipient] = address;
                        }
                    } else {
                        record.address[recipient] = user.email;
                    }
                }
            });
            this.$inertia.post(route('reports.render', {report: 'ministryRequest'}), record);
        },
    }
}
</script>

<style scoped>

</style>
