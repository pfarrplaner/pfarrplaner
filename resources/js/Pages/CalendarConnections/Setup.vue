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
    <admin-layout title="Kalenderverbindung einrichten">
        <template slot="navbar-left">
            <button class="btn btn-primary" @click="saveConnection">
                <span class="d-inline d-md-none mdi mdi-content-save"></span><span class="d-none d-md-inline">Speichern</span>
            </button>
            <button class="btn btn-danger ml-1" @click="deleteConnection">
                <span class="d-inline d-md-none mdi mdi-delete"></span><span class="d-none d-md-inline">Löschen</span>
            </button>
        </template>
        <div class="alert alert-info">
            <b>Hinweis:</b> Nach dem Speichern werden alle gewählten Gottesdienste mit dem externen Kalender
            synchronisiert. Es kann eine Weile dauern, bis alle Daten zum externen Kalender übertragen sind. Dieser
            Prozess läuft im Hintergrund ab. Du kannst solange ganz normal weiterarbeiten.
        </div>
        <card>
            <card-header>Verbindungsdaten</card-header>
            <card-body>
                <form-input name="title" label="Bezeichnung der Verbindung" v-model="myConnection.title" autofocus/>
                <form-input name="credentials1" label="Benutzername" v-model="myConnection.credentials1"/>
                <form-input name="credentials2" label="Passwort" v-model="myConnection.credentials2" type="password"/>
                <form-selectize name="type" label="Ziel der Verbindung" :options="myConnectionOptions"
                                v-model="myConnectionType"/>
                <form-input v-if="myConnectionType != 0" name="connection_string" label="Sharepoint-URL"
                            v-model="mySPUrl"/>
                <div v-if="myConnectionType == 0">
                    <div v-if="myConnection.credentials1 && myConnection.credentials2 && (exchangeFolders.length == 0)">
                        <button class="btn btn-info" @click="getFolders">Ordnerliste abrufen</button>
                    </div>
                    <div v-if="myConnection.credentials1 && myConnection.credentials2 && (exchangeFolders.length > 0)">
                        <form-selectize name="connection_string" label="Kalenderordner"
                                        :options="exchangeFolders" v-model="exchangeFolderName"
                                        :settings="{ searchField: ['name']}"/>
                    </div>
                </div>
                <fieldset>
                    <legend>Inhalte</legend>
                </fieldset>
                <div class="row" v-for="(city,key,index) in cities" :key="key">
                    <div class="col-md-4 text-bold">{{ city.name }}</div>
                    <div class="col-md-8">
                        <form-selectize :options="myContentOptions" v-model="myCitiesSync[city.id]['connection_type']"/>
                    </div>

                </div>
                <form-check name="include_hidden" label="Versteckte Gottesdienste mit einbeziehen"
                            v-model="myConnection.include_hidden"/>
                <form-check name="include_hidden" label="Vorbereitungstermine mit einbeziehen"
                            help="z.B. Taufgespräche, Trauergespräche, Traugespräche"
                            v-model="myConnection.include_alternate"/>
            </card-body>
        </card>
    </admin-layout>
</template>

<script>
import Card from "../../components/Ui/cards/card";
import CardHeader from "../../components/Ui/cards/cardHeader";
import CardBody from "../../components/Ui/cards/cardBody";
import FormInput from "../../components/Ui/forms/FormInput";
import FormSelectize from "../../components/Ui/forms/FormSelectize";
import FormCheck from "../../components/Ui/forms/FormCheck";

export default {
    name: "Setup",
    components: {FormCheck, FormSelectize, FormInput, CardBody, CardHeader, Card},
    props: ['calendarConnection', 'cities'],
    data() {
        var myConnectionType = 0;
        var mySPUrl = '';
        var exchangeFolders = [];

        if (this.calendarConnection.connection_string) {
            switch (this.calendarConnection.connection_string.substr(0, 3)) {
                case 'exc':
                    myConnectionType = 0;
                    break;
                case 'spp':
                    myConnectionType = 1;
                    mySPUrl = this.calendarConnection.connection_string.substr(4);
                    break;
                case 'sps':
                    myConnectionType = 2;
                    mySPUrl = this.calendarConnection.connection_string.substr(4);
                    break;
            }
        }

        // get cities / pivot data
        var myCitiesSync = {};
        this.cities.forEach(city => {
            myCitiesSync[city.id] = {connection_type: 0};
        });
        this.calendarConnection.cities.forEach(city => {
            myCitiesSync[city.id] = {connection_type: city.pivot.connection_type};
        });

        return {
            myConnection: this.calendarConnection,
            myConnectionOptions: [
                {id: 0, name: 'Outlook (Exchange-Server)', prefix: 'exc'},
                {id: 1, name: 'Sharepoint (geteilter Kalender)', prefix: 'sps'},
                {id: 2, name: 'Sharepoint (persönlicher Bereich)', prefix: 'spp'},
            ],
            myContentOptions: [
                {id: 0, name: 'keine Einträge'},
                {id: 1, name: 'nur eigene Gottesdienste'},
                {id: 2, name: 'alle Gottesdienste'},
            ],
            myConnectionType: myConnectionType,
            mySPUrl: mySPUrl,
            exchangeFolderName: '',
            myExchangeConnection: '',
            myCitiesSync: myCitiesSync,
            exchangeFolders: exchangeFolders,
        }
    },
    beforeMount() {
        if (this.myConnection.connection_string) {
            if ((this.myConnection.connection_string.substr(0, 3) == 'exc') && (this.myConnection.connection_string.length > 4)) {
                this.exchangeFolderName = this.myConnection.connection_string.substr(4);
                this.getFolders();
            }
        }
    },
    methods: {
        saveConnection() {
            this.myConnection.cities = this.myCitiesSync;
            if (this.myConnectionType == 0) {
                this.myConnection.connection_string = 'exc:' + this.exchangeFolderName;
            } else {
                this.myConnection.connection_string = this.myConnectionOptions[this.myConnectionType]['prefix'] + ':' + this.mySPUrl;
            }

            this.$inertia.patch(route('calendarConnection.update', this.myConnection.id), this.myConnection);
        },
        deleteConnection() {
            this.$inertia.delete(route('calendarConnection.destroy', this.myConnection.id));
        },
        getFolders() {
            axios.post(route('calendarConnection.exchangeCalendars'), this.myConnection)
                .then(response => {
                    this.exchangeFolders = response.data;
                });
        }
    },
}
</script>

<style scoped>

</style>
