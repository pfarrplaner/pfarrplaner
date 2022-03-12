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
    <admin-layout title="Benutzerrollen">
        <template v-slot:navbar-left>
            <save-button @click="saveRole" />
            <nav-button class="ml-1" type="danger" icon="mdi mdi-delete" title="Benutzerrolle löschen"
                        @click="deleteRole">Löschen</nav-button>
        </template>
        <form-input name="name" label="Name" v-model="myRole.name" />
        <form-selectize name="permissions" label="Berechtigungen" v-model="myRole.permissions"
                        id-key="name"  :options="Object.values(permissions)" multiple
                        :settings="selectizeSettings" />
    </admin-layout>
</template>

<script>
import NavButton from "../../../components/Ui/buttons/NavButton";
import SaveButton from "../../../components/Ui/buttons/SaveButton";
import FormInput from "../../../components/Ui/forms/FormInput";
import FormSelectize from "../../../components/Ui/forms/FormSelectize";


export default {
    name: "Index",
    components: {
        FormSelectize,
        FormInput,
        SaveButton,
        NavButton
    },
    props: ['role', 'permissions'],
    data() {
        let myRole = this.role;

        for (let permissionKey in myRole.permissions) {
            myRole.permissions[permissionKey] = myRole.permissions[permissionKey].name;
        }

        return {
            myRole,
            selectizeSettings: {
                create: function(item) {
                    return {name: item};
                },
            },
        }
    },
    methods: {
        saveRole() {
            if (this.myRole.id) {
                this.$inertia.patch(route('role.update', this.myRole.id), this.myRole);
            } else {
                this.$inertia.post(route('role.store'), this.myRole);
            }
        },
        deleteRole(role) {
            if (confirm('Willst du diese Benutzerrolle wirklich komplett löschen?')) {
                this.$inertia.delete(route('role.destroy', this.role.id));
            }
        }
    }
}
</script>

<style scoped>
.row.even {
    background-color: #ececec;
}
</style>
