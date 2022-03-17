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
    <admin-layout title="Bekanntgaben erstellen">
        <template v-slot:navbar-left>
            <nav-button type="primary" icon="mdi mdi-email-fast" title="Bekanntgaben erstellen"
                        @click="submitForm">Erstellen
            </nav-button>
        </template>
        <form method="post" :action="route('reports.render', {report: 'announcements'})" ref="myForm">
            <form-csrf-token/>
            <form-selectize label="Kirchengemeinde" name="city" v-model="myCity" :options="cities"/>
            <div v-if="(!servicesLoading) && (services.length > 0)">
                <form-selectize label="Bekanntgaben für den folgenden Gottesdienst erstellen"
                                name="service" :options="services" v-model="myService"/>
            </div>
            <div v-if="(!lastServiceLoading) && (myLastServiceDays.length > 0)">
                <form-selectize label="Herzlichen Dank für das Opfer der Gottesdienste vom..."
                                :key="lastServiceDaysLoaded"
                                name="lastService" :options="myLastServiceDays" v-model="myLastServiceDay"/>
            </div>
            <div v-if="(!amountLoading)">
                <form-input label="... in Höhe von ..."
                            :key="amountLoaded"
                            name="offerings" v-model="amount"/>

                <form-textarea name="offering_text"
                               label="Text zum Opfer" placeholder="Wenn vorhanden, z.B. Brief des Landesbischofs"/>
            </div>
            <form-check name="mix_outlook" label="Veranstaltungen aus dem Outlook-Kalender mit aufnehmen."
                        v-model="mixOutlook" :key="servicesLoaded"/>
            <form-check name="mix_op" label="Veranstaltungen aus dem Online-Planer mit aufnehmen."
                        v-model="mixOP" :key="servicesLoaded"/>
        </form>
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
import FormCheck from "../../../components/Ui/forms/FormCheck";
import FormCsrfToken from "../../../components/Ui/forms/FormCsrfToken";

export default {
    name: "Setup",
    props: ['cities'],
    components: {
        FormCsrfToken,
        FormCheck,
        NavButton,
        FormTextarea,
        FormSelectize, FormInput
    },
    async mounted() {
        await this.getServices();
        await this.getLastServiceDays();
        await this.getAmount();
    },
    data() {
        return {
            apiToken: this.$page.props.currentUser.data.api_token,
            myCity: this.cities.length ? this.cities[0].id : null,
            servicesLoading: false,
            servicesLoaded: 0,
            lastServiceLoading: false,
            services: [],
            myService: null,
            myLastServiceDays: [],
            myLastServiceDay: null,
            lastServiceDaysLoaded: 1,
            amountLoading: false,
            amountLoaded: 1,
            amount: '',
            mixOutlook: false,
            mixOP: false,
        }
    },
    watch: {
        async myCity() {
            await this.getServices();
        },
        async myService() {
            await this.getLastServiceDays();
        },
        async myLastServiceDay() {
            await this.getAmount();
        },
    },
    methods: {
        queryDate(d) {
            return typeof d == 'object' ? d.format('YYYY-MM-DD') : moment(d, 'DD.MM.YYYY').format('YYYY-MM-DD');
        },
        async getServices() {
            if (!this.myCity) return [];
            this.servicesLoading = true;
            this.services = await axios.post(route('api.report.step', {
                api_token: this.apiToken,
                report: 'announcements',
                step: 'services',
                city: this.myCity,
            })).then(response => {
                this.servicesLoading = false;
                this.servicesLoaded++;
                this.mixOutlook = response.data.mixOutlook;
                this.mixOP = response.data.mixOP;
                return response.data.services;
            });
            if (this.services.length) this.myService = this.services[0].id;
        },
        async getLastServiceDays() {
            if ((!this.myCity) || (!this.myService)) return [];
            this.lastServiceLoading = true;
            this.myLastServiceDays = await axios.post(route('api.report.step', {
                api_token: this.apiToken,
                report: 'announcements',
                step: 'lastServiceDays',
                city: this.myCity,
                service: this.myService,
            })).then(response => {
                this.lastServiceLoading = false;
                this.lastServiceDaysLoaded++;
                return response.data;
            });
            if (this.myLastServiceDays.length) this.myLastServiceDay = this.myLastServiceDays[0].id;
        },
        async getAmount() {
            if ((!this.myCity) || (!this.myLastServiceDay)) return [];
            this.amountLoading = true;
            this.amount = await axios.post(route('api.report.step', {
                api_token: this.apiToken,
                report: 'announcements',
                step: 'offerings',
                city: this.myCity,
                day: this.myLastServiceDay,
            })).then(response => {
                this.amountLoading = false;
                this.amountLoaded++;
                return (new Intl.NumberFormat('de-DE', {style: 'currency', currency: 'EUR'}).format(response.data));
            });
            if (this.myLastServiceDays.length) this.myLastServiceDay = this.myLastServiceDays[0].id;
        },
        setLocation(e) {
            this.myLocations = e;
        },
        submitForm() {
            this.$refs.myForm.submit();
        },
    }
}
</script>

<style scoped>

</style>
