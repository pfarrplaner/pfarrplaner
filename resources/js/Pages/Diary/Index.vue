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
            <nav-button type="light" v-if="!picking" @click="picking=true">
                {{ moment(myDate + '-01').locale('de').format('MMMM YYYY') }}
            </nav-button>
            <date-picker v-if="picking" v-model="myDate" :config="myDatePickerSettings" @input="picking=false"
                         autofocus/>
        </template>
        <div class="row">
            <div class="col-md-4">
                <h2>Gottesdienste</h2>
                <div v-if="this.myServices.length == 0" class="text-small text-muted">Zur Zeit sind keine Gottesdienste
                    vorhanden, die noch nicht eingetragen sind.
                </div>
                <div v-else>
                    <div>
                        <nav-button type="light" icon="mdi mdi-notebook-multiple"
                                    title="Gottesdienste automatisch eintragen"
                                    force-icon class="btn-sm" @click="autoSort">Alle automatisch eintragen
                        </nav-button>
                    </div>
                    <draggable v-if="myServices.length > 0" :list="services" :class="{ghostClass: 'ghost-block'}"
                               group="items" id="service_list" @change="itemDroppedBack">
                        <div v-for="(service, serviceKey) in myServices" :key="'service_'+serviceKey"
                             class="border m-1 rounded p-2 service-item">
                            <div>{{ service.titleText }}</div>
                            <div>{{ moment(service.date).locale('de').format('DD.MM.YYYY, HH:mm') }}</div>
                            <div>{{ service.locationText }}</div>
                        </div>
                    </draggable>
                </div>
                <h2>Kalender</h2>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <card class="col-md-4" v-for="(diaryCategory,diaryKey) in diaryCategories" :key="diaryKey">
                        <card-header>{{ diaryCategory }} ({{ diaryKey }})</card-header>
                        <card-body>
                            <draggable group="items" :list="diary[diaryKey]" class="category-list"
                                       :id="'category_'+diaryKey" @change="itemDropped($event, diaryKey)">
                                <div v-for="(item, itemKey) in diary[diaryKey]" :key="'item_'+diaryKey+'_'+itemKey">
                                    {{ moment(item.date).locale('de').format('DD.MM. HH:mm') }} {{ item.title }}
                                </div>
                            </draggable>
                        </card-body>
                    </card>
                </div>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import draggable from 'vuedraggable'
import NavButton from "../../components/Ui/buttons/NavButton";
import Card from "../../components/Ui/cards/card";
import CardHeader from "../../components/Ui/cards/cardHeader";
import CardBody from "../../components/Ui/cards/cardBody";
import {DiaryCategories} from "./DiaryCategories";

export default {
    name: "Index",
    components: {Card, CardBody, CardHeader, NavButton, draggable},
    props: ['date', 'services', 'diaryEntries'],
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
            myDate: (moment(this.date) || moment()).format('YYYY-MM'),
            myDatePickerSettings: {
                format: 'YYYY-MM',
                locale: 'de-de',
                viewMode: 'months',
            },
            picking: false,
            diaryCategories: DiaryCategories,
            diary,
            draggableList,
            myServices: this.services,
            currentService: null,
        }
    },
    methods: {
        autoSort() {
            this.$inertia.post(route('diary.autosort', {date: this.myDate}), {}, {preserveState: false});
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
        itemDroppedBack({added}) {
            if (!added) return;
            console.log('itemDroppedBack data', added);
            axios.delete(route('api.diary.entry.destroy', {
                diaryEntry: added.element.id,
                api_token: this.apiToken,
            })).then(response => {
                this.myServices[added.newIndex] = response.data;
                this.sortList(null);
                this.$forceUpdate();
            });
        }
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
</style>
