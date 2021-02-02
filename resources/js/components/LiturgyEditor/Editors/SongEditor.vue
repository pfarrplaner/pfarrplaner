<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
  - @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
  - @link https://github.com/potofcoffee/pfarrplaner
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
    <form @submit.prevent="save">
        <div class="liturgy-item-song-editor">
            <div class="form-group">
                <label for="title">Titel im Ablaufplan</label>
                <input class="form-control" v-model="editedElement.title" v-focus/>
            </div>
            <div v-if="this.songs.length == 0">
                Bitte warten, Liederliste wird geladen...
            </div>
            <div v-else>
                <div class="form-group">
                    <label for="existing_text">Lied</label>
                    <select class="form-control" name="existing_text" v-model="editedElement.data.song">
                        <option :value="emptySong"></option>
                        <option v-for="song in songs" :value="song">{{ displayTitle(song) }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="verses">Zu singende Strophen</label>
                    <input class="form-control" v-model="editedElement.data.verses" placeholder="Gültiges Format z.B. 1-2+4"/>
                    <small>Strophen: {{ getVersesToDisplay(editedElement.data.verses) }}</small>
                </div>
                <div v-if="(editedElement.data.song.id == -1) || editSong">
                    <div class="form-group">
                        <label for="title">Titel des Lieds</label>
                        <input class="form-control" v-model="editedElement.data.song.title"/>
                    </div>
                    <div class="form-group">
                        <label>Kehrvers</label>
                        <textarea class="form-control" v-model="editedElement.data.song.refrain" placeholder="Leer lassen, wenn es keinen Kehrvers gibt"></textarea>
                    </div>
                    <div class="row" v-for="verse in editedElement.data.song.verses">
                        <div class="col-1 form-group">
                            <label>Strophe</label>
                            <input type="text" class="form-control" v-model="verse.number"/>
                        </div>
                        <div class="col-10">
                            <div class="form-group">
                                <label>Text der Strophe</label>
                                <textarea class="form-control" v-model="verse.text"></textarea>
                            </div>
                            <div class="form-check-inline">
                                <input type="checkbox" v-model="verse.refrain_before" /> &nbsp;Kehrvers vor dieser Strophe
                            </div>
                            <div class="form-check-inline">
                                <input type="checkbox" v-model="verse.refrain_after" />  &nbsp;Kehrvers nach dieser Strophe
                            </div>
                        </div>
                        <div class="col-1 text-right" style="margin-top: 2em;">
                            <button class="btn btn-sm btn-danger" @click.prevent="deleteVerse(verseIndex)">
                                <span class="fa fa-trash"></span>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Copyrights</label>
                        <textarea class="form-control" v-model="editedElement.data.song.copyrights"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-11">
                            <div class="form-group">
                                <label>Liederbuch</label>
                                <textarea class="form-control" v-model="editedElement.data.song.songbook"></textarea>
                            </div>
                        </div>
                        <div class="col-1 form-group">
                            <label>Abkürzung</label>
                            <input type="text" class="form-control" v-model="editedElement.data.song.songbook_abbreviation"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="reference">Liednummer</label>
                        <input class="form-control" v-model="editedElement.data.song.reference"/>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-sm btn-light" @click.prevent="addVerse">Strophe hinzufügen</button>
                        <button class="btn btn-sm btn-light" @click.prevent="saveText" v-if="!editSong">Als neues Lied speichern</button>
                        <button class="btn btn-sm btn-light" @click.prevent="updateText" v-if="editSong" :title="songIsDirty ? 'Es existieren ungespeicherte Änderungen am Lied.' : ''">
                            <span v-if="songIsDirty" class="fa fa-exclamation-triangle" style="color:red;"></span>
                            Änderungen am Lied speichern
                        </button>
                    </div>
                </div>
                <div v-else>
                    <button class="btn btn-sm btn-light" @click.prevent="editSong = true">Lied bearbeiten</button>
                    <div class="liturgical-text-quote" v-html="quotableText()" />
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-primary" @click="save" :disabled="songIsDirty" :title="songIsDirty ? 'Du musst zuerst die Änderungen am Lied speichern.' : ''">Speichern</button>
                <inertia-link class="btn btn-secondary" :href="route('services.liturgy.editor', this.service.id)">
                    Abbrechen
                </inertia-link>
            </div>
        </div>
        <hr/>
        <pre>{{ JSON.stringify(editedElement, null, 4) }}</pre>
    </form>
</template>

<script>
import Nl2br from 'vue-nl2br';

export default {
    name: "SongEditor",
    components: {
        Nl2br,
    },
    props: ['element', 'service'],
    /**
     * Load existing songs
     * @returns {Promise<void>}
     */
    async created() {
        const songs = await axios.get(route('liturgy.song.index'))
        if (songs.data) {
            this.songs = songs.data;
        }
    },
    data() {
        var e = this.element;
        var emptySong = {
            id: -1,
            title: '',
            verses: [
                {id: -1, number: '1', text: '', refrain_before: false, refrain_after: false}
            ],
            refrain: '',
            copyrights: '',
            songbook: '',
            songbook_abbreviation: '',
            reference: '',
        };

        if (undefined == e.data.verses) e.data.verses = '';
        if (undefined == e.data.song) e.data.song = emptySong;
        return {
            editedElement: e,
            emptySong: emptySong,
            editSong: false,
            songs: {},
            songIsDirty: false,
        };
    },
    watch: {
        'editedElement.data.song': {
            handler: function (oldVal, newVal) {
                this.songIsDirty = this.editSong;
            },
            deep: true,
        }
    },
    methods: {
        addVerse() {
            this.editedElement.data.song.verses.push({
                id: -1,
                number: this.editedElement.data.song.verses.length + 1,
                text: '',
                refrain_before: false,
                refrain_after: false
            });
        },
        deleteVerse(verseIndex) {
            this.editedElement.data.song.verses = this.editedElement.data.song.verses.splice(verseIndex, 1);
        },
        save: function () {
            this.$inertia.patch(route('liturgy.item.update', {
                service: this.service.id,
                block: this.element.liturgy_block_id,
                item: this.element.id,
            }), this.editedElement, {preserveState: false});
        },
        saveText() {
            axios.post(route('liturgy.song.store'), this.editedElement.data.song).then(response => {
                return response.data;
            }).then(data => {
                this.editSong = false;
                this.songs = data.songs;
                this.editedElement.data.song = data.song;
                this.songIsDirty = false;
            });
            this.songIsDirty = false;
        },
        updateText() {
            axios.patch(route('liturgy.song.update', this.editedElement.data.song.id), this.editedElement.data.song).then(response => {
                return response.data;
            }).then(data => {
                this.editSong = false;
                this.songs = data.songs;
                this.editedElement.data.song = data.song;
                this.songIsDirty = false;
            });
            this.songIsDirty = false;
        },
        displayTitle(song) {
            var title = song.title;
            if (song.reference)  {
                title = song.reference+' '+title;
            }
            if (song.songbook_abbreviation) {
                title = song.songbook_abbreviation+' '+title;
            } else if (song.songbook) {
                title = song.songbook+' '+title;
            }
            return title;
        },
        quotableText() {
            var text = '';
            var title = this.displayTitle(this.editedElement.data.song);
            if (title.trim().length > 0) {
                text = '<b>'+title+"</b>\n\n";
            }

            var e = this.editedElement;
            this.getVersesToDisplay(e.data.verses).forEach(function(verseNumber){
                e.data.song.verses.forEach(function (verse) {
                    if (verse.number == verseNumber) {
                        if (verse.refrain_before) {
                            text = text + "<p>"+e.data.song.refrain+"</p>";
                        }
                        text = text + "<p>"+(verse.number != '' ? verse.number+'. ' : '')+verse.text+"</p>";
                        if (verse.refrain_after) {
                            text = text + "<p>"+e.data.song.refrain+"</p>";
                        }
                    }
                });
            });

            return text;
        },
        getVersesToDisplay(range) {
            var verses = [];
            if ((null === range) || (undefined === range)) return [];
            if (range == '') {
                this.editedElement.data.song.verses.forEach(function (verse){
                    verses.push(verse.number);
                });
                return verses;
            }
            range.split('+').forEach(function (subRange){
                if (subRange.indexOf('-') >= 0) {
                    var tmp = subRange.split('-');
                    if ((tmp[0] != '') && (tmp[1] !='' )) {
                        if ((!isNaN(tmp[0])) && (!isNaN(tmp[1]))) {
                            for (var i=parseInt(tmp[0]); i<=parseInt(tmp[1]); i++) {
                                verses.push(i.toString());
                            }
                        }
                    } else if (tmp[0] != '') {
                        verses.push(tmp[0]);
                    }
                } else {
                    if (subRange != '') verses.push(subRange);
                }
            });
            return verses;
        },
    }
}
</script>

<style scoped>
.liturgy-item-song-editor {
    padding: 5px;
}

.liturgical-text-quote {
    padding: 20px;
    border-left: solid 3px lightgray;
    margin: 10px;
}
</style>
