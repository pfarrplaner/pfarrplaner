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
    <admin-layout title="Kasualien">
        <template slot="navbar-left">
            <a class="btn btn-primary" :href="route('calendar')"><span class="mdi mdi-calendar"></span> <span
                class="d-none d-md-inline">Zum Kalender</span></a>&nbsp;
            <inertia-link v-if="config.wizardButtons == '1'" class="btn btn-light" :href="route('baptisms.create')">
                <span class="mdi mdi-water"></span>
                <span class="d-none d-md-inline">Taufe anlegen...</span></inertia-link>&nbsp;
            <inertia-link v-if="config.wizardButtons == '1'" class="btn btn-light" :href="route('funerals.wizard')">
                <span class="mdi mdi-grave-stone"></span>
                <span class="d-none d-md-inline">Beerdigung anlegen...</span></inertia-link>&nbsp;
            <a v-if="config.wizardButtons == '1'" class="btn btn-light" :href="route('weddings.wizard')"><span
                class="mdi mdi-ring"></span>
                <span class="d-none d-md-inline">Trauung anlegen...</span></a>&nbsp;
        </template>
        <div class="mb-3" v-if="!searching">
            <form-input label="Nach Namen suchen" placeholder="Name" v-model="query" autofocus v-on:keyup.native.enter="doSearch(query)"/>
            <nav-button  icon="mdi mdi-magnify" title="Suchen" type="primary" @click="doSearch(query)"
                        :force-icon="true">Suchen
            </nav-button>
        </div>
        <div v-if="searching" class="text-small text-muted">
            <span class="mdi mdi-spin mdi-loading"></span> Suche nach "{{ query }}" läuft... Das kann etwas länger
            dauern, weil die Daten nur verschlüsselt vorliegen.
        </div>
        <div v-else>
            <div v-if="results.baptisms.length > 0 || results.funerals.length > 0 || results.weddings.length > 0">
                <hr/>
                <h2>Suchergebnisse</h2>
                <div v-if="results.funerals.length > 0">
                    <h3>Beerdigungen</h3>
                    <fake-table :columns="[2,2,4,3,1]" collapsed-header="Beerdigungen"
                                :headers="['Gottesdienst', 'Verstorbene:r', 'Informationen zur Bestattung', 'Dokumente', '']">
                        <funeral v-for="(funeral, funeralIndex) in results.funerals" :funeral="funeral"
                                 :key="funeral.id" :show-service="true"
                                 :show-pastor="true"
                                 class="mb-3 p-1" :class="{'stripe-odd': (funeralIndex % 2 == 0)}"/>
                    </fake-table>
                </div>
                <div v-if="results.baptisms.length > 0">
                    <h3>Taufen</h3>
                    <fake-table :columns="[2,2,4,3,1]" collapsed-header="Taufen"
                                :headers="['Gottesdienst', 'Täufling', 'Informationen zur Taufe', 'Dokumente', '']">
                        <baptism v-for="(baptism, baptismIndex) in results.baptisms" :baptism="baptism"
                                 :key="baptism.id" :show-service="true"
                                 :show-pastor="true"
                                 class="mb-3 p-1" :class="{'stripe-odd': (baptismIndex % 2 == 0)}"/>
                    </fake-table>
                </div>
                <div v-if="results.weddings.length > 0">
                    <h3>Trauungen</h3>
                    <fake-table :columns="[2,2,4,3,1]" collapsed-header="Trauungen"
                                :headers="['Gottesdienst','Hochzeitspaar', 'Informationen zur Trauung', 'Dokumente', '']">
                        <wedding v-for="(wedding, weddingIndex) in results.weddings" :wedding="wedding"
                                 :key="wedding.id" :show-service="true"
                                 :show-pastor="true"
                                 class="mb-3 p-1" :class="{'stripe-odd': (weddingIndex % 2 == 0)}"/>
                    </fake-table>
                </div>
            </div>
            <div v-else>
                Es wurden keine Ergebnisse gefunden.
            </div>
        </div>
    </admin-layout>
</template>

<script>
import FormInput from "../../components/Ui/forms/FormInput";
import FakeTable from "../../components/Ui/FakeTable";
import Baptism from "../../components/ServiceEditor/rites/Baptism";
import Wedding from "../../components/ServiceEditor/rites/Wedding";
import Funeral from "../../components/ServiceEditor/rites/Funeral";
import NavButton from "../../components/Ui/buttons/NavButton";

export default {
    name: "Index",
    props: ['config'],
    components: {NavButton, Funeral, Wedding, Baptism, FakeTable, FormInput},
    data() {
        return {
            apiToken: this.$page.props.currentUser.data.api_token,
            component: this,
            query: '',
            searching: false,
            results: {
                baptisms: [],
                funerals: [],
                weddings: [],
            },
        }
    },
    methods: {
        doSearch(query) {
            if (query.trim() == '') {
                this.results = {
                    baptisms: [],
                    funerals: [],
                    weddings: [],
                };
                return;
            }
            this.searching = true;
            axios.get(route('api.rites.query', {query: query, api_token: this.apiToken}))
                .then(response => {
                    this.searching = false;
                    this.results = response.data;
                });
        },
    }
}
</script>

<style scoped>

</style>
