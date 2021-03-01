<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
  - @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
  - @link https://github.com/potofcoffee/pfarrplaner
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
    <div class="service-editor">
        <admin-layout title="Gottesdienst bearbeiten">
            <template slot="navbar-left">
                <button class="btn btn-primary" @click.prevent="saveService"><span class="fa fa-save d-md-none"></span><span class="d-none d-md-inline"> Speichern</span></button>&nbsp;
                <a class="btn btn-light" target="_blank" :href="route('services.liturgy.editor', service.id)" title="Liturgie zu diesem Gottesdienst bearbeiten"><span class="fa fa-th-list"></span><span class="d-none d-md-inline"> Liturgie</span></a>&nbsp;
                <a class="btn btn-light" target="_blank" :href="route('services.sermon.editor', service.id)"  title="Predigt zu diesem Gottesdienst bearbeiten"><span class="fa fa-microphone"></span><span class="d-none d-md-inline"> Predigt</span></a>&nbsp;
            </template>
            <form @submit.prevent="saveService" id="formSermon">
                <card>
                    <card-header>
                        <tab-headers>
                            <tab-header id="home" title="Allgemeines" :active-tab="activeTab" />
                            <tab-header id="special" title="Besonderheiten" :active-tab="activeTab" />
                            <tab-header id="offerings" title="Opfer" :active-tab="activeTab" />
                            <tab-header id="rites" title="Kasualien" :active-tab="activeTab" :count="service.funerals.length+service.baptisms.length+service.weddings.length"/>
                            <tab-header id="cc" title="Kinderkirche" :active-tab="activeTab" />
                            <tab-header id="streaming" title="Streaming" :active-tab="activeTab" />
                            <tab-header id="konfiapp" title="KonfiApp" :active-tab="activeTab" />
                            <tab-header id="registrations" title="Anmeldungen" :active-tab="activeTab"  :count="service.bookings.length"/>
                            <tab-header id="attachments" title="Dateien" :active-tab="activeTab" :count="service.attachments.length"/>
                            <tab-header id="comments" title="Kommentare" :active-tab="activeTab" :count="service.comments.length"/>
                        </tab-headers>
                    </card-header>
                    <card-body>
                        <tabs>
                            <tab id="home" :active-tab="activeTab">
                                <home-tab :service="editedService" :locations="locations"
                                          :days="days" :people="users" :ministries="ministries"/>
                            </tab>
                            <tab id="special" :active-tab="activeTab">
                                <special-tab :service="service" :tags="tags" :service-groups="serviceGroups"/>
                            </tab>
                            <tab id="offerings" :active-tab="activeTab">
                                <offerings-tab :service="service" />
                            </tab>
                            <tab id="cc" :active-tab="activeTab">
                                <c-c-tab :service="service" />
                            </tab>
                        </tabs>
                    </card-body>
                </card>
            </form>
        </admin-layout>
    </div>
</template>

<script>
import TabHeaders from "../components/Ui/tabs/tabHeaders";
import TabHeader from "../components/Ui/tabs/tabHeader";
import Tabs from "../components/Ui/tabs/tabs";
import Tab from "../components/Ui/tabs/tab";
import HomeTab from "../components/ServiceEditor/tabs/HomeTab";
import SpecialTab from "../components/ServiceEditor/tabs/SpecialTab";
import OfferingsTab from "../components/ServiceEditor/tabs/OfferingsTab";
import Card from "../components/Ui/cards/card";
import CardHeader from "../components/Ui/cards/cardHeader";
import CardBody from "../components/Ui/cards/cardBody";
import CCTab from "../components/ServiceEditor/tabs/CCTab";
export default {
name: "serviceEditor",
    components: {CCTab, CardBody, CardHeader, Card, OfferingsTab, SpecialTab, HomeTab, Tab, Tabs, TabHeader, TabHeaders},
    props: {
        service: Object,
        tab: String,
        locations: Array,
        ministries: Array,
        users: Array,
        tags: Array,
        serviceGroups: Array,
        days: Array,
    },
    data() {
        return {
            activeTab: this.tab,
            editedService: this.service,
        };
    },
    methods: {
        saveService() {
            this.$inertia.patch(route('services.update', this.service.id), this.editedService, { preserveState: false });
        },
    },
}
</script>

<style scoped>
    .card-header {
        padding-bottom: 0 !important;
        background-color: #fcfcfc;
    }

    ul.nav.nav-tabs {
        margin-bottom: 0;
        border-bottom-width: 0;
    }
</style>
