<!--
  - Pfarrplaner
  -
  - @package Pfarrplaner
  - @author Christoph Fischer <chris@toph.de>
  - @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
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
    <div>
        <small><span class="mdi mdi-timer"></span> <span :class="speechTime == '00:00' ? 'warning' : ''">{{ speechTime }}</span></small>
    </div>
</template>

<script>
import TextStats from "./TextStats";

export default {
    name: "ItemTextStats",
    components: {TextStats},
    props: ['item', 'service'],
    data() {
        return {
            rounded: this.$page.props.settings.liturgy_times_rounded ?  true : false,
            wpm: this.$page.props.settings.wpm || 110,
        }
    },
    computed: {
        speechTime() {
            return this.item.data.time || this.calculatedSpeechTime();
        }
    },
    methods: {
        getItemText() {
            switch(this.item.data_type) {
                case 'freetext':
                    return this.item.data ? (this.item.data.description || '') : '';
                case 'liturgic':
                    return this.item.data ? (this.item.data.text || '') : '';
                case 'psalm':
                    return this.item.data.psalm ? (this.item.data.psalm.text || '') : '';
                case 'sermon':
                    return this.service.sermon ? (this.service.sermon.text || '') : '';
                case 'song':
                    return this.quotableSongText(this.item);
                case 'reading':
                    return this.item.data ? (this.item.data.text || '') : '';
            }
            return '';
        },
        getItemWPM() {
            return this.item.data.wpm || this.wpm || (this.item.data_type == 'song' ? 40 : 110);
        },
        getVersesToDisplay(song) {
            const range = song.data.verses;
            var verses = [];
            if (undefined == song.data.song) return [];
            if (undefined == song.data.song.song) return [];
            if ((null === range) || (undefined === range) || (range == '')) {
                song.data.song.song.verses.forEach(function (verse) {
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
        quotableSongText(song) {
            var text = '';
            this.getVersesToDisplay(song).forEach(function (verseNumber) {
                song.data.song.song.verses.forEach(function (verse) {
                    if (verse.number == verseNumber) {
                        if (verse.refrain_before) {
                            text = text + "<p>" + song.data.song.song.refrain + "</p>";
                        }
                        text = text + "<p>" + (verse.number != '' ? verse.number + '. ' : '') + verse.text + "</p>";
                        if (verse.refrain_after) {
                            text = text + "<p>" + song.data.song.song.refrain + "</p>";
                        }
                    }
                });
            });
            return text;
        },
        wordCount() {
            return this.getItemText().trim().split(/\s+/).length;
        },
        formatTime(sec_num) {
            var hours = Math.floor(sec_num / 3600);
            var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
            var seconds = sec_num - (hours * 3600) - (minutes * 60);

            var hoursText = '';
            if (hours) {
                hoursText = hours+':';
            }
            if (minutes < 10) {
                minutes = "0" + minutes;
            }
            if (seconds < 10) {
                seconds = "0" + seconds;
            }
            return hoursText + minutes + ':' + seconds;
        },
        speechTimeInSeconds() {
            let seconds = parseInt((this.wordCount()) / this.getItemWPM() * 60, 10);
            return this.rounded ? Math.ceil(seconds / 30) * 30 : seconds;
        },
        calculatedSpeechTime() {
            return this.formatTime(this.speechTimeInSeconds());
        },
    }
}
</script>

<style scoped>
.fa {
    color: lightgray;
}

span.warning {
    background-color: yellow;
}
</style>
