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

<template xmlns="http://www.w3.org/1999/html">
    <div class="liturgy-editor-info-pane">
        <div class="card" v-if="liturgy['title']">
            <div class="card-body">
                <div class="row" v-if="liturgy['title']">
                    <div class="col-2">
                        <div v-if="liturgy['title']">
                            <b>{{ liturgy['title'] }}</b>
                            <span v-if="liturgy['litColor']" class="litColor"
                                  :style="{ backgroundColor: liturgy['litColor'] }"
                                  :title="'Liturgische Farbe: '+liturgy['litColorName']"></span>
                        </div>
                        <div v-if="liturgy['feastCircleName']">
                            <span class="badge badge-info">{{
                                    liturgy['feastCircleName']
                                }} ({{ romanize(liturgy['perikope']) }}) </span>
                        </div>
                        <div v-if="liturgy['subjects']">
                        <span v-for="subject in liturgy['subjects']"
                              class="badge badge-light">{{ subject.subjectTitle }}</span>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-light" @click.prevent="$emit('info')"
                                    title="Weitere Informationen">
                                <span class="fa fa-info"></span> <span class="d-none d-md-inline">Weitere Infos</span>
                            </button>
                        </div>
                    </div>
                    <div class="col-2">
                        <bible-reference :liturgy="service.day.liturgy" liturgy-key="litTextsWeeklyQuote" title="WSp"/>
                        <bible-reference :liturgy="service.day.liturgy" liturgy-key="litTextsWeeklyPsalm" title="Ps"/>
                        <bible-reference :liturgy="service.day.liturgy" liturgy-key="currentPerikope" title="Pr"/>
                    </div>
                    <div class="col-2">
                        <bible-reference v-for="n in 3" :liturgy="service.day.liturgy"
                                         :liturgy-key="'litTextsPerikope'+n"
                                         :key="'litTextsPerikope'+n"
                                         :title="romanize(n)"
                                         :style="{fontWeight: (n==liturgy['perikope']) ? 'bold' : 'normal' }"/>
                    </div>
                    <div class="col-2">
                        <bible-reference v-for="n in 3" :liturgy="service.day.liturgy"
                                         :liturgy-key="'litTextsPerikope'+(n+3)"
                                         :key="'litTextsPerikope'+(n+3)"
                                         :title="romanize(n+3)"
                                         :style="{fontWeight: ((n+3)==liturgy['perikope']) ? 'bold' : 'normal' }"/>
                    </div>
                    <div class="col-4">
                        <div v-if="liturgy['songs']" v-for="song in liturgy['songs']">
                            {{ song.number }} {{ song.title }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <funeral-info-pane v-for="funeral in service.funerals"
                           :key="'funeral_info_'+funeral.id"
                           :funeral="funeral" :service="service" />
    </div>
</template>

<script>
import BibleReference from "../Elements/BibleReference";
import FuneralInfoPane from "./FuneralInfoPane";

export default {
    name: "InfoPane",
    components: {FuneralInfoPane, BibleReference},
    data() {
        return {
            liturgy: this.service.day.liturgy,
        };
    },
    props: ['service'],
    methods: {
        /**
         * @source http://blog.stevenlevithan.com/archives/javascript-roman-numeral-converter
         * @param num
         * @returns {string|number}
         */
        romanize(num) {
            if (isNaN(num))
                return NaN;
            var digits = String(+num).split(""),
                key = ["", "C", "CC", "CCC", "CD", "D", "DC", "DCC", "DCCC", "CM",
                    "", "X", "XX", "XXX", "XL", "L", "LX", "LXX", "LXXX", "XC",
                    "", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX"],
                roman = "",
                i = 3;
            while (i--)
                roman = (key[+digits.pop() + (i * 10)] || "") + roman;
            return Array(+digits.join("") + 1).join("M") + roman;
        },
    }
}
</script>

<style scoped>
.liturgy-editor-info-pane {
    font-size: 0.8em;
}

.litColor {
    border: solid 1px gray;
    min-width: 10px;
    min-height: 10px;
    border-radius: 5px;
    display: inline-block;
}
</style>
