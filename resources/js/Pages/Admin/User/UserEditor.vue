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
    <admin-layout title="Person bearbeiten">
        <template slot="navbar-left">
            <save-button @click="saveUser"/>
            <nav-button v-if="myUser.isOfficialUser && (!justCreated)"
                        title="Passwort zurücksetzen und Nachricht versenden"
                        @click="resetUserPassword" class="ml-1"
                        type="light" icon="key">Passwort zurücksetzen
            </nav-button>
            <nav-button type="danger" icon="trash" title="Benutzer löschen" class="ml-1"
                        @click="deleteUser">Löschen
            </nav-button>
        </template>
        <template slot="tab-headers">
            <tab-headers>
                <tab-header id="home" :active-tab="activeTab" title="Person"/>
                <tab-header id="account" :active-tab="activeTab" title="Benutzerkonto"/>
                <tab-header v-if="myUser.isOfficialUser" id="permissions" :active-tab="activeTab"
                            title="Berechtigungen"/>
                <tab-header v-if="myUser.isOfficialUser" id="menu" :active-tab="activeTab"
                            title="Menü"/>
                <tab-header v-if="myUser.isOfficialUser" id="homescreen" :active-tab="activeTab"
                            title="Startseite"/>
                <tab-header v-if="myUser.isOfficialUser" id="settings" :active-tab="activeTab"
                            title="Alle Einstellungen"/>
                <tab-header id="absences" :active-tab="activeTab" title="Urlaub"/>
            </tab-headers>
        </template>
        <tabs>
            <tab id="home" :active-tab="activeTab">
                <form-input name="name" label="Name" v-model="myUser.name"/>
                <div class="row">
                    <div class="col-md-2">
                        <form-input name="title" label="Titel" v-model="myUser.title"/>
                    </div>
                    <div class="col-md-5">
                        <form-input name="first_name" label="Vorname" v-model="myUser.first_name"/>
                    </div>
                    <div class="col-md-5">
                        <form-input name="last_name" label="Nachname" v-model="myUser.last_name"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-7">
                        <form-input name="office" label="Dienststelle" v-model="myUser.office"/>
                        <form-textarea name="address" label="Adresse" v-model="myUser.address"/>
                        <form-input name="phone" label="Telefonnummer"
                                    help="Nur Zahlen und Leerzeichen erlaubt" v-model="myUser.phone"/>
                        <form-input type="email" name="email" label="E-Mailadresse" v-model="myUser.email"
                                    :key="user.email"
                                    :required="Boolean(myUser.isOfficialUser)"/>
                    </div>
                    <div class="col-md-5">
                        <form-image-attacher v-if="myUser.id"
                                             name="image" v-model="myUser.image"
                                             label="Benutzerbild"
                                             :attach-route="route('user.attach', {model: myUser.id})"
                                             :detach-route="route('user.detach', {model: myUser.id})"/>
                        <div v-else>
                            <label>Benutzerbild</label>
                            <div class="alert alert-info">Die Person muss zuerst einmal gespeichert werden,
                                bevor ein Bild angehängt werden kann.
                            </div>
                        </div>
                    </div>
                </div>
            </tab>
            <tab id="account" :active-tab="activeTab">
                <div v-if="!myUser.isOfficialUser" class="alert alert-warning">
                            <span
                                class="text-bold">Für diese Person wurde bisher noch kein Benutzerkonto angelegt.</span>
                    <form-check label="Benutzerkonto anlegen und Willkommensnachricht mit Kontodaten versenden"
                                v-model="myUser.isOfficialUser" @input="createUserAccount"/>
                </div>
                <div v-else>
                    <form-input type="email" name="email" :required="true"
                                :key="user.email"
                                label="E-Mailadresse"
                                help="Die E-Mailadresse ist auch der Benutzername zur Anmeldung."
                                v-model="myUser.email"/>
                    <form-selectize name="homeCities[]" v-model="myUser.home_cities" multiple
                                    label="Diese Person gehört zu folgenden Kirchengemeinden"
                                    :options="cities"
                                    :settings="{ labelField: 'name', searchFields: ['name']}"/>
                    <form-selectize name="parishes[]" v-model="myUser.parishes" multiple
                                    label="Diese Person hat folgende Pfarrämter inne"
                                    :options="parishes"
                                    :settings="{ labelField: 'name', searchFields: ['name']}"/>
                </div>
            </tab>
            <tab v-if="myUser.isOfficialUser" id="permissions" :active-tab="activeTab">
                <form-selectize name="roles[]" v-model="myUser.roles" multiple
                                label="Benutzerrollen"
                                :options="Object.values(roles)"
                                :settings="{ labelField: 'name', searchFields: ['name']}"/>
                <hr/>
                <fake-table :columns="[4,4,4]" :headers="['Kirchengemeinde', 'Rechte', 'Benachrichtungen']"
                            collapsed-header="Kirchengemeinden" class="border-bottom">
                    <div class="row p-1 mb-3 border-top" v-for="city in adminCities">
                        <div class="col-md-4">{{ city.name }}</div>
                        <div class="col-md-4">
                            <city-permission-toggle v-model="cityPermission[city.id].permission"/>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select v-model="mySubscriptions[city.id].subscription_type"
                                        class="form-control">
                                    <option value="0">keine Benachrichtigungen</option>
                                    <option value="1">bei Änderungen an eigenen Gottesdiensten</option>
                                    <option value="2">bei Änderungen an allen Gottesdiensten</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </fake-table>
            </tab>
            <tab v-if="myUser.isOfficialUser" id="menu" :active-tab="activeTab">
                <p>Hier können im Menü für diese Person einzelne Module an und abgeschaltet werden.</p>
                <div v-for="(moduleGroup,moduleGroupTitle,moduleGroupIndex) in modules" class="border-bottom">
                    <div class="text-bold py-3 border-top mt-3" v-if="moduleGroupTitle != 'default'">{{
                            moduleGroupTitle
                        }}
                    </div>
                    <div v-for="(module,moduleIndex) in moduleGroup" class="row module-row p-2"
                         :class="moduleIndex % 2 ? 'odd' : 'even'">
                        <div class="col-md-1">
                            <form-check label="" :name="'settings[modules]['+module.key+']'"
                                        v-model="mySettings.modules[module.key]"/>
                        </div>
                        <div class="col-md-11">
                            <span class="fa" :class="'fa-'+module.icon" :style="{color: module.color}"></span>
                            <span>{{ module.title }}</span>
                        </div>
                    </div>
                </div>
            </tab>
            <tab v-if="myUser.isOfficialUser" id="homescreen" :active-tab="activeTab">
                <home-screen-configuration-tab :available-tabs="availableTabs" :cities="cities"
                                               :home-screen-tabs-config="mySettings.homeScreenTabsConfig"
                                               :locations="locations" :ministries="ministries"
                                               :settings="settings" third-party="1" :module-groups="modules"/>
            </tab>
            <tab v-if="myUser.isOfficialUser" id="settings" :active-tab="activeTab">
                <div v-if="!riskTaker" class="alert alert-warning">
                    <p>Auf dieser Seite können <u>alle</u> Benutzereinstellungen bearbeitet werden. Das solltest
                        du nur tun, wenn du wirklich weißt, was du tust.</p>
                    <form-check v-model="riskTaker" label="Verstanden, ich weiß, was ich da tue."/>
                </div>
                <div v-if="riskTaker" :key="Object.keys(mySettings).length">
                    <fake-table :columns="[3,8,1]" :headers="['Einstellung', 'Inhalt', '']"
                                collapsed-header="Benutzereinstellungen" class="border-bottom">
                        <div v-for="settingKey in Object.keys(mySettings).sort()" class="row p-1 mb-3 border-top"
                             v-if="mySettings[settingKey]">
                            <div class="col-md-3"><code class="text-bold">{{ settingKey }}</code></div>
                            <div class="col-md-7">
                                <div v-if="editSetting == settingKey">
                                    <json-editor :data-input="mySettings[settingKey]"
                                                 @data-output="(data) => (mySettings[settingKey] = data)"/>
                                </div>
                                <vue-json-pretty v-else @click="editSetting = settingKey"
                                                 :data="mySettings[settingKey]"/>
                            </div>
                            <div class="col-md-1 text-right">
                                <nav-button type="danger" icon="trash" force-no-text force-icon
                                            class="btn-sm pull-right"
                                            title="Einstellung löschen" @click="mySettings[settingKey] = null"/>
                            </div>
                        </div>
                    </fake-table>
                </div>

            </tab>
            <tab id="absences" :active-tab="activeTab">
                <form-check name="manage_absences" label="Urlaub für diesen Benutzer verwalten"
                            v-model="myUser.manage_absences"/>
                <div v-if="myUser.manage_absences">
                    <form-check name="show_absences_with_services" label="Urlaub im Gottesdienstplaner anzeigen"
                                v-model="myUser.show_vacations_with_services"/>
                    <form-check name="needs_replacement" label="Urlaubsvertretung benötigt"
                                v-model="myUser.needs_replacement"/>
                    <hr/>
                    <legend>Genehmigungsprozess</legend>
                    <p>Wenn die folgenden Felder ausgefüllt sind, durchläuft ein Abwesenheitsantrag dieser
                        Person
                        zuerst <a href="/media/manual/media/documents/Urlaubsanträge im Pfarrplaner.pdf"
                                  target="_blank">einen Genehmigungsprozess</a>.</p>
                    <people-select label="Urlaub muss durch eine der folgenden Personen geprüft werden"
                                   :people="users" v-model="myUser.vacation_admins"/>
                    <people-select label="Urlaub muss durch eine der folgenden Personen genehmigt werden"
                                   :people="users" v-model="myUser.vacation_approvers"/>
                </div>
            </tab>
        </tabs>
    </admin-layout>
