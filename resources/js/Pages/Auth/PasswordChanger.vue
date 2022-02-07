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
    <admin-layout title="Passwort ändern">
        <template slot="navbar-left">
            <save-button @click="savePassword" />
        </template>
        <div v-if="mustChange" class="alert alert-warning">Dein Benutzerpasswort muss geändert werden. Vielleicht verwendest du noch das Originalpasswort, das du von deinem Administrator bekommen hast?
            Bitte wähle ein neues, eigenes Passwort, das nur du selbst kennst.</div>
        <card>
            <card-header>Passwort ändern</card-header>
            <card-body>
                <form-input name="current_password" v-if="!originalPassword"
                            label="Aktuelles Passwort" type="password"
                            v-model="auth.current_password" />
                <form-input name="new_password"
                            label="Neues Passwort" type="password"
                            v-model="auth.new_password" />
                <form-input name="new_password_confirmation"
                            label="Neues Passwort (wiederholen)" type="password"
                            v-model="auth.new_password_confirmation" />
            </card-body>
        </card>
    </admin-layout>
</template>

<script>
import Card from "../../components/Ui/cards/card";
import CardHeader from "../../components/Ui/cards/cardHeader";
import CardFooter from "../../components/Ui/cards/cardFooter";
import SaveButton from "../../components/Ui/buttons/SaveButton";
import CardBody from "../../components/Ui/cards/cardBody";
import FormInput from "../../components/Ui/forms/FormInput";
export default {
    name: "PasswordChanger",
    props: ['originalPassword', 'mustChange'],
    components: {FormInput, CardBody, SaveButton, CardFooter, CardHeader, Card},
    data() {
        return {
            auth: {
                current_password: '',
                new_password: '',
                new_password_confirmation: '',
            }
        }
    },
    methods: {
        savePassword() {
            let data = this.auth;
            if (this.originalPassword) delete(data.current_password);
            this.$inertia.post(route('password.change'), data);
        },
    }
}
</script>

<style scoped>

</style>
