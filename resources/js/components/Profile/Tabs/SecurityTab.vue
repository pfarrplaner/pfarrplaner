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
    <div class="security-tab">
        <form-input name="current_password" label="Aktuelles Passwort"
                    help="Um dein Passwort zu ändern, musst du dein aktuelles Passwort mit angeben."
                    type="password" v-model="password.current" />
        <form-input name="new_password" label="Neues Passwort"
                    type="password" v-model="password.new" />
        <form-input name="new_password_confirm" label="Neues Passwort wiederholen"
                    type="password" v-model="password.confirm" />
    </div>
</template>

<script>
import FormInput from "../../Ui/forms/FormInput";
export default {
    name: "SecurityTab",
    components: {FormInput},
    props: ['user', 'password'],
}
</script>

<style scoped>

</style>
