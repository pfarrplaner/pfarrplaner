<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
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
    <div class="liturgy-item-psalm-editor">
        <form @submit.prevent="save">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="title">Titel im Ablaufplan</label>
                        <input class="form-control" v-model="editedElement.title" v-focus/>
                    </div>
                    <div v-if="this.songs === null">
                        Bitte warten, Liederliste wird geladen...
                    </div>
                    <div v-else>
                        <div class="form-group">
                            <label for="existing_text">Lied</label>
                            <selectize class="form-control" name="existing_text" v-model="selectedSong">
                                <option v-for="song in songs" :value="song.id">{{ displayTitle(song) }}</option>
                            </selectize>
                        </div>
                        <div class="form-group">
                            <label for="verses">Zu singende Strophen</label>
                            <input class="form-control" :value="editedElement.data.verses"
                                   placeholder="Gültiges Format z.B. 1-2+4; leer lassen = alle Strophen" @input="updateQuote($event)"/>
                            <small>Strophen: {{ getVersesToDisplay(editedElement.data.verses) }}</small>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-sm btn-light" title="Neues Lied anlegen" @click.prevent="newSong">
                                Neues Lied
                            </button>
                            <button v-if="editedElement.data.song.id != -1" class="btn btn-sm btn-light"
                                    title="Lied bearbeiten" @click.prevent="editSong">Lied bearbeiten
                            </button>
                        </div>
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="liturgical-text-quote" v-html="quote"/>
                    <text-stats :text="quote" />
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-primary" @click="save">Speichern
                </button>
                <inertia-link class="btn btn-secondary" :href="route('liturgy.editor', this.service.slug)">
                    Abbrechen
                </inertia-link>
            </div>
        </form>
        <modal :title="(editedElement.data.song.id == -1) ? 'Neues Lied' : 'Lied bearbeiten'" v-if="modalOpen"
               @close="(editedElement.data.song.id == -1) ? saveText() : updateText()" @cancel="modalOpen = false;"
               close-button-label="Lied speichern" cancel-button-label="Abbrechen" max-width="800"
        >


            <div class="form-group">
                <label for="title">Titel des Lieds</label>
                <input class="form-control" v-model="modalSong.title"/>
            </div>
            <div class="form-group">
                <label>Kehrvers</label>
                <textarea class="form-control" v-model="modalSong.refrain"
                          placeholder="Leer lassen, wenn es keinen Kehrvers gibt"></textarea>
            </div>
            <div class="row">
                <div class="col-1 form-group">
                    <label>Strophe</label>
                </div>
                <div class="col-10 form-group">
                    <label>Text der Strophe</label>
                </div>
            </div>
            <div v-for="(verse,verseKey,verseIndex) in modalSong.verses">
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
                            <span class="fa fa-trash"></span>
                        </button>
                    </div>
                </div>
                <hr/>
            </div>
            <div class="form-group">
                <button class="btn btn-sm btn-light" @click.prevent="addVerse">Strophe hinzufügen</button>
            </div>
            <div class="form-group">
                <label>Copyrights</label>
                <textarea class="form-control" v-model="modalSong.copyrights"></textarea>
            </div>
            <div class="form-group">
                <label>Liederbuch</label>
                <selectize v-model="songbook" :settings="selectizeSettings">
                    <option v-for="songbook in songbooks" :value="songbook">
                        {{ songbookTitle(songbook) }}
                    </option>
                </selectize>
            </div>
            <div class="form-group">
                <label for="reference">Liednummer</label>
                <input class="form-control" v-model="modalSong.reference"/>
            </div>
        </modal>
    </div>
</template>

<script>
import Nl2br from 'vue-nl2br';
import Selectize from 'vue2-selectize';
import Modal from "../../Ui/modals/Modal";
import TextStats from "../Elements/TextStats";

