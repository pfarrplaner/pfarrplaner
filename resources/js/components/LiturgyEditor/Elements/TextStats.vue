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
    <div v-if="text && (text.length > 0)">
        <small class="form-text text-muted"><span v-if="!timeOnly">{{
                text.length.toLocaleString('de-DE')
            }} Zeichen &middot;
            {{ wordCount().toLocaleString('de-DE') }} Wörter &middot;
            Voraussichtliche Redezeit: </span>{{ calculatedSpeechTime() }}</small>
    </div>
</template>

<script>
export default {
    name: "TextStats",
    props: ['text', 'timeOnly', 'hideHours', 'wpm'],
    methods: {
        wordCount() {
            return this.text.trim().split(/\s+/).length;
        },
        formatTime(sec_num) {
            var hours = Math.floor(sec_num / 3600);
            var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
            var seconds = sec_num - (hours * 3600) - (minutes * 60);

            if (hours < 10) {
                var hoursText = "0" + hours;
            }
            if (minutes < 10) {
                minutes = "0" + minutes;
            }
            if (seconds < 10) {
                seconds = "0" + seconds;
            }
            return (this.hideHours ? (hours > 0 ? hoursText+':' : '') : hoursText+':') + minutes + ':' + seconds;
        },
        calculatedSpeechTime() {
            const defaultWPM = 110;
            const wpm = this.wpm || defaultWPM;
            const speechTime = (this.wordCount()) / wpm * 60;
            return this.formatTime(parseInt(speechTime, 10));
        },
    }
}
</script>

<style scoped>

</style>
