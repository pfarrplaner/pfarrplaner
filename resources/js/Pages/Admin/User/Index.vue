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
    <admin-layout title="Benutzerverwaltung">
        <template slot="navbar-left">
            <nav-button v-if="canCreate" type="success" icon="mdi mdi-account-plus" title="Neue Person anlegen"
                        @click="createUser">Person hinzufügen</nav-button>
        </template>
        <dataset v-slot="{ ds }"
                 :ds-data="users"
                 ds-sort-by="sortName"
                 :ds-filter-fields="filterFields"
                 :ds-search-in="['name', 'first_name', 'last_name', 'email']">
            <div class="row mb-3" :data-page-count="ds.dsPagecount">
                <div class="col-md-6 mb-2 mb-md-0">
                    <dataset-search ds-search-placeholder="Suchen..." ref="search" autofocus />
                </div>
                <div class="col-md-1 mb-2 mb-md-0">
                    <label v-for="checkbox in checkboxes" class="mt-1" :title="checkbox.title" v-if="checkbox.condition">
                        <input type="checkbox" v-model="checkbox.value" :title="checkbox.title" />
                        <span v-if="checkbox.icon" :class="checkbox.icon"></span>
                        <span v-if="checkbox.label">{{ checkbox.label }}</span>
                    </label>
                </div>
                <div class="col-md-5 text-right">
                    <dataset-show class="float-right" />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover d-md-table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Kirchengemeinde(n)</th>
                                <th>E-Mailadresse</th>
                                <th></th>
                            </tr>
                            </thead>
                            <dataset-item tag="tbody">
                                <template #default="{ row, rowIndex }">
                                    <tr>
                                        <td>
                                            <div>
                                                <span  :class="row.isOfficialUser ? 'mdi mdi-account-check' : 'mdi mdi-account-alert-outline'"></span>
                                                <span v-if="row.last_name && row.first_name">
                                                    <b>{{ row.last_name }}</b>, {{ row.first_name }}
                                                </span>
                                                <span v-else>{{ row.name }}</span>
                                            </div>
                                            <span class="badge badge-secondary"
                                                  v-for="city in row.home_cities">{{ city.name }}</span>
                                        </td>
                                        <td>
                                            <cities-with-access :user="row"/>
                                        </td>
                                        <td>
                                            <div>{{ row.email }}</div>
                                            <role-badge v-for="role in row.roles" :role="role" :key="role.name"/>
                                        </td>
                                        <td class="text-right">
                                            <nav-button type="primary" icon="mdi mdi-account-edit" title="Person bearbeiten"
                                                        class="btn-sm" v-if="canEdit(row)"
                                                        force-icon force-no-text @click="editUser(row)"/>
                                            <nav-button type="light" icon="mdi mdi-call-merge" title="Personen zusammenführen"
                                                        class="btn-sm" v-if="canEdit(row)"
                                                        force-icon force-no-text @click="mergeUser(row)"/>
                                            <nav-button type="light" icon="mdi mdi-lock-reset" title="Passwort zurücksetzen"
                                                        class="btn-sm" v-if="canEdit(row) && row.isOfficialUser"
                                                        force-icon force-no-text @click="resetPassword(row)"/>
                                            <nav-button type="light" icon="mdi mdi-account-switch" title="Als diese Person anmelden"
                                                        v-if="isAdmin && (currentUser.id != row.id) && row.isOfficialUser"
                                                        class="btn-sm"
                                                        force-icon force-no-text @click="loginAsUser(row)"/>
                                            <nav-button type="danger" icon="mdi mdi-account-remove" title="Person löschen" class="btn-sm"
                                                        v-if="canEdit(row)"
                                                        force-icon force-no-text @click="deleteUser(row)"/>
                                        </td>
                                    </tr>
                                </template>
                            </dataset-item>
                        </table>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-md-row flex-column justify-content-between align-items-center border-top pt-2">
                <dataset-info class="mb-2 mb-md-0"/>
                <dataset-pager/>
            </div>
        </dataset>
    </admin-layout>
