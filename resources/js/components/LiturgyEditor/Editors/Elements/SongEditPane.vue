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
    <div class="song-edit-pane">
        <tab-headers>
            <tab-header id="home" :active-tab="activeTab" title="Allgemeines" :switch-handler="true" @tab="activeTab = $event"/>
            <tab-header id="text" :active-tab="activeTab" title="Text"  :switch-handler="true" @tab="activeTab = $event"/>
            <tab-header id="songbooks" :active-tab="activeTab" title="Liederbücher"  :switch-handler="true" @tab="activeTab = $event"/>
        </tab-headers>
        <tabs>
            <tab id="home" :active-tab="activeTab">
                <form-input label="Titel des Lieds" v-model="song.song.title" />
                <form-textarea label="Copyrights" v-model="song.song.copyrights" />
            </tab>
            <tab id="text" :active-tab="activeTab">
                <form-textarea label="Kehrvers" v-model="song.song.refrain"
                               placeholder="Leer lassen, wenn es keinen Kehrvers gibt" />
                <div class="row">
                    <div class="col-1 form-group">
                        <label>Strophe</label>
                    </div>
                    <div class="col-10 form-group">
                        <label>Text der Strophe</label>
                    </div>
                </div>
                <div v-for="(verse,verseKey,verseIndex) in song.song.verses" class="mb-2">
                    <div class="row">
                        <div class="col-1 form-group">
                            <input type="text" class="form-control" v-model="verse.number"/>
                        </div>
                        <div class="col-10">
                            <div class="form-group">
                                <textarea class="form-control" v-model="verse.text"></textarea>
                            </div>
                            <div class="form-check-inline">
                                <input type="checkbox" v-model="verse.refrain_before"/> &nbsp;Kehrvers vor
                                dieser
                                Strophe
                            </div>
                            <div class="form-check-inline">
                                <input type="checkbox" v-model="verse.refrain_after"/> &nbsp;Kehrvers nach
                                dieser
                                Strophe
                            </div>
                        </div>
                        <div class="col-1 text-right" style="margin-top: 2em;">
                            <button class="btn btn-sm btn-danger" @click.prevent="deleteVerse(verseKey)">
                                <span class="mdi mdi-delete"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
                    <button class="btn btn-sm btn-light" @click.prevent="addVerse">Strophe hinzufügen</button>
                </div>
            </tab>
            <tab id="songbooks" :active-tab="activeTab">
                <songbook-list v-model="song.song.songbooks" />
            </tab>
        </tabs>
    </div>
</template>

<script>
import SongbookList from "./SongbookList";
import TabHeaders from "../../../Ui/tabs/tabHeaders";
import TabHeader from "../../../Ui/tabs/tabHeader";
import Tabs from "../../../Ui/tabs/tabs";
import Tab from "../../../Ui/tabs/tab";
import FormInput from "../../../Ui/forms/FormInput";
import FormTextarea from "../../../Ui/forms/FormTextarea";
export default {
    name: "SongEditPane",
    props: ['song'],
    components: {FormTextarea, FormInput, Tab, Tabs, TabHeader, TabHeaders, SongbookList},
    data() {
        return {
            activeTab: 'home',
        }
    },
    methods: {
        addVerse() {
            this.song.song.verses.push({
                id: -1,
                number: this.song.song.verses.length + 1,
                text: '',
                refrain_before: false,
                refrain_after: false
            });
        },
        deleteVerse(verseIndex) {
            this.song.song.verses.splice(verseIndex, 1);
        },
    }
}
</script>

<style scoped>

</style>
