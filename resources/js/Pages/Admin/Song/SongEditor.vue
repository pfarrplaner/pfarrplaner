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
    <admin-layout title="Lied bearbeiten">
        <template v-slot:navbar-left>
            <save-button @click="saveSong"/>
        </template>
        <template v-slot:tab-headers>
            <tab-headers>
                <tab-header id="home" :active-tab="activeTab" title="Allgemeines" :switch-handler="true"
                            @tab="activeTab = $event"/>
                <tab-header id="text" :active-tab="activeTab" title="Text" :switch-handler="true"
                            @tab="activeTab = $event"/>
                <tab-header id="songbooks" :active-tab="activeTab" title="Liederbücher" :switch-handler="true"
                            @tab="activeTab = $event"/>
            </tab-headers>
        </template>
        <tabs>
            <tab id="home" :active-tab="activeTab">
                <form-input label="Titel des Lieds" v-model="song.title"/>
                <form-textarea label="Copyrights" v-model="song.copyrights"/>
            </tab>
            <tab id="text" :active-tab="activeTab">
                <form-textarea label="Kehrvers" v-model="song.refrain"
                               placeholder="Leer lassen, wenn es keinen Kehrvers gibt"/>
                <div class="row">
                    <div class="col-1 form-group">
                        <label>Strophe</label>
                    </div>
                    <div class="col-10 form-group">
                        <label>Text der Strophe</label>
                    </div>
                </div>
                <div v-for="(verse,verseKey,verseIndex) in song.verses">
                    <div class="row">
                        <div class="col-1 form-group">
                            <input type="text" class="form-control" v-model="verse.number"/>
                        </div>
                        <div class="col-10">
                            <div class="form-group">
                                <textarea class="form-control" v-model="verse.text"></textarea>
                            </div>
                            <div class="form-check-inline">
                                <input type="checkbox" v-model="verse.refrain_before"/> &nbsp;
                                Kehrvers vor dieser Strophe
                            </div>
                            <div class="form-check-inline">
                                <input type="checkbox" v-model="verse.refrain_after"/> &nbsp;
                                Kehrvers nach dieser Strophe
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
                <songbook-list v-model="song.songbooks" :allow-split="true" />
            </tab>
        </tabs>
    </admin-layout>
</template>

<script>
import SaveButton from "../../../components/Ui/buttons/SaveButton";
import FormInput from "../../../components/Ui/forms/FormInput";
import FormTextarea from "../../../components/Ui/forms/FormTextarea";
import FormImageAttacher from "../../../components/Ui/forms/FormImageAttacher";
import SongEditPane from "../../../components/LiturgyEditor/Editors/Elements/SongEditPane";
import TabHeaders from "../../../components/Ui/tabs/tabHeaders";
import TabHeader from "../../../components/Ui/tabs/tabHeader";
import Tabs from "../../../components/Ui/tabs/tabs";
import Tab from "../../../components/Ui/tabs/tab";
import SongbookList from "../../../components/LiturgyEditor/Editors/Elements/SongbookList";

export default {
    name: "SongEditor",
    components: {
        SongbookList,
        Tab, Tabs, TabHeader, TabHeaders, SongEditPane, FormImageAttacher, FormTextarea, FormInput, SaveButton
    },
    props: ['song'],
    data() {
        return {
            mySong: this.song,
            activeTab: 'home',
        }
    },
    methods: {
        saveSong() {
            if (this.mySong.id) {
                this.$inertia.patch(route('song.update', this.mySong.id), this.mySong);
            } else {
                this.$inertia.post(route('song.store'), this.mySong);
            }
        }
    }
}
</script>

<style scoped>

</style>