</template>

<script>
import Card from "../../../components/Ui/cards/card";
import CardBody from "../../../components/Ui/cards/cardBody";
import FakeTable from "../../../components/Ui/FakeTable";
import CardHeader from "../../../components/Ui/cards/cardHeader";
import FormInput from "../../../components/Ui/forms/FormInput";
import PersonName from "../../../components/Ui/elements/Person/PersonName";
import NavButton from "../../../components/Ui/buttons/NavButton";
import {
    Dataset,
    DatasetItem,
    DatasetSearch,
} from 'vue-dataset';
import CitiesWithAccess from "../../../components/Ui/elements/Person/CitiesWithAccess";
import RoleBadge from "../../../components/Ui/elements/Person/RoleBadge";
import DatasetInfo from "../../../components/Ui/dataset/DatasetInfo";
import DatasetShow from "../../../components/Ui/dataset/DatasetShow";
import DatasetPager from "../../../components/Ui/dataset/DatasetPager";

export default {
    name: "Index",
    props: ['users', 'canCreate'],
    components: {
        RoleBadge,
        CitiesWithAccess,
        NavButton, PersonName, FormInput, CardHeader, FakeTable, CardBody, Card,
        Dataset,
        DatasetItem,
        DatasetInfo,
        DatasetPager,
        DatasetSearch,
        DatasetShow
    },
    data() {
        return {
            filter: '',
            isAdmin: this.$page.props.currentUser.data.isAdmin,
            currentUser: this.$page.props.currentUser.data,
            checkboxes: [
                {
                    name: 'isOfficialUser',
                    title: 'Nur Personen mit Benutzerkonto anzeigen',
                    icon: 'mdi mdi-account-check',
                    label: '',
                    value: true,
                    condition: this.$page.props.currentUser.data.isLocalAdmin
                        || this.$page.props.currentUser.data.isAdmin
                        || this.hasPermission('benutzer-beabeiten')
                },
            ],
        }
    },
    computed: {
        filterFields() {
            if (this.checkboxes.every((i) => i.value === false)) {
                return {}
            }
            return this.checkboxes.reduce((acc, curr) => {
                return { ...acc, ...{ [curr.name]: curr.value === false ? '' : curr.value } }
            }, {})
        }
    },
    methods: {
        canEdit(user) {
            if (user.isAdmin) {
                if (user.name == 'Admin') return (this.currentUser.name == 'Admin');
                return (this.currentUser.isAdmin);
            }
            return this.currentUser.isAdmin
            || this.currentUser.isLocalAdmin
            || this.hasPermission('benutzer-bearbeiten');
        },
        createUser() {
            this.$inertia.get(route('user.create'));
        },
        editUser(user) {
            this.$inertia.get(route('user.edit', user.id));
        },
        deleteUser(user) {
            if (confirm('Möchtest du '+user.name+' wirklich endgültig und unwiderruflich löschen?')) {
                this.$inertia.delete(route('user.destroy', user.id));
            }
        },
        loginAsUser(user) {
            window.location.href = route('user.switch', user.id);
        },
        mergeUser(user) {
            window.location.href = route('user.join', user.id);
        },
        resetPassword(user) {
            if (confirm('Willst du das Passwort für '+user.name+' wirklich zurücksetzen? '
                +(user.first_name || user.name)+' erhält dann eine E-Mail mit neuen Zugangsdaten. Das bisherige Passwort '
                +'ist dann ab sofort ungültig.')) {
                this.$inertia.post(route('user.password.reset', user.id), { preserveState: false });
            }
        }
    }
}
</script>

<style scoped>
.row.even {
    background-color: #ececec;
}

.badge {
    margin-right: 1px;
}

.fa.fa-user-times {
    color: darkgray;
}
</style>
