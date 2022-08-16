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
    <admin-layout :title="'Amtskalender '+moment(myDate+'-01').locale('de').format('MMMM YYYY')" no-content-header>
        <template v-slot:navbar-left>
            <nav-button type="default" class="mr-1" title="Einen Monat zurück" icon="mdi mdi-chevron-left" force-icon
                        force-no-text
                        @click="myDate = moment(myDate+'-01').subtract(1, 'month').format('YYYY-MM')"/>
            <nav-button type="default" class="mr-1" title="Gehe zu heute" icon="mdi mdi-calendar-today" force-icon
                        force-no-text
                        @click="myDate = moment().format('YYYY-MM')"/>
            <nav-button type="default" v-if="!picking" @click="focusPicker" class="mr-1">
                {{ moment(myDate + '-01').locale('de').format('MMMM YYYY') }}
            </nav-button>
            <date-picker v-if="picking" v-model="myDate" :config="myDatePickerSettings" @input="picking=false"
                         autofocus ref="picker"/>
            <nav-button type="default" class="mr-1" title="Einen Monat vor" icon="mdi mdi-chevron-right" force-icon
                        force-no-text
                        @click="myDate = moment(myDate+'-01').add(1, 'month').format('YYYY-MM')"/>
            <nav-button type="light" icon="mdi mdi-file-word" title="Worddokument herunterladen" force-icon
                        @click="openAsWordDocument">Als Dokument öffnen</nav-button>
        </template>
        <div class="row">
            <div class="col-md-4">
                <tab-headers>
                    <tab-header id="services" title="Gottesdienste" :active-tab="activeTab"/>
                    <tab-header id="calendars" title="Kalender" :active-tab="activeTab"
                                v-if="calendarConnections.length > 0"/>
                </tab-headers>
                <tabs class="mb-4">
                    <tab id="services" :active-tab="activeTab">
                        <div v-if="this.myServices.length == 0" class="text-small text-muted">Zur Zeit sind keine
                            Gottesdienste
                            vorhanden, die noch nicht eingetragen sind.
                        </div>
                        <div v-else>
                            <div>
                                <nav-button type="light" icon="mdi mdi-notebook-multiple"
                                            title="Gottesdienste automatisch eintragen"
                                            force-icon class="btn-sm" @click="autoSort">Alle automatisch eintragen
                                </nav-button>
                            </div>
                            <draggable v-if="myServices.length > 0" :list="services"
                                       :class="{ghostClass: 'ghost-block'}"
                                       :group="{name: 'items', put: false}" id="service_list" @change="itemDroppedBack">
                                <div v-for="(service, serviceKey) in myServices" :key="'service_'+serviceKey"
                                     class="border m-1 rounded p-2 service-item">
                                    <div>{{ service.titleText }}</div>
                                    <div>{{ moment(service.date).locale('de').format('DD.MM.YYYY, HH:mm') }}</div>
                                    <div>{{ service.locationText }}</div>
                                </div>
                            </draggable>
                        </div>
                    </tab>
                    <tab id="calendars" :active-tab="activeTab" v-if="calendarConnections.length > 0">
                        <form-selectize label="Kalender auswählen" v-model="activeCalendar"
                                        :options="calendarConnections"/>
                        <div v-if="calendarLoading">
                            <span class="mdi mdi-spin mdi-loading"></span> Kalendereinträge werden geladen.
                        </div>
                        <div v-else>
                            <div>
                                <nav-button type="light" icon="mdi mdi-notebook-multiple" title="Kalendereinträge automatisch eintragen (soweit möglich)"
                                            force-icon @click="autoSortCalendar(activeCalendar)">Automatisch eintragen</nav-button>
                            </div>
                            <form-date-picker v-if="activeCalendar" :config="calendarConfig"
                                              v-model="activeCalendarDate"/>
                            <draggable :list="calendarItems[activeCalendarDate] || []" :group="{name: 'items', put: false}"n
                                       @change="itemDroppedBack">
                                <div v-for="(event, eventKey) in (calendarItems[activeCalendarDate] || [])"
                                     :key="'event_'+event.UID"
                                     class="border m-1 rounded p-2 service-item">
                                    <div>{{ event.Subject }}</div>
                                    <div>{{ moment(event.Start).locale('de').format('DD.MM.YYYY, HH:mm') }}</div>
                                </div>
                            </draggable>
                        </div>
                    </tab>
                </tabs>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-4 p-1" v-for="(diaryCategory,diaryKey) in diaryCategories" :key="diaryKey">
                        <card>
                            <card-header>{{ diaryCategory }}</card-header>
                            <card-body>
                                <draggable group="items" :list="diary[diaryKey]" class="category-list"
                                           :id="'category_'+diaryKey" @change="itemDropped($event, diaryKey)">
                                    <div v-for="(item, itemKey) in diary[diaryKey]" :key="'item_'+diaryKey+'_'+itemKey"
                                         class="diary-entry" @click="edit(item)">
                                        <span v-if="item.title != undefined">{{ moment(item.date).locale('de').format('DD.MM. HH:mm') }} {{ item.title }}</span>
                                        <span v-else class="mdi mdi-spin mdi-loading" title="Eintrag wird erstellt..."></span>
                                    </div>
                                </draggable>
                                <nav-button icon="mdi mdi-plus" class="btn-sm" type="success" title="Eintrag hinzufügen"
                                            force-no-text force-icon @click="createItem(diaryKey)"/>
                            </card-body>
                        </card>
                    </div>
                </div>
            </div>
        </div>
        <diary-entry-editor v-if="editing" :diary-entry="activeEntry" @input="storeItem" @cancel="cancelCreateItem"/>
    </admin-layout>
