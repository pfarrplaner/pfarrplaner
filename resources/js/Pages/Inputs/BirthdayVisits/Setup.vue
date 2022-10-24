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
    <admin-layout title="Geburtstagsbesuche eintragen">
        <template slot="navbar-left">
            <nav-button type="primary" title="Eintragen" icon="mdi mdi-calendar"
                        @click="execute" :disabled="file && mySetup.calendar">Eintragen
            </nav-button>
        </template>
        <form-selectize label="In den folgenden Kalender eintragen" :options="calendarConnections"
                        title-key="title"
                        v-model="mySetup.calendar" name="calendarConnections"/>
        <form-input v-model="mySetup.password" type="password" label="Technisches Passwort"/>
        <div v-if="file">
            <i>Datei ausgewählt.</i>
            <nav-button type="danger" icon="mdi mdi-delete" force-icon @click="file = null">Zurücksetzen</nav-button>
        </div>
        <div v-else>
            <form-file-upload @input="upload" no-camera="1" no-description="1" no-pixabay="1" no-url="1"/>
        </div>
        <hr/>
        <form-group label="Besuchstermine nur an folgenden Tagen gestatten">
            <div>
                <form-check label="Montag" class="form-check-inline" v-model="mySetup.permitted[1]"/>
                <form-check label="Dienstag" class="form-check-inline" v-model="mySetup.permitted[2]"/>
                <form-check label="Mittwoch" class="form-check-inline" v-model="mySetup.permitted[3]"/>
                <form-check label="Donnerstag" class="form-check-inline" v-model="mySetup.permitted[4]"/>
                <form-check label="Freitag" class="form-check-inline" v-model="mySetup.permitted[5]"/>
                <form-check label="Samstag" class="form-check-inline" v-model="mySetup.permitted[6]"/>
                <form-check label="Sonntag" class="form-check-inline" v-model="mySetup.permitted[7]"/>
            </div>
        </form-group>
        <form-check label="Besuche frühestens am Folgetag einplanen" class="form-check-inline" v-model="mySetup.forceNext"/>
        <form-input label="Geplante Besuchsdauer (Minuten)" v-model="mySetup.duration" type="number"/>
        <form-group label="Beginn">
            <div class="row">
                <div class="col-md-6">
                    <form-input label="Frühestens" v-model="mySetup.earliest"/>
                </div>
                <div class="col-md-6">
                    <form-input label="Spätestens" v-model="mySetup.latest"/>
                </div>
            </div>
        </form-group>
        <form-group label="Mittagspause">
            <div class="row">
                <div class="col-md-6">
                    <form-input label="Von" v-model="mySetup.pauseFrom"/>
                </div>
                <div class="col-md-6">
                    <form-input label="Bis" v-model="mySetup.pauseTo" />
                </div>
            </div>
        </form-group>
        <form-group label="Abstand zu anderen Terminen (Minuten)">
            <div class="row">
                <div class="col-md-6">
                    <form-input label="Vorher" v-model="mySetup.preceding" type="number"/>
                </div>
                <div class="col-md-6">
                    <form-input label="Hinterher" v-model="mySetup.trailing" type="number"/>
                </div>
            </div>
        </form-group>
    </admin-layout>
</template>

<script>
import FormGroup from "../../../components/Ui/forms/FormGroup";
import FormSelectize from "../../../components/Ui/forms/FormSelectize";
import NavButton from "../../../components/Ui/buttons/NavButton";
import LocationSelect from "../../../components/Ui/elements/LocationSelect";
import FormFileUpload from "../../../components/Ui/forms/FormFileUpload";
import FormInput from "../../../components/Ui/forms/FormInput";
import FormCheck from "../../../components/Ui/forms/FormCheck";

export default {
    name: "Setup",
    components: {FormCheck, LocationSelect, NavButton, FormSelectize, FormGroup, FormFileUpload, FormInput},
    props: ['calendarConnections', 'setup'],
    data() {
        return {
            file: null,
            mySetup: {
                calendar: this.calendarConnections[0].id,
                password: '',
                duration: this.setup.duration || 30,
                earliest: this.setup.earliest || '10:00',
                latest: this.setup.latest || '17:30',
                preceding: this.setup.preceding || 30,
                pauseFrom: this.setup.pauseFrom || '12:30',
                pauseTo: this.setup.pauseTo || '14:30',
                trailing: this.setup.trailing || 30,
                permitted: this.setup.permitted || {
                    1: false,
                    2: false,
                    3: false,
                    4: true,
                    5: true,
                    6: true,
                    7: true,
                },
                forceNext: this.setup.forceNext || 0,
            }
        }
    },
    methods: {
        upload(file) {
            this.file = file;
            return;
        },
        execute() {
            let fd = new FormData();
            fd.append('setup[calendar]', this.mySetup.calendar);
            fd.append('setup[password]', this.mySetup.password);
            fd.append('setup[duration]', this.mySetup.duration);
            fd.append('setup[earliest]', this.mySetup.earliest);
            fd.append('setup[latest]', this.mySetup.latest);
            fd.append('setup[pauseFrom]', this.mySetup.pauseFrom);
            fd.append('setup[pauseTo]', this.mySetup.pauseTo);
            fd.append('setup[preceding]', this.mySetup.preceding);
            fd.append('setup[trailing]', this.mySetup.trailing);
            fd.append('setup[permitted]', JSON.stringify(this.mySetup.permitted));
            fd.append('setup[forceNext]', this.mySetup.forceNext);
            fd.append('attachment', this.file);

            this.uploading = true;
            this.$inertia.post(route('inputs.save', 'birthdayVisits'), fd, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
        }
    }
}
</script>

<style scoped>

</style>
