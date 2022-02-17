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



                <div class="accordion" id="accordionExample">
                    <div class="card z-depth-0 bordered">
                        <div class="card-header" :id="'setupHeading'">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse" :data-target="'#setupBody'"
                                        aria-expanded="true" :aria-controls="'setupBody'">
                                    Einstellungen
                                </button>
                            </h5>
                        </div>
                        <div :id="'setupBody'" class="collapse p-2 show"  aria-labelledby="headingOne"
                             data-parent="#accordionExample">
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
                            <form-textarea name="prolog" label="Weitere Konfiguration" v-model="mySong.prolog" help="Kopfzeilen (ABC-Notation)"/>
                        </div>
                    </div>
                    <div class="card z-depth-0 bordered">
                        <div class="card-header" :id="'notationRefrainHeading'">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" :data-target="'#notationRefrainBody'"
                                        aria-expanded="true" :aria-controls="'notationRefrainBody'">
                                    Kehrvers
                                </button>
                            </h5>
                        </div>
                        <div :id="'notationRefrainBody'" class="collapse p-2"  aria-labelledby="headingOne"
                             data-parent="#accordionExample">
                            <form-textarea name="notation_refrain" label="Notation (Kehrvers)" v-model="mySong.refrain_notation" />
                            <form-textarea name="refrain_text_notation" label="Kehrverstext" v-model="mySong.refrain_text_notation" />
                        </div>
                    </div>
                    <div class="card z-depth-0 bordered">
                        <div class="card-header" :id="'notationHeading'">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" :data-target="'#notationBody'"
                                        aria-expanded="true" :aria-controls="'notationBody'">
                                    Notation (Strophen)
                                </button>
                            </h5>
                        </div>
                        <div :id="'notationBody'" class="collapse p-2"  aria-labelledby="headingOne"
                             data-parent="#accordionExample">
                            <form-textarea name="notation" label="Notation (Strophen)" v-model="mySong.notation" />
                        </div>
                    </div>
                    <div v-for="(verse,verseIndex) in mySong.verses" class="card z-depth-0 bordered">
                        <div class="card-header" :id="'verseHeading'+verseIndex">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" :data-target="'#verseBody'+verseIndex"
                                        aria-expanded="true" :aria-controls="'verseBody'+verseIndex">
                                    <span class="toggle-visibility mr-1" :class="verseVisible[verse.number] ? 'mdi mdi-eye' : 'mdi mdi-eye-off'"
                                          @click.prevent.stop="verseVisible[verse.number] = !verseVisible[verse.number]"></span>
                                    Strophe {{ verse.number }} -- {{ verse.text.substr(0,40) }}...
                                </button>
                            </h5>
                        </div>
                        <div :id="'verseBody'+verseIndex" class="collapse p-2" aria-labelledby="headingOne"
                             data-parent="#accordionExample">
                            <form-textarea label="Verstext" v-model="verse.notation" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div id="paper"></div>
                <div id="refrain"></div>
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
        },
        refrain() {
            if (!this.mySong.refrain_notation) return '';
            let music = 'X:2\n'
                + 'T:Kehrvers\n'
                + 'M:'+this.mySong.measure+'\n'
                + 'L:'+this.mySong.note_length+'\n';
            if (this.mySong.refrain_notation) {
                let lineCtr = 0;
                let refrainLines = (this.mySong.refrain_text_notation || '').split('\n');
                this.mySong.refrain_notation.split('\n').forEach(line => {
                    music += line+'\n';
                    music += 'w:'+refrainLines[lineCtr]+'\n';
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
            mySong.verses[verseIndex].notation = mySong.verses[verseIndex].notation || mySong.verses[verseIndex].text || '';
            verseVisible[mySong.verses[verseIndex].number] = true;
        }
        mySong.refrain_text_notation = mySong.refrain_text_notation || mySong.refrain || '';
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
            this.renderer.renderAbc('paper', this.music);
            this.renderer.renderAbc('refrain', this.refrain);
        },
        saveSong() {
            axios.patch(route('liturgy.song.update', {song: this.mySong.id}), this.mySong);
        }
    }
}
</script>

<style scoped>
    .btn-link {
        width: 100%;
        text-align: left;
    }

    .btn-link:after {
        content: "\f107";
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        float: right;
    }

    .btn-link.collapsed:after {
        content: "\f106";
    }

</style>