</template>

<script>
import Card from "../../../components/Ui/cards/card";
import CardHeader from "../../../components/Ui/cards/cardHeader";
import CardBody from "../../../components/Ui/cards/cardBody";
import TabHeaders from "../../../components/Ui/tabs/tabHeaders";
import TabHeader from "../../../components/Ui/tabs/tabHeader";
import Tabs from "../../../components/Ui/tabs/tabs";
import Tab from "../../../components/Ui/tabs/tab";
import FormInput from "../../../components/Ui/forms/FormInput";
import FormTextarea from "../../../components/Ui/forms/FormTextarea";
import FormImageAttacher from "../../../components/Ui/forms/FormImageAttacher";
import FormCheck from "../../../components/Ui/forms/FormCheck";
import SaveButton from "../../../components/Ui/buttons/SaveButton";
import NavButton from "../../../components/Ui/buttons/NavButton";
import FormSelectize from "../../../components/Ui/forms/FormSelectize";
import FakeTable from "../../../components/Ui/FakeTable";
import CityPermissionToggle from "../../../components/Ui/elements/Person/CityPermissionToggle";
import FormGroup from "../../../components/Ui/forms/FormGroup";
import PeopleSelect from "../../../components/Ui/elements/PeopleSelect";
import HomeScreenConfigurationTab from "../../../components/Profile/Tabs/HomeScreenConfigurationTab";
import JsonEditor from "@kassaila/vue-json-editor";
import "@kassaila/vue-json-editor/src/styles/main.scss";

