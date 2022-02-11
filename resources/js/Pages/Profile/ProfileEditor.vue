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
    <admin-layout title="Mein Profil">
        <template slot="navbar-left">
            <button class="btn btn-primary" title="Profil speichern" @click="saveProfile">
                <span class="d-inline d-md-none fa fa-save"></span> <span class="d-none d-md-inline">Speichern</span>
            </button>
        </template>
        <template slot="tab-headers">
            <tab-headers>
                <tab-header title="Profil" id="profile" :active-tab="activeTab"/>
                <tab-header title="Sicherheit" id="security" :active-tab="activeTab"/>
                <tab-header title="E-Mailbenachrichtigungen" id="subscriptions" :active-tab="activeTab"/>
                <tab-header title="Startseite" id="homeScreenConfiguration" :active-tab="activeTab"/>
                <tab-header title="Verbundene Kalender" id="calendars" :active-tab="activeTab"
                            :count="calendarConnections.length"
                            :disabled="user.email.slice(-8) != '@elkw.de'"
                            disabled-title="Diese Funktion ist nur für Benutzer mit einem ELKW-Konto verfügbar."/>
                <tab-header title="Externe Inhalte" id="externalContent" :active-tab="activeTab"/>
            </tab-headers>
        </template>
        <tabs>
            <tab id="profile" :active-tab="activeTab">
                <profile-tab :user="user"/>
            </tab>
            <tab id="security" :active-tab="activeTab">
                <security-tab :user="user" :password="password"/>
            </tab>
            <tab id="subscriptions" :active-tab="activeTab">
                <subscriptions-tab :cities="cities" :subscriptions="subscriptions"/>
            </tab>
            <tab id="homeScreenConfiguration" :active-tab="activeTab">
                <home-screen-configuration-tab :available-tabs="availableTabs" :cities="cities"
                                               :home-screen-tabs-config="homeScreenTabsConfig"
                                               :locations="locations" :ministries="ministries"
                                               :settings="settings"/>
            </tab>
            <tab id="calendars" :active-tab="activeTab">
                <calendar-connections-tab :user="user" :calendar-connections="calendarConnections"/>
            </tab>
            <tab id="externalContent" :active-tab="activeTab">
                <external-content-tab :user="editedUser"/>
            </tab>
        </tabs>
    </admin-layout>
</template>

<script>
import Card from "../../components/Ui/cards/card";
import CardHeader from "../../components/Ui/cards/cardHeader";
import TabHeaders from "../../components/Ui/tabs/tabHeaders";
import TabHeader from "../../components/Ui/tabs/tabHeader";
import CardBody from "../../components/Ui/cards/cardBody";
import Tabs from "../../components/Ui/tabs/tabs";
import Tab from "../../components/Ui/tabs/tab";
import ProfileTab from "../../components/Profile/Tabs/ProfileTab";
import SecurityTab from "../../components/Profile/Tabs/SecurityTab";
import CalendarConnectionsTab from "../../components/Profile/Tabs/CalendarConnectionsTab";
import SubscriptionsTab from "../../components/Profile/Tabs/SubscriptionsTab";
import HomeScreenConfigurationTab from "../../components/Profile/Tabs/HomeScreenConfigurationTab";
import ExternalContentTab from "../../components/Profile/Tabs/ExternalContentTab";

export default {
    name: "ProfileEditor",
    props: ['tab', 'user', 'calendarConnections', 'cities', 'subscriptions', 'availableTabs', 'homeScreenTabsConfig',
        'locations', 'ministries', 'settings'],
    components: {
        ExternalContentTab,
        HomeScreenConfigurationTab,
        SubscriptionsTab,
        CalendarConnectionsTab,
        SecurityTab, ProfileTab, Tab, Tabs,  TabHeader, TabHeaders,
    },
    data() {
        return {
            activeTab: this.tab || 'profile',
            password: {current: '', new: '', confirm: ''},
            editedUser: this.user,
        }
    },
    methods: {
        saveProfile() {
            var result = {
                name: this.user.name,
                email: this.user.email,
                office: this.user.office,
                address: this.user.address,
                phone: this.user.phone,
                subscriptions: this.subscriptions,
                homeScreenTabsConfig: this.homeScreenTabsConfig,
                settings: this.settings,
                own_website: this.user.own_website,
                own_podcast_title: this.user.own_podcast_title,
                own_podcast_url: this.user.own_podcast_url,
                own_podcast_spotify: this.user.own_podcast_spotify,
                own_podcast_itunes: this.user.own_podcast_itunes,
            };
            if (this.password.current && this.password.new && this.password.confirm) {
                result['current_password'] = this.password.current;
                result['new_password'] = this.password.new;
                result['new_password_confirmation'] = this.password.confirm;
            }
            this.$inertia.patch(route('user.profile.save'), result);
        },
    }
}
</script>

<style scoped>
</style>
