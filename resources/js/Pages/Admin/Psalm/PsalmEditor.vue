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
    <admin-layout title="Psalm bearbeiten">
        <template v-slot:navbar-left>
            <save-button @click="savePsalm"/>
            <nav-button @click="saveAsCopy" class="ml-1" title="Kopie als neuen Psalm speichern"
                        type="light" icon="mdi mdi-content-save-move-outline">Kopie speichern</nav-button>
        </template>
        <div class="row">
            <div class="col-md-8">
                <form-input label="Titel des Psalms" v-model="myPsalm.title"/>
                <form-textarea name="intro" label="Einleitende Worte" v-model="myPsalm.intro" />
                <form-textarea name="text" label="Text" v-model="myPsalm.text" />
                <form-textarea name="copyrights" label="Copyrights" v-model="myPsalm.copyrights" />
                <div class="row">
                    <div class="col-10">
                        <form-input name="songbook" label="Liederbuch" v-model="myPsalm.songbook" />
                    </div>
                    <div class="col-2 form-group">
                        <form-input name="songbook_abbreviation" label="Abkürzung" v-model="myPsalm.songbook_abbreviation" />
                    </div>
                </div>
                <form-input name="reference" label="Liednummer" v-model="myPsalm.reference"/>
            </div>
            <div class="col-md-4">
                <div class="liturgical-text-quote">
                    <p v-if="myPsalm.title"><b>{{ myPsalm.title }}</b></p>
                    <p v-if="myPsalm.intro"><i>{{ myPsalm.intro }}</i></p>
                    <nl2br v-if="myPsalm.text" tag="p" :text="myPsalm.text" />
                </div>
                <text-stats :text="myPsalm.text" />
            </div>
        </div>
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
import TextStats from "../../../components/LiturgyEditor/Elements/TextStats";
import Nl2br from 'vue-nl2br';
import NavButton from "../../../components/Ui/buttons/NavButton";

export default {
    name: "SongEditor",
    components: {
        NavButton,
        TextStats, Nl2br, FormTextarea, FormInput, SaveButton
    },
    props: ['psalm'],
    data() {
        return {
            myPsalm: this.psalm,
            activeTab: 'home',
        }
    },
    methods: {
        savePsalm() {
            if (this.myPsalm.id) {
                this.$inertia.patch(route('psalm.update', this.myPsalm.id), this.myPsalm);
            } else {
                this.saveAsCopy();
            }
        },
        saveAsCopy() {
            this.$inertia.post(route('psalm.store'), {
                title: this.myPsalm.title,
                copyrights: this.myPsalm.copyrights,
                intro: this.myPsalm.intro,
                songbook: this.myPsalm.songbook,
                songbook_abbreviation: this.myPsalm.songbook_abbreviation,
                reference: this.myPsalm.reference,
                text: this.myPsalm.text,
            });
        }
    }
}
</script>

<style scoped>
.liturgical-text-quote {
    padding: 20px;
    border-left: solid 3px lightgray;
    margin: 10px;
}

</style>
