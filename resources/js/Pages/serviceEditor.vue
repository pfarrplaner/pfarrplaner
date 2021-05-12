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
                <button class="btn btn-danger" @click.prevent="deleteService"><span class="fa fa-trash d-md-none"></span><span class="d-none d-md-inline"> Löschen</span></button>&nbsp;
                <a class="btn btn-light" :href="route('services.liturgy.editor', service.id)" title="Liturgie zu diesem Gottesdienst bearbeiten"><span class="fa fa-th-list"></span><span class="d-none d-md-inline"> Liturgie</span></a>&nbsp;
                <a class="btn btn-light" :href="route('services.sermon.editor', service.id)"  title="Predigt zu diesem Gottesdienst bearbeiten"><span class="fa fa-microphone"></span><span class="d-none d-md-inline"> Predigt</span></a>&nbsp;
            </template>
            <form @submit.prevent="saveService" id="formSermon">
                <card>
                    <card-header>
                        <tab-headers>
                            <tab-header id="home" title="Allgemeines" :active-tab="activeTab" />
                            <tab-header id="people" title="Mitwirkende" :active-tab="activeTab" :count="peopleCount" />
                            <tab-header id="offerings" title="Opfer" :active-tab="activeTab" />
                            <tab-header id="rites" title="Kasualien" :active-tab="activeTab" :count="service.funerals.length+service.baptisms.length+service.weddings.length"/>
                            <tab-header id="cc" title="Kinderkirche" :active-tab="activeTab" />
                            <tab-header id="streaming" title="Streaming" :active-tab="activeTab"  v-if="hasStreaming" />
                            <tab-header id="registrations" title="Anmeldungen" :active-tab="activeTab"  :count="service.seating.count"/>
                            <tab-header id="attachments" title="Dateien" :active-tab="activeTab" :count="countAttachments()"/>
                            <tab-header id="comments" title="Kommentare" :active-tab="activeTab" :count="service.comments.length"/>
                        </tab-headers>
                    </card-header>
                    <card-body>
                        <tabs>
                            <tab id="home" :active-tab="activeTab">
                                <home-tab :service="editedService" :locations="locations"
                                          :days="days"
                                          :tags="tags" :service-groups="serviceGroups"/>
                            </tab>
                            <tab id="people" :active-tab="activeTab">
                                <people-tab :service="service"
                                            :people="users" :ministries="ministries" />
                            </tab>
                            <tab id="offerings" :active-tab="activeTab">
                                <offerings-tab :service="service" />
                            </tab>
                            <tab id="rites" :active-tab="activeTab">
                                <rites-tab :service="service" />
                            </tab>
                            <tab id="cc" :active-tab="activeTab">
                                <c-c-tab :service="service" />
                            </tab>
                            <tab id="streaming" :active-tab="activeTab" v-if="hasStreaming">
                                <streaming-tab :service="service" />
                            </tab>
                            <tab id="registrations" :active-tab="activeTab">
                                <registrations-tab :service="service" />
                            </tab>
                            <tab id="attachments" :active-tab="activeTab">
                                <attachments-tab :service="service" :liturgy-sheets="liturgySheets" :files="files"/>
                            </tab>
                            <tab id="comments" :active-tab="activeTab">
                                <comments-tab :service="service" />
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
import OfferingsTab from "../components/ServiceEditor/tabs/OfferingsTab";
import Card from "../components/Ui/cards/card";
import CardHeader from "../components/Ui/cards/cardHeader";
import CardBody from "../components/Ui/cards/cardBody";
import CCTab from "../components/ServiceEditor/tabs/CCTab";
import StreamingTab from "../components/ServiceEditor/tabs/StreamingTab";
import RitesTab from "../components/ServiceEditor/tabs/RitesTab";
import AttachmentsTab from "../components/ServiceEditor/tabs/AttachmentsTab";
import PeopleTab from "../components/ServiceEditor/tabs/PeopleTab";
import CommentsTab from "../components/ServiceEditor/tabs/CommentsTab";
import RegistrationsTab from "../components/ServiceEditor/tabs/RegistrationsTab";
export default {
name: "serviceEditor",
    components: {
        RegistrationsTab,
        CommentsTab,
        PeopleTab,
        AttachmentsTab,
        RitesTab,
        StreamingTab,
        CCTab, CardBody, CardHeader, Card, OfferingsTab, HomeTab, Tab, Tabs, TabHeader, TabHeaders},
    props: {
        service: Object,
        tab: String,
        locations: Array,
        ministries: Array,
        users: Array,
        tags: Array,
        serviceGroups: Array,
        days: Array,
        liturgySheets: Object,
    },
    computed: {
        peopleCount() {
            var count = this.editedService.pastors.length
                + this.editedService.organists.length
                + this.editedService.sacristans.length;

            Object.entries(this.editedService.ministriesByCategory).forEach(ministry => {
                    console.log(ministry, ministry[1].length);
                    count += ministry[1].length;
                });
            return count;
        }
    },
    data() {
        return {
            activeTab: this.tab,
            editedService: this.service,
            files: { attachments: [null], attachment_text: [''] },
        };
    },
    methods: {
        saveService() {
            // build a request record:
            var record = {
                ...this.editedService,
                participants: {
                    P:  this.extractParticipants(this.editedService.pastors),
                    O:  this.extractParticipants(this.editedService.organists),
                    M:  this.extractParticipants(this.editedService.sacristans),
                },
                ministries: {},
                tags: [],
                serviceGroups: [],
                ...this.files,
            };
            var ct = 0;
            Object.keys(this.editedService.ministriesByCategory).forEach(key => {
                record.ministries[ct] = { description: key, people: [] };
                this.editedService.ministriesByCategory[key].forEach(person => { record.ministries[ct].people.push(person.id) });
                ct++;
            });
            this.editedService.tags.forEach(tag => {record.tags.push(tag.id); });
            this.editedService.service_groups.forEach(group => {record.serviceGroups.push(group.id); });

            // convert to FormData
            let fd = new FormData();
            for (const [key, value] of Object.entries(record)) {
                fd.append(key, value || '');
            }
            // send the request
            this.$inertia.patch(route('services.update', this.service.id), record, {
                preserveState: false
            });
        },
        deleteService() {
            this.$inertia.delete(route('services.destroy', this.editedService.id), {}, { preserveState: false});
        },
        extractParticipants(e) {
            var items = [];
            e.forEach(person => {
                items.push(person.id)
            });
            return items;
        },
        hasKonfiApp() {
            return (undefined != this.service.id) && (this.service.city.konfiapp_apikey != '');
        },
        hasStreaming() {
            return (undefined != this.service.id) && (this.service.city.google_access_token != '');
        },
        countAttachments() {
            var ctr = this.editedService.attachments.length;
            if (this.editedService.liturgy_blocks.length) {
                for (var sheet in this.liturgySheets) {
                    if (!this.liturgySheets[sheet].isNotAFile) ctr++;
                }
            }
            return ctr;
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
