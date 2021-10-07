<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
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
    <div class="homescreen-configruation-tab">
        <div class="mb-3 p-1">
            <form-check label="Schaltflächen für das schnelle Erstellen von Kasualien anzeigen"
                        v-model="settings.homeScreenConfig.wizardButtons" />
            <form-check label="Aktuell von mir vertretene Kollegen anzeigen"
                        v-model="settings.homeScreenConfig.showReplacements" />
        </div>
        <div class="row">
            <div class="col-md-9 pl-3">
                <h3>Angezeigte Reiter</h3>
                <draggable :list="myTabs" group="tabs">
                    <div v-for="(tab,tabIndex) in myTabs" class="tab-block p-1 m-1 rounded-sm">
                        <div class="row">
                            <div class="col-1">
                                <span class="fa fa-arrows-alt-v"></span>
                            </div>
                            <div class="col-9">
                                <div class="text-bold">{{ tab.config.title || availableTabs[tab.type].title }}</div>
                                <div>{{ availableTabs[tab.type].description }}</div>
                                <div v-if="tab.configVisible">
                                    <hr />
                                    <div :is="configurationComponent(tab)" :tab="tab" :cities="cities"
                                         :locations="locations" :ministries="ministries" />
                                </div>
                            </div>
                            <div class="col-2 text-right">
                                <button v-if="Object.entries(tab.config).length" class="btn btn-light btn-sm"
                                        @click="toggleConfig(tabIndex)"
                                    :title="tab.configVisible ? 'Konfiguration einklappen' : 'Dieser Reiter kann weiter konfiguriert werden'">
                                    <span class="fa" :class="tab.configVisible ? 'fa-chevron-up' : 'fa-chevron-down'"></span>
                                </button>
                                <button class="btn btn-sm btn-danger" @click="deleteTab(tabIndex)" title="Reiter entfernen">
                                    <span class="fa fa-trash"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </draggable>
            </div>
            <div class="col-md-3">
                <h3>Hinzufügen</h3>
                <div class="available-tab btn btn-light mb-1 text-left" v-for="(tab,tabIndex) in availableTabs"
                    @click="addTab(tab.type)">
                    <div class="text-bold">
                        <span class="fa fa-puzzle-piece"></span> {{ tab.title }}
                    </div>
                    <div>{{ tab.description }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import draggable from 'vuedraggable'
import NextServicesTabConfig from "../TabConfig/NextServicesTabConfig";
import BaptismsTabConfig from "../TabConfig/BaptismsTabConfig";
import CasesTabConfig from "../TabConfig/CasesTabConfig";
import FuneralsTabConfig from "../TabConfig/FuneralsTabConfig";
import MissingEntriesTabConfig from "../TabConfig/MissingEntriesTabConfig";
import StreamingTabConfig from "../TabConfig/StreamingTabConfig";
import WeddingsTabConfig from "../TabConfig/WeddingsTabConfig";
import FormCheck from "../../Ui/forms/FormCheck";

export default {
    name: "homeScreenConfigurationTab",
    components: {
        FormCheck,
        draggable,
        NextServicesTabConfig,
        BaptismsTabConfig,
        CasesTabConfig,
        FuneralsTabConfig,
        MissingEntriesTabConfig,
        StreamingTabConfig,
        WeddingsTabConfig,
    },
    props: ['availableTabs', 'homeScreenTabsConfig', 'cities', 'locations', 'ministries', 'settings'],
    created() {
        if (!this.settings.homeScreenConfig) this.settings.homeScreenConfig = {};
        if (!this.settings.homeScreenConfig.wizardButtons) this.settings.homeScreenConfig.wizardButtons = false;
        if (!this.settings.homeScreenConfig.showReplacements) this.settings.homeScreenConfig.showReplacements = false;
    },
    data() {
        if (!this.homeScreenTabsConfig.tabs) this.homeScreenTabsConfig.tabs = [];

        this.homeScreenTabsConfig.tabs.forEach(tab => {
            tab['configVisible'] = false;
        })

        return {
            myTabs: this.homeScreenTabsConfig.tabs,
        }
    },
    methods: {
        addTab(tabType) {
            this.myTabs.push({
                type: tabType,
                config: this.availableTabs[tabType].config,
            });
        },
        deleteTab(tabIndex) {
            this.MyTabs = this.myTabs.splice(tabIndex, 1);
        },
        toggleConfig(tabIndex) {
            this.myTabs[tabIndex].configVisible = !this.myTabs[tabIndex].configVisible;
            this.$forceUpdate();
        },
        configurationComponent(tab) {
            return tab.type.substr(0,1).toUpperCase()+tab.type.substr(1)+'TabConfig';
        }
    }
}
</script>

<style scoped>
    .tab-block {
        border: solid 1px gray;
    }
    .available-tab {
        border: solid 1px gray;
        width: 100%;
    }
</style>