import VueJsonPretty from 'vue-json-pretty';
import 'vue-json-pretty/lib/styles.css';

export default {
    name: "UserEditor",
    props: [
        'user',
        'cities',
        'adminCities',
        'homescreen',
        'roles',
        'parishes',
        'users',
        'activeTab',
        'subscriptions',
        'settings',
        'availableTabs',
        'locations',
        'ministries',
        'modules',
    ],
    components: {
        HomeScreenConfigurationTab,
        PeopleSelect,
        FormGroup,
        CityPermissionToggle,
        FakeTable,
        FormSelectize,
        NavButton,
        SaveButton,
        FormCheck,
        FormImageAttacher,
        FormTextarea, FormInput, Tab, Tabs, TabHeader, TabHeaders, CardBody, CardHeader, Card,
        JsonEditor,
        VueJsonPretty,
    },
    data() {
        let myUser = this.user;
        console.log(myUser.writable_cities);
        myUser.password = null;

        myUser.home_cities = this.reduceToIds(myUser.home_cities);
        myUser.parishes = this.reduceToIds(myUser.parishes);
        myUser.roles = this.reduceToIds(myUser.roles);
        myUser.cities = this.reduceToIds(myUser.cities);
        console.log(myUser.writable_cities);
        myUser.writable_cities = this.reduceToIds(myUser.writable_cities);
        console.log(myUser.writable_cities);
        myUser.admin_cities = this.reduceToIds(myUser.admin_cities);
        myUser.vacation_admins = this.reduceToIds(myUser.vacation_admins);
        myUser.vacation_approvers = this.reduceToIds(myUser.vacation_approvers);

        // create empty settings for new users
        this.settings.homeScreenTabsConfig = this.settings.homeScreenTabsConfig || {tabs: []}

        // create empty modules setting
        if (!this.settings.modules) {
            this.settings.modules = {};
            for (const group in this.modules) {
                this.modules[group].forEach(module => {
                    this.settings.modules[module.key] = 1;
                });
            }
        }

        let cityPermission = {};
        this.adminCities.forEach(city => {
            cityPermission[city.id] = {permission: 'n'};
            myUser.cities.forEach(thisCity => {
                if (thisCity == city.id) cityPermission[thisCity] = {permission: 'r'};
            });
            myUser.writable_cities.forEach(thisCity => {
                if (thisCity == city.id) cityPermission[thisCity] = {permission: 'w'};
            });
            myUser.admin_cities.forEach(thisCity => {
                if (thisCity == city.id) cityPermission[thisCity] = {permission: 'a'};
            });
        });
        console.log('cityPermission', cityPermission);

        let mySubscriptions = {};
        this.subscriptions.forEach(subscription => {
            mySubscriptions[subscription.city_id] = subscription;
        });

        this.adminCities.forEach(city => {
            mySubscriptions[city.id] = mySubscriptions[city.id] || {subscription_type: 0};
        })

        return {
            myUser,
            cityPermission,
            justCreated: false,
            mySubscriptions,
            riskTaker: false,
            mySettings: this.settings,
            editSetting: null,
        }
    },
    methods: {
        createUserAccount(e) {
            if (e) {
                this.myUser.password = 'testtest';
                this.justCreated = true;
            }
        },
        resetUserPassword() {
            if (confirm('Willst du das Passwort für ' + this.user.name + ' wirklich zurücksetzen? '
                + (this.user.first_name || this.user.name) + ' erhält dann eine E-Mail mit neuen Zugangsdaten. Das bisherige Passwort '
                + 'ist dann ab sofort ungültig.')) {
                this.$inertia.post(route('user.password.reset', this.user.id));
            }
        },
        saveUser() {
            let permissions = {};
            for (const city in this.cityPermission) {
                permissions[city] = this.cityPermission[city].permission;
            }

            let subscriptions = {};
            for (const city in this.mySubscriptions) {
                subscriptions[city] = this.mySubscriptions[city].subscription_type;
            }

            let record = {
                ...this.myUser,
                settings: this.mySettings,
                cityPermissions: this.cityPermissions,
                vacation_admins: this.reduceToIds(this.myUser.vacation_admins),
                vacation_approvers: this.reduceToIds(this.myUser.vacation_approvers),
                createAccount: this.justCreated ? 1 : 0,
                permissions,
                subscriptions,
            };
            if (this.myUser.id) {
                this.$inertia.patch(route('user.update', this.myUser.id), record);
            } else {
                this.$inertia.post(route('user.store'), record);
            }
        },
        deleteUser() {
            if (confirm('Möchtest du ' + this.user.name + ' wirklich endgültig und unwiderruflich löschen?')) {
                this.$inertia.delete(route('user.destroy', this.user.id));
            }
        },
        reduceToIds(records) {
            let ids = [];
            records.forEach(record => {
                if (record && record.id) ids.push(record.id);
            });
            return ids;
        },
        deleteSetting(key) {
            console.log('delete Setting', key, this.mySettings[key]);
            delete this.mySettings[key];
            console.log(this.mySettings[key], this.mySettings);
        }
    }
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

.module-row {
    width: 100%;
}

.module-row.even {
    background-color: lightgray;
}

</style>
