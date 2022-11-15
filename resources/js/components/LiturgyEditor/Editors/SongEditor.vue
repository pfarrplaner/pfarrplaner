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
                    <form-selectize label="Lied" :options="lists.songs" @input="selectSong"
                                    :key="songListState"
                                    v-model="selectedSong"/>
                    <div class="form-group">
                        <label for="verses">Zu singende Strophen</label>
                        <input class="form-control" :value="editedElement.data.verses"
                               placeholder="Gültiges Format z.B. 1-2+4; leer lassen = alle Strophen"
                               @input="updateQuote($event)"/>
                        <small>Strophen: {{ getVersesToDisplay(editedElement.data.verses) }}</small>
                    </div>
                    <div class="form-group">
                        <inertia-link class="btn btn-sm btn-light" title="Neues Lied anlegen" :href="route('song.create')">
                            Neues Lied
                        </inertia-link>
                        <inertia-link v-if="(undefined != editedElement.data.song) && (undefined != editedElement.data.song.song) && (editedElement.data.song.song.id > 0)"
                                      class="btn btn-sm btn-light"
                                title="Lied bearbeiten" :href="route('song.edit', editedElement.data.song.song.id)">Lied bearbeiten
                        </inertia-link>
                        <button v-if="(undefined != editedElement.data.song) && (undefined != editedElement.data.song.song) && (editedElement.data.song.song.id != -1)"
                                class="btn btn-sm btn-light"
                                title="Lied im Noteneditor bearbeiten" @click.prevent="editMusic">
                            <span class="mdi mdi-music"></span> Lied im Noteneditor bearbeiten
                        </button>
                    </div>
                </div>

            </div>
            <div class="col-md-6">
                <div class="liturgical-text-quote" v-html="quote" :key="editedElement.data.song.song.id"/>
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
import FormSelectize from "../../Ui/forms/FormSelectize";

export default {
    name: "SongEditor",
    components: {
        FormSelectize,
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
        this.selectSong(this.editedElement.data.song.id);
    },
    data() {
        var e = this.element;

        if (undefined == e.data.song) e.data.song = {};
        if (undefined == e.data.song.song) e.data.song.song = {};
        if (undefined == e.data.song.song.verses) e.data.song.song.verses = [];
        if (undefined == e.data.song.song.id) e.data.song.song.id = -1;

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
        const component = this;
        return {
            apiToken: this.$page.props.currentUser.data.api_token,
            editedElement: e,
            emptySong: emptySong,
            songs: this.lists.songs,
            songList: [],
            songListState: 0,
            songbook: e.data.song.songbook + '||' + e.data.song.songbook_abbreviation,
            songbooks: null,
            songIsDirty: false,
            selectedSong: e.data.song.id,
            component: this,
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
        songbook: {
            handler: function (newVal, oldVal) {
                var tmp = newVal.split('||');
                this.editedElement.data.song.songbook = tmp[0];
                this.editedElement.data.song.songbook_abbreviation = tmp[1];
            }
        },
        'lists.songs': {
            handler: function(newVal, oldVal) {
                this.selectSong(this.editedElement.data.song.id);
                this.songListState++;
            }
        }
    },
    methods: {
        selectSong(e) {
            console.log('selectSong', e);
            if (!e) return;
            if (!e) return;
            if (e < 0) return;
            if (undefined == this.songList[e]) {
                axios.get(route('api.liturgy.song.single', {
                    api_token: this.apiToken,
                    songReferenceId: e,
                })).then(response => {
                    this.songList[e] = response.data;
                    this.editedElement.data.song = this.songList[e];
                    this.quote = this.quotableText();
                    this.$forceUpdate();
                });
            } else {
                this.editedElement.data.song = this.songList[e];
                this.quote = this.quotableText();
                this.$forceUpdate();
            }
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