</template>

<script>
import draggable from 'vuedraggable'
import NavButton from "../../components/Ui/buttons/NavButton";
import Card from "../../components/Ui/cards/card";
import CardHeader from "../../components/Ui/cards/cardHeader";
import CardBody from "../../components/Ui/cards/cardBody";
import {DiaryCategories} from "./DiaryCategories";
import TabHeaders from "../../components/Ui/tabs/tabHeaders";
import TabHeader from "../../components/Ui/tabs/tabHeader";
import Tabs from "../../components/Ui/tabs/tabs";
import Tab from "../../components/Ui/tabs/tab";
import FormSelectize from "../../components/Ui/forms/FormSelectize";
import FormDatePicker from "../../components/Ui/forms/FormDatePicker";
import DiaryEntryEditor from "./DiaryEntryEditor";

export default {
    name: "Index",
    components: {
        DiaryEntryEditor,
        FormDatePicker,
        FormSelectize, Tab, Tabs, TabHeader, TabHeaders, Card, CardBody, CardHeader, NavButton, draggable
    },
    props: ['date', 'services', 'diaryEntries', 'calendarConnections'],
    data() {
        let diary = {};
        let draggableList = {};
        for (const diaryKey in DiaryCategories) {
            diary[diaryKey] = [];
            draggableList[diaryKey] = [];
        }
        this.diaryEntries.forEach(entry => {
            diary[entry.category].push(entry);
        });

        return {
            apiToken: this.$page.props.currentUser.data.api_token,
            activeTab: 'services',
            myDate: (moment(this.date) || moment()).format('YYYY-MM'),
            myDatePickerSettings: {
                format: 'YYYY-MM',
                locale: 'de-de',
                viewMode: 'months',
                keepOpen: true,
            },
            calendarConfig: {
                format: 'YYYY-MM-DD',
                locale: 'de-de',
                inline: true,
                minDate: (moment(this.date) || moment()).format('YYYY-MM') + '-01',
                maxDate: (moment(this.date) || moment()).endOf('month').format('YYYY-MM-DD'),
                enabledDates: [
                    (moment(this.date) || moment()).format('YYYY-MM') + '-01',
                ],
            },
            activeCalendarDate: (moment(this.date) || moment()).format('YYYY-MM') + '-01',
            picking: false,
            diaryCategories: DiaryCategories,
            diary,
            draggableList,
            myServices: this.services,
            activeCalendar: this.calendarConnections.length ? this.calendarConnections[0].id : null,
            calendarLoading: false,
            calendarItems: {},
            activeEntry: null,
            editing: false,
        }
    },
    created() {
        this.loadCalendar();
    },
    watch: {
        myDate: {
            handler: function (newVal, oldVal) {
                this.$inertia.get(route('diary.index', {date: newVal}), {}, {preserveState: false});
            },
        },
        activeCalendar: {
            handler: function (newVal, oldVal) {
                this.loadCalendar();
            },
        }
    },
    methods: {
        loadCalendar() {
            this.calendarLoading = true;
            axios.get(route('api.diary.calendar', {
                calendarConnection: this.activeCalendar,
                date: this.myDate,
                api_token: this.apiToken,
            })).then(response => {
                this.calendarItems = response.data;
                let enabledDates = Object.keys(this.calendarItems);
                this.calendarConfig.enabledDates = enabledDates;
                if (!enabledDates.includes(this.activeCalendarDate)) this.activeCalendarDate = enabledDates[0] || null;
                this.calendarLoading = false;
            });
        },
        focusPicker() {
            this.picking = true;
            this.$nextTick(function() {
                this.$refs.picker.$el.focus();
            }, this);
        },
        autoSort() {
            this.$inertia.post(route('diary.autosort', {date: this.myDate}), {}, {preserveState: false});
        },
        autoSortCalendar(calendarConnection) {
            this.$inertia.post(route('diary.autosort.calendar', {
                date: this.myDate,
                calendarConnection: calendarConnection,
            }), {}, { preserveState: false});
        },
        sortList(category) {
            (category ? this.diary[category] : this.myServices).sort(function (a, b) {
                var keyA = new Date(a.date),
                    keyB = new Date(b.date);
                // Compare the 2 dates
                if (keyA < keyB) return -1;
                if (keyA > keyB) return 1;
                return 0;
            });
        },
        itemDropped({added}, category) {
            if (added) {
                // is this already a diaryEntry item?
                if (added.element.title != undefined) {
                    this.itemMoved(added.element, added.newIndex, category);
                }
                if (added.element.titleText != undefined) {
                    this.serviceAddedToCategory(added.element, added.newIndex, category);
                }
                if (added.element.UID != undefined) {
                    this.eventAddedToCategory(added.element, added.newIndex, category);
                }
            }
        },
        itemMoved(item, newIndex, category) {
            axios.post(route('api.diary.category.items.move', {
                category: category,
                diaryEntry: item.id,
                api_token: this.apiToken
            })).then(response => {
                this.diary[category][newIndex] = response.data;
                this.sortList(category);
                this.$forceUpdate();
            });
        },
        serviceAddedToCategory(service, newIndex, category) {
            axios.post(route('api.diary.category.services.add', {
                category: category,
                service: service.id,
                api_token: this.apiToken,
            })).then(response => {
                this.diary[category][newIndex] = response.data;
                this.sortList(category);
                this.$forceUpdate();
            })
        },
        eventAddedToCategory(event, newIndex, category) {
            console.log('Event added to cat '+category, event, newIndex);
            axios.post(route('api.diary.category.events.add', {
                category: category,
                api_token: this.apiToken,
            }), {event: event}).then(response => {
                this.diary[category][newIndex] = response.data;
                this.sortList(category);
                this.$forceUpdate();
            })
        },
        itemDroppedBack({added}) {
            if (!added) return;
            let orginal = added.element;
            console.log('itemDroppedBack data', added);
            axios.delete(route('api.diary.entry.destroy', {
                diaryEntry: added.element.id,
                api_token: this.apiToken,
            })).then(response => {
                if (response.data.id) {
                    this.myServices[added.newIndex] = response.data;
                    this.sortList(null);
                } else {
                    this.calendarItems[moment(response.data.date).format('YYYY-MM-DD')][added.newIndex] = {
                        Start: response.data.date,
                        Subject: response.data.title,
                    }
                }
                this.$forceUpdate();
            });
        },
        openAsWordDocument() {
            window.location.href = route('diary.word', {date: this.myDate});
        },
        edit(item) {
            this.activeEntry = item;
            this.editing = true
        },
        createItem(category) {
            this.activeEntry = {
                id: -1,
                title: '',
                date: moment(this.myDate).startOf('month'),
                category: category,
            };
            this.diary[category].push(this.activeEntry);
            this.editing = true
        },
        cancelCreateItem(source) {
            let found = false;
            for (let index in this.diary[this.activeEntry.category]) {
                if (this.diary[this.activeEntry.category][index].id == -1) found = index;
            }
            if (found) this.diary[this.activeEntry.category].splice(found, 1);
            this.activeEntry = null;
            this.editing = false;
            this.$forceUpdate();
        },
        storeItem(item) {
            this.editing = false;
            if (item.created) {
                console.log('Created!', item, this.diary[item.category]);
                for (let index in this.diary[item.category]) {
                   if (this.diary[item.category][index].id == -1) this.diary[item.category][index] = item;
                };
            }
            this.sortList(item.category);

        },
    }
}
</script>

<style scoped>
.category-list {
    font-size: .8em;
}

.service-item {
    font-size: .8em;
}

.service-item div:first-child {
    font-weight: bold;
}

.diary-entry:hover {
    cursor: pointer;
    background-color: lightyellow;
}
</style>
