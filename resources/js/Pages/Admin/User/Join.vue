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
    <admin-layout title="Benutzer zusammenführen'">
        <template v-slot:navbar-left>
            <save-button label="Zusammenführen" @click="doJoin" />
        </template>
        <form-selectize :options="people" v-model="record.user2" name="user2"
                        :label="'Alle Daten für Benutzer '+user.name+' umschreiben auf:'" />
    </admin-layout>
</template>

<script>
import PeopleSelect from "../../../components/Ui/elements/PeopleSelect";
import FormSelectize from "../../../components/Ui/forms/FormSelectize";
import SaveButton from "../../../components/Ui/buttons/SaveButton";
export default {
    name: "Join",
    components: {SaveButton, FormSelectize, PeopleSelect},
    props: ['user', 'people'],
    data() {
        return {
            record: {
                user1: this.user.id,
                user2: '',
            }
        }
    },
    methods: {
        doJoin() {
            this.$inertia.post(route('user.join.finalize', this.record));
        }
    }
}
</script>

<style scoped>

</style>
