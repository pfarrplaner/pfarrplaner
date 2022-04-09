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
    <div class="songbook-list">
        <label>Steht in folgenden Liederbüchern:</label>
        <table class="table table-striped table-hover" :key="mySongbooks.length">
            <tbody>
            <tr v-for="(songbook,songbookIndex,songbookKey) in mySongbooks" :key="songbookKey">
                <td colspan="2" v-if="editing == songbookIndex">
                    <songbook-select :songbooks="allSongbooks" v-model="mySongbooks[songbookIndex]" :pivot="songbook.pivot"/>
                </td>
                <td v-if="editing != songbookIndex">{{ songbook.code }}</td>
                <td v-if="editing != songbookIndex">{{ songbook.name }}</td>
                <td>
                    <div v-if="editing == songbookIndex">
                        <form-input v-model="songbook.pivot.reference" />
                    </div>
                    <div v-else>{{ songbook.pivot.reference }}</div>
                </td>
                <td class="text-right">
                    <nav-button icon="mdi mdi-pencil" title="Eintrag bearbeiten" v-if="editing != songbookIndex"
                                class="btn-sm"
                                force-no-text force-icon @click="editEntry(songbookIndex)" />
                    <nav-button icon="mdi mdi-delete" title="Eintrag löschen" v-if="editing != songbookIndex"
                                class="btn-sm" type="danger"
                                force-no-text force-icon @click="deleteEntry(songbookIndex)" />
                    <nav-button icon="mdi mdi-check" v-if="editing == songbookIndex"
                                class="btn-sm"
                                type="success" title="Bestätigen"
                                force-no-text force-icon @click="editing = -1" />
                </td>
            </tr>
            </tbody>
        </table>
        <hr />
        <nav-button icon="mdi mdi-plus" title="Eintrag hinzufügen" class="btn-sm"
                    @click="addEntry">Eintrag hinzufügen</nav-button>
    </div>
</template>

<script>
import NavButton from "../../../Ui/buttons/NavButton";
import FormInput from "../../../Ui/forms/FormInput";
import SongbookSelect from "./SongbookSelect";
export default {
    name: "SongbookList",
    components: {SongbookSelect, FormInput, NavButton},
    props: ['value'],
    created() {
        axios.get(route('api.songbooks.index', { api_token: this.apiToken }))
        .then(result => {
            this.allSongbooks = result.data;
        });
    },
    data() {
        return {
            editing: -1,
            mySongbooks: this.value,
            allSongbooks: [],
            apiToken: this.$page.props.currentUser.data.api_token,
        };
    },
    methods: {
        editEntry(songbookIndex) {
            this.editing = songbookIndex;
        },
        deleteEntry(songbookIndex) {
            this.mySongbooks.splice(songbookIndex, 1)
            this.$forceUpdate();
        },
        addEntry() {
            this.mySongbooks.push({
                code: '',
                description: '',
                id: null,
                image: null,
                isbn: null,
                name: '',
                pivot: {
                    reference: '',
                    song_id: null,
                    songbook_id: null,
                }
            });
            this.editing = this.mySongbooks.length - 1;
        }
    }
}
</script>

<style scoped>

</style>
