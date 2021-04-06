<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
  - @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
  - @link https://github.com/potofcoffee/pfarrplaner
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
    <admin-layout title="Tokens verwalten">
        <card>
            <card-header>API-Tokens verwalten</card-header>
            <card-body>
                <table v-if="tokens" class="table table-striped">
                    <thead>
                    <tr>
                        <th>Bezeichnung</th>
                        <th>Angelegt</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(token,key,index) in tokens" :key="key">
                        <td>{{ token.name }}</td>
                        <td>{{ moment(token.created_at).locale('de-DE').format('LLLL')}}</td>
                        <td class="text-right">
                            <button class="btn btn-sm btn-danger" title="Token löschen" @click.prevent="deleteToken(token)">
                                <span class="fa fa-trash"></span>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div v-if="token">
                    <hr />
                    <h3>Neues Token "{{ token.accessToken.name }}" hinzugefügt.</h3>
                    <p>Das folgende Token wird einmalig hier angezeigt. Bitte kopiere den Inhalt an einen sicheren Ort:</p>
                    <pre>{{ token.plainTextToken }}</pre>
                </div>
                <hr />
                <h3>Neues Token hinzufügen</h3>
                <form-input v-model="newTokenTitle" label="Bezeichnung"></form-input>
                <button class="btn btn-success" title="Token hinzufügen" @click.prevent="addToken">
                    Neues Token anlegen
                </button>
            </card-body>
        </card>
    </admin-layout>
</template>

<script>
import Card from "../components/Ui/cards/card";
import CardBody from "../components/Ui/cards/cardBody";
import CardHeader from "../components/Ui/cards/cardHeader";
import FormInput from "../components/Ui/forms/FormInput";
export default {
    name: "tokens",
    components: {FormInput, CardHeader, CardBody, Card},
    props: ['tokens', 'token'],
    data() {
        return {
            newTokenTitle: '',
        }
    },
    methods: {
        addToken() {
            this.$inertia.post(route('tokens.create', {title: this.newTokenTitle}));
        },
        deleteToken(token) {
            this.$inertia.delete(route('tokens.destroy', {token: token.id}));
        }
    }
}
</script>

<style scoped>

</style>
