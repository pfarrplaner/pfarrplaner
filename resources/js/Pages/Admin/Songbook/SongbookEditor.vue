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
    <admin-layout title="Liederbuch bearbeiten">
        <template v-slot:navbar-left>
            <save-button @click="saveSongbook" />
        </template>
        <form-input label="Titel" v-model="mySongbook.name"/>
        <form-input label="Kürzel" v-model="mySongbook.code"/>
        <form-input label="ISBN" v-model="mySongbook.isbn"/>
        <form-textarea label="Beschreibung" v-model="mySongbook.description"/>
        <div v-if="!(mySongbook.id)" class="alert alert-info">
            Du musst das Liederbuch erst einmal speichern, um ein Bild hinzufügen zu können.
        </div>
        <div v-else>
            <form-image-attacher
                :attach-route="route('songbook.cover.attach', {model: mySongbook.id})"
                :detach-route="route('songbook.cover.detach', {model: mySongbook.id})"
                label="Titelbild" :handle-paste="true"
                v-model="mySongbook.image"
            />
        </div>
    </admin-layout>
</template>

<script>
import SaveButton from "../../../components/Ui/buttons/SaveButton";
import FormInput from "../../../components/Ui/forms/FormInput";
import FormTextarea from "../../../components/Ui/forms/FormTextarea";
import FormImageAttacher from "../../../components/Ui/forms/FormImageAttacher";
export default {
    name: "SongbookEditor",
    components: {FormImageAttacher, FormTextarea, FormInput, SaveButton},
    props: ['songbook'],
    data() {
        return {
            mySongbook: this.songbook,
        }
    },
    methods: {
        saveSongbook() {
            if (this.mySongbook.id) {
                this.$inertia.patch(route('songbook.update', this.mySongbook.id), this.mySongbook);
            } else {
                this.$inertia.post(route('songbook.store'), this.mySongbook);
            }
        }
    }
}
</script>

<style scoped>

</style>
