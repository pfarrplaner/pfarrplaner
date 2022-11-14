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
    <div class="liturgy-item-song-editor">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="title">Titel im Ablaufplan</label>
                    <input class="form-control" v-model="editedElement.title" v-focus/>
                </div>
                <div v-if="(!lists.songs) || (!lists.songs.length)">
                    <span class="mdi mdi-spin mdi-loading"></span> Bitte warten, Liederliste wird geladen...
                </div>
                <div v-else>
                    <div class="form-group">
                        <label for="existing_text">Lied</label>
                        <selectize class="form-control" name="existing_text" v-model="selectedSong">
                            <option v-for="song in lists.songs" :value="song.id">{{ displayTitle(song) }}</option>
                        </selectize>
                    </div>
                    <div class="form-group">
                        <label for="verses">Zu singende Strophen</label>
                        <input class="form-control" :value="editedElement.data.verses"
                               placeholder="Gültiges Format z.B. 1-2+4; leer lassen = alle Strophen"
                               @input="updateQuote($event)"/>
                        <small>Strophen: {{ getVersesToDisplay(editedElement.data.verses) }}</small>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-sm btn-light" title="Neues Lied anlegen" @click.prevent="newSong">
                            Neues Lied
                        </button>
                        <button v-if="editedElement.data.song.id != -1" class="btn btn-sm btn-light"
                                title="Lied bearbeiten" @click.prevent="editSong">Lied bearbeiten
                        </button>
                        <button v-if="editedElement.data.song.id != -1" class="btn btn-sm btn-light"
                                title="Lied im Noteneditor bearbeiten" @click.prevent="editMusic">
                            <span class="mdi mdi-music"></span> Lied im Noteneditor bearbeiten
                        </button>
                    </div>
                </div>

            </div>
            <div class="col-md-6">
                <div class="liturgical-text-quote" v-html="quote"/>
                <text-stats :text="quote"/>
            </div>
        </div>
        <modal :title="(modalSong.song.id == -1) ? 'Neues Lied' : 'Lied bearbeiten'" v-if="modalOpen"
               @close="((modalSong.song.id == -1) ? saveText() : updateText())" @cancel="modalOpen = false;"
               close-button-label="Lied speichern" cancel-button-label="Abbrechen" max-width="800"
        >
            <song-edit-pane :song="modalSong"/>


        </modal>
    </div>
</template>

<script>
import Nl2br from 'vue-nl2br';
import Selectize from 'vue2-selectize';
import Modal from "../../Ui/modals/Modal";
import TextStats from "../Elements/TextStats";
import TimeFields from "./Elements/TimeFields";
import SongEditPane from "./Elements/SongEditPane";

export default {
    name: "SongEditor",
    components: {
        SongEditPane,
        TimeFields,
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
    inject: ['lists'],
    /**
     * Load existing songs
     * @returns {Promise<void>}
     */
    async created() {
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
            apiToken: this.$page.props.currentUser.data.api_token,
            editedElement: e,
            emptySong: emptySong,
            songs: this.lists.songs,
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
                this.lists.songs.forEach(function (song) {
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
            this.modalSong =
                {
                    code: null,
                    songbook_id: null,
                    reference: null,
                    song_id: null,
                    song: {
                        id: -1,
                        title: '',
                        verses: [
                            {id: -1, number: '1', text: '', refrain_before: false, refrain_after: false}
                        ],
                        refrain: '',
                        copyrights: '',
                        songbooks: [],
                    },
                }
            this.modalOpen = true;
        },
        editSong() {
            this.modalSong = this.editedElement.data.song;
            this.modalOpen = true;
        },
        editMusic() {
            window.open(route('liturgy.song.musiceditor', this.editedElement.data.song.id));
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
            this.lists.songs = [];
            var component = this;
            axios.post(route('api.liturgy.song.store', {api_token: this.apiToken}), this.modalSong).then(response => {
                return response.data;
            }).then(data => {
                component.lists.songs = data.songs;
                component.editedElement.data.song = data.song;
                component.songIsDirty = false;
                component.selectedSong = data.song.id;
            });
            this.modalOpen = false;
            this.songIsDirty = false;
            this.quote = this.quotableText();
        },
        updateText() {
            var updateIndex = this.lists.songs.findIndex(element => element.id == this.editedElement.data.song.id);
            console.log('update song', this.modalSong);
            var component = this;
            axios.patch(route('api.liturgy.song.update', {
                song: this.editedElement.data.song.song.id,
                api_token: this.apiToken,
            }), this.modalSong).then(response => {
                console.log('updating ', updateIndex, this.lists.songs[updateIndex], response.data)
                this.lists.songs[updateIndex].song = response.data;
                this.editedElement.data.song.song = response.data;
                this.songIsDirty = false;
                this.selectedSong = this.lists.songs[updateIndex].id;
                this.$forceUpdate();
            });
            this.modalOpen = false;
            this.songIsDirty = false;
            this.quote = this.quotableText();
        },
        displayTitle(song) {
            if ((!song) || (!song.song)) return '';
            var title = song.song.title;
            if (song.reference) {
                title = song.reference + ' ' + title;
            }
            if (song.code) {
                title = song.code + ' ' + title;
            } else if (song.songbook) {
                title = song.songbook.name + ' ' + title;
            }
            return title;
        },
        getVersesToDisplay(range) {
            var verses = [];
            if ((null === range) || (undefined === range)) return [];
            if ((!this.editedElement.data.song) || (!this.editedElement.data.song.song)) return [];
            if (range == '') {
                this.editedElement.data.song.song.verses.forEach(function (verse) {
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
            var title = this.displayTitle(this.editedElement.data.song.song);
            if (title.trim().length > 0) {
                text = '<b>' + title + "</b>\n\n";
            }

            this.getVersesToDisplay(e.data.verses).forEach(function (verseNumber) {
                e.data.song.song.verses.forEach(function (verse) {
                    if (verse.number == verseNumber) {
                        if (verse.refrain_before) {
                            text = text + "<p>" + e.data.song.song.refrain + "</p>";
                        }
                        text = text + "<p>" + (verse.number != '' ? verse.number + '. ' : '') + verse.text + "</p>";
                        if (verse.refrain_after) {
                            text = text + "<p>" + e.data.song.song.refrain + "</p>";
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
.liturgy-item-song-editor {
    padding: 5px;
}

.liturgical-text-quote {
    padding: 20px;
    border-left: solid 3px lightgray;
    margin: 10px;
}
</style>