export default {
    name: "SongEditor",
    components: {
        TextStats,
        Nl2br,
        Selectize,
        Modal,
    },
    props: {
        element: Object,
        service: Object,
        agendaMode: {
            type: Boolean,
            default: false,
        }
    },
    /**
     * Load existing songs
     * @returns {Promise<void>}
     */
    async created() {
        const songs = await axios.get(route('liturgy.song.index'))
        if (songs.data) {
            this.songs = songs.data;
        }
        const songbooks = await axios.get(route('liturgy.song.songbooks'));
        if (songbooks.data) {
            var mySongbooks = [];
            var e = this.editedElement.data;
            Object.entries(songbooks.data).forEach(function (book) {
                book = book[1];
                mySongbooks.push(book.title + '||' + book.abbreviation);
            });
            this.songbooks = mySongbooks;
        }
    },
    mounted() {
        this.quote = this.quotableText();
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
            songs: null,
            songbook: e.data.song.songbook + '||' + e.data.song.songbook_abbreviation,
            songbooks: null,
            songIsDirty: false,
            selectedSong: e.data.song.id,
            selectizeSettings: {
                create: function (input, callback) {
                    return callback({
                        value: input + '||' + window.prompt('Bitte gib eine Abkürzung für "' + input + '" ein'),
                        text: input,
                    })
                },
                render: {
                    option_create: function (data, escape) {
                        return '<div class="create">Neues Liederbuch anlegen: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                    }
                },
            },
            modalSong: {},
            modalOpen: false,
            quote: '',
        };
    },
    watch: {
        'editedElement.data.verses': {
            handler: function (newVal) {
                this.quote = this.quotableText();
            }
        },
        'editedElement.data.song': {
            handler: function (oldVal, newVal) {
                this.songIsDirty = this.editSong;
                this.quote = this.quotableText();
            },
            deep: true,
        },
        selectedSong: {
            handler: function (newVal, oldVal) {
                var found = false;
                this.songs.forEach(function (song) {
                    if (song.id == newVal) found = song;
                });
                if (found) {
                    this.editedElement.data.song = found;
                }
                this.quote = this.quotableText();
            }
        },
        songbook: {
            handler: function (newVal, oldVal) {
                var tmp = newVal.split('||');
                this.editedElement.data.song.songbook = tmp[0];
                this.editedElement.data.song.songbook_abbreviation = tmp[1];
            }
        }
    },
    methods: {
        newSong() {
            this.modalSong = {
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
            this.modalOpen = true;
        },
        editSong() {
            this.modalSong = this.editedElement.data.song;
            this.modalOpen = true;
        },
        addVerse() {
            this.modalSong.verses.push({
                id: -1,
                number: this.modalSong.verses.length + 1,
                text: '',
                refrain_before: false,
                refrain_after: false
            });
        },
        deleteVerse(verseIndex) {
            this.editedElement.data.song.verses.splice(verseIndex, 1);
        },
        save: function () {
            var data = {
                ...this.editedElement,
                data: {
                    responsible: this.editedElement.data.responsible,
                    song: this.editedElement.data.song,
                    verses: this.editedElement.data.verses,
                }
            };
            this.$inertia.patch(route('liturgy.item.update', {
                service: this.service.id,
                block: this.element.liturgy_block_id,
                item: this.element.id,
            }), data, {preserveState: false});
            this.quote = this.quotableText();
        },
        saveText() {
            this.songs = [];
            var component = this;
            axios.post(route('liturgy.song.store'), this.modalSong).then(response => {
                return response.data;
            }).then(data => {
                component.songs = data.songs;
                component.editedElement.data.song = data.song;
                component.songIsDirty = false;
                component.selectedSong = data.song.id;
            });
            this.modalOpen = false;
            this.songIsDirty = false;
            this.quote = this.quotableText();
        },
        updateText() {
            this.songs = [];
            var component = this;
            axios.patch(route('liturgy.song.update', this.editedElement.data.song.id), this.modalSong).then(response => {
                return response.data;
            }).then(data => {
                component.songs = data.songs;
                component.editedElement.data.song = data.song;
                component.songIsDirty = false;
                component.selectedSong = data.song.id;
            });
            this.modalOpen = false;
            this.songIsDirty = false;
            this.quote = this.quotableText();
        },
        displayTitle(song) {
            var title = song.title;
            if (song.reference) {
                title = song.reference + ' ' + title;
            }
            if (song.songbook_abbreviation) {
                title = song.songbook_abbreviation + ' ' + title;
            } else if (song.songbook) {
                title = song.songbook + ' ' + title;
            }
            return title;
        },
        getVersesToDisplay(range) {
            var verses = [];
            if ((null === range) || (undefined === range)) return [];
            if (range == '') {
                this.editedElement.data.song.verses.forEach(function (verse) {
                    verses.push(verse.number);
                });
                return verses;
            }
            range.split('+').forEach(function (subRange) {
                if (subRange.indexOf('-') >= 0) {
                    var tmp = subRange.split('-');
                    if ((tmp[0] != '') && (tmp[1] != '')) {
                        if ((!isNaN(tmp[0])) && (!isNaN(tmp[1]))) {
                            for (var i = parseInt(tmp[0]); i <= parseInt(tmp[1]); i++) {
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
        quotableText() {
            var e = this.editedElement;

            var text = '';
            var title = this.displayTitle(this.editedElement.data.song);
            if (title.trim().length > 0) {
                text = '<b>' + title + "</b>\n\n";
            }

            this.getVersesToDisplay(e.data.verses).forEach(function (verseNumber) {
                e.data.song.verses.forEach(function (verse) {
                    if (verse.number == verseNumber) {
                        if (verse.refrain_before) {
                            text = text + "<p>" + e.data.song.refrain + "</p>";
                        }
                        text = text + "<p>" + (verse.number != '' ? verse.number + '. ' : '') + verse.text + "</p>";
                        if (verse.refrain_after) {
                            text = text + "<p>" + e.data.song.refrain + "</p>";
                        }
                    }
                });
            });

            return text;
        },
        songbookTitle(songbook) {
            var tmp = songbook.split('||');
            return tmp[0] + ' (' + tmp[1] + ')';
        },
        updateQuote(event) {
            this.editedElement.data.verses = event.target.value;
            this.quote = this.quotableText();
        }
    }
}
</script>

<style scoped>
.liturgy-item-psalm-editor {
    padding: 5px;
}

.liturgical-text-quote {
    padding: 20px;
    border-left: solid 3px lightgray;
    margin: 10px;
}
</style>
