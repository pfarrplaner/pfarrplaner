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
    <div class="profile-tab">
        <form-input name="name" label="Name" v-model="user.name" />
        <form-input name="email" label="E-Mailadresse" v-model="user.email" />
        <form-input name="office" label="Pfarramt/Büro" v-model="user.office" />
        <form-textarea name="address" label="Adresse" v-model="user.address" />
        <form-input name="phone" label="Telefon" v-model="user.phone" />
    </div>
</template>

<script>
import FormInput from "../../Ui/forms/FormInput";
import FormTextarea from "../../Ui/forms/FormTextarea";
export default {
    name: "ProfileTab",
    props: ['user'],
    components: {FormTextarea, FormInput}
}
</script>

<style scoped>

</style>
