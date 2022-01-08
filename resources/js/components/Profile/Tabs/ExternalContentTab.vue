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
    <div class="external-content-tab">
        <fieldset>
            <legend>Homepage</legend>
            <form-input name="own_website" label="Eigene Homepage" help="URL der eigenen Homepage"
                        v-model="editedUser.own_website" placeholder="https://..." />
        </fieldset>
        <fieldset>
            <legend>Podcast</legend>
            <form-input name="own_podcast_title" label="Titel" v-model="editedUser.own_podcast_title" />
            <form-input name="own_podcast_url" label="Adresse" v-model="editedUser.own_podcast_url"
                        placeholder="https://..." help="URL des eigenen Podcasts" />
            <form-check name="own_podcast_spotify" label="Auf Spotify verfügbar" v-model="editedUser.own_podcast_spotify" />
            <form-check name="own_podcast_itunes" label="Auf iTunes verfügbar" v-model="editedUser.own_podcast_itunes" />
        </fieldset>
    </div>
</template>

<script>
import FormInput from "../../Ui/forms/FormInput";
import FormCheck from "../../Ui/forms/FormCheck";
export default {
    components: {FormCheck, FormInput},
    props: ['user'],
    name: "ExternalContentTab",
    data() {
        return {
            editedUser: this.user,
        }
    }
}
</script>

<style scoped>

</style>
