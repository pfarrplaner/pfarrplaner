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
    <admin-layout :title="song.title + ' :: Noteneditor '">
        <template slot="navbar-left">
            <save-button @click="saveSong" />
        </template>
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-4">
                        <form-input name="key" label="Tonart" v-model="mySong.key" />
                    </div>
                    <div class="col-sm-4">
                        <form-input name="measure" label="Takt" v-model="mySong.measure" />
                    </div>
                    <div class="col-sm-4">
                        <form-input name="note_length" label="Notenlänge" v-model="mySong.note_length" />
                    </div>
                </div>
                <form-textarea name="notation" label="Notation (Strophen)" v-model="mySong.notation" />
                <form-textarea name="notation_refrain" label="Notation (Refrain)" v-model="mySong.refrain_notation" />



                <div class="accordion" id="accordionExample">
                    <div v-for="(verse,verseIndex) in mySong.verses" class="card z-depth-0 bordered">
                        <div class="card-header" :id="'verseHeading'+verseIndex">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse" :data-target="'#verseBody'+verseIndex"
                                        aria-expanded="true" :aria-controls="'verseBody'+verseIndex">
                                    <span class="fa" :class="verseVisible[verse.number] ? 'fa-eye' : 'fa-eye-slash'"
                                          @click.prevent.stop="verseVisible[verse.number] = !verseVisible[verse.number]"></span>
                                    Strophe {{ verse.number }} -- {{ verse.text.substr(0,40) }}...
                                </button>
                            </h5>
                        </div>
                        <div :id="'verseBody'+verseIndex" class="collapse p-2" :class="verseIndex == 0 ? 'show' : ''" aria-labelledby="headingOne"
                             data-parent="#accordionExample">
                            <form-textarea label="Notation" v-model="verse.notation" />
                        </div>
                    </div>
                </div>

                <form-textarea name="prolog" label="Weitere Konfiguration" v-model="mySong.prolog" />

                <pre id="music">{{ music }}</pre>
            </div>
            <div class="col-md-6">Noten
                <div id="paper">
                </div>
            </div>
        </div>
    </admin-layout>
</template>

<script>
import FormInput from "../../../components/Ui/forms/FormInput";
import FormTextarea from "../../../components/Ui/forms/FormTextarea";
import abcjs from 'abcjs';
import 'abcjs/abcjs-audio.css';
import SaveButton from "../../../components/Ui/buttons/SaveButton";


export default {
    name: "MusicEditor",
    components: {SaveButton, FormTextarea, FormInput},
    props: ['song'],
    computed: {
        music() {
            let music = 'X:1\n'
                + 'T:'+(this.mySong.songbook_abbreviation+' '+this.mySong.reference+' '+this.mySong.title).trim()+'\n'
                + 'C:'+this.mySong.copyrights+'\n'
                + 'M:'+this.mySong.measure+'\n'
                + 'L:'+this.mySong.note_length+'\n'
            ;

            let verseLines = {};
            this.mySong.verses.forEach(verse => {
                verseLines[verse.number] = [];
                let lines = verse.notation.trim().split('\n');
                lines.forEach(line => {
                    if (line.trim() != '') {
                        verseLines[verse.number].push('w:'+verse.number+'.~'+line);
                    }
                });
            });

            music += this.mySong.prolog ? this.mySong.prolog+'\n' : '';
            music += 'K:'+this.mySong.key+'\n';


            if (this.mySong.notation) {
                let lineCtr = 0;
                this.mySong.notation.split('\n').forEach(line => {
                    music += line+'\n';
                    this.mySong.verses.forEach(verse => {
                        if (this.verseVisible[verse.number]) {
                            music+=verseLines[verse.number][lineCtr] ? verseLines[verse.number][lineCtr]+'\n' : '';
                        }
                    });
                    lineCtr++;
                });
            }


            return music;
        }
    },
    data() {
        let mySong = this.setupSong(this.song);
        let verseVisible = {};
        for (const verseIndex in mySong.verses) {
            mySong.verses[verseIndex].notation = mySong.verses[verseIndex].notation || mySong.verses[verseIndex].text;
            verseVisible[mySong.verses[verseIndex].number] = true;
        }
        return {
            mySong,
            renderer: abcjs,
            verseVisible,
        }
    },
    mounted() {
        this.redraw();
    },
    watch: {
        mySong: {
            handler: function() {
                this.redraw();
            },
            deep: true,
        },
        verseVisible: {
            handler: function() {
                this.redraw();
            },
            deep: true,
        }
    },
    methods: {
        setupSong(mySong) {
            mySong.key = mySong.key || 'C';
            mySong.measure = mySong.measure || '4/4';
            mySong.note_length = mySong.note_length || '1/4';
            return mySong;
        },
        redraw() {
            this.renderer.renderAbc('paper', this.music)
        },
        saveSong() {
            axios.patch(route('liturgy.song.update', {song: this.mySong.id}), this.mySong)
            .then(response => {
                return response.data;
            }).then(data => {
                this.mySong = this.setupSong(data);
                window.history.back();
            });
        }
    }
}
</script>

<style scoped>

</style>
