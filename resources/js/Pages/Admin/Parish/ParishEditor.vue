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
    <admin-layout :title="(parish.name || 'Neues Pfarramt')+' bearbeiten'">
        <template v-slot:navbar-left>
            <save-button @click="saveParish"/>
            <nav-button @click="deleteParish" title="Pfarramt löschen"
                        class="ml-1" type="danger" icon="mdi mdi-delete">Löschen</nav-button>

        </template>
        <form-selectize :options="cities" name="city_id" v-model="parish.owningCity" label="Kirchengemeinde"/>
        <form-input name="name" label="Name" v-model="parish.name" />
        <form-input name="code" label="Bezeichnung in DaviP" v-model="parish.code" />
        <form-input name="congregation_name" label="Name der Teilkirchengemeinde" v-model="parish.congregation_name"
                    placeholder="Leer lassen, wenn keine Teilkirchengemeinde"/>
        <form-input name="congregation_url" label="Link zur Teilkirchengemeinde" v-model="parish.congregation_url"
                    placeholder="Leer lassen, wenn keine Teilkirchengemeinde"/>
        <form-input name="address" label="Straße" v-model="parish.address" />
        <form-input name="zip" label="Straße" v-model="parish.zip" />
        <form-input name="city" label="Ort" v-model="parish.city" />
        <form-input name="phone" label="Telefon" v-model="parish.phone" />
        <form-input name="email" label="E-Mailadresse" v-model="parish.email" />
        <form-textarea name="csv" label="CSV-formatierte Straßeneinträge aus DaviP" rows="15" v-model="parish.csv" />
    </admin-layout>
</template>

<script>
import SaveButton from "../../../components/Ui/buttons/SaveButton";
import NavButton from "../../../components/Ui/buttons/NavButton";
import FormInput from "../../../components/Ui/forms/FormInput";
import FormSelectize from "../../../components/Ui/forms/FormSelectize";
import FormTextarea from "../../../components/Ui/forms/FormTextarea";
export default {
    name: "ParishEditor",
    components: {FormTextarea, FormSelectize, FormInput, NavButton, SaveButton},
    props: ['parish', 'cities'],
    data() {
        let myParish = this.parish;
        myParish.owningCity = myParish.city_id;
        myParish.csv = myParish.csv || '';
        return {
            myParish
        }
    },
    methods: {
        saveParish() {
            if (this.myParish.id) {
                this.$inertia.patch(route('parish.update', this.myParish.id), this.myParish);
            } else {
                this.$inertia.post(route('parish.store'), this.myParish);
            }
        },
        deleteParish() {
            if (!confirm('Willst du dieses Pfarramt wirklich löschen?')) return;
            this.$inertia.delete(route('parish.destroy', this.myParish.id));
        }
    }
}
</script>

<style scoped>

</style>
