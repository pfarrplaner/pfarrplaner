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
    <admin-layout :title="'Bereich'+(mySection.title ? ' '+mySection.title : '')+' bearbeiten'">
        <template slot="navbar-left">
            <save-button @click="saveRow"/>
            <nav-button @click="deleteRow" title="Bereich löschen"
                        class="ml-1" type="danger" icon="mdi mdi-delete">Löschen</nav-button>
        </template>
        <form-input name="title" label="Bezeichnung" v-model="mySection.title" />
        <form-input name="priority" label="Prioriät" type="number" v-model="mySection.priority" />
        <form-group name="color" label="Farbe">
            <verte v-model="mySection.color" :rgbSliders="true" model="rgb" style="justify-content: left"/>
        </form-group>
    </admin-layout>
</template>

<script>
import FormInput from "../../../components/Ui/forms/FormInput";
import verte from 'verte';
import 'verte/dist/verte.css';
import { fromString } from 'css-color-converter';
import FormGroup from "../../../components/Ui/forms/FormGroup";
import NavButton from "../../../components/Ui/buttons/NavButton";
import SaveButton from "../../../components/Ui/buttons/SaveButton";
import SeatingLabel from "../../../components/LocationEditor/SeatingLabel";
import SeatingSplit from "../../../components/LocationEditor/SeatingSplit";
import SectionSelect from "../../../components/Ui/elements/SectionSelect";

export default {
    name: "SeatingRowEditor",
    components: {SectionSelect, SeatingSplit, SeatingLabel, SaveButton, NavButton, FormGroup, FormInput, verte},
    props: ['seatingSection'],
    data() {
        let mySection = this.seatingSection;
        mySection.color = mySection.color ? fromString(mySection.color).toRgbString() : '';
        if (mySection.modelClass) mySection.seating_model = mySection.modelClass;

        return {
            mySection,
        }
    },
    methods: {
        saveRow() {
            if (this.mySection.id) {
                this.$inertia.patch(route('seatingSection.update', this.mySection.id), this.mySection);
            } else {
                this.$inertia.post(route('seatingSection.store'), this.mySection);
            }
        },
        deleteRow() {
            if (!confirm('Willst du diesen Bereich wirklich aus dem Sitzplan löschen?')) return;
            this.$inertia.delete(route('seatingSection.destroy', this.mySection.id));
        }
    }
}
</script>

<style scoped>

</style>
