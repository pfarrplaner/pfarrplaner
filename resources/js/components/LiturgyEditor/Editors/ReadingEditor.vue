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
    <div class="liturgy-item-reading-editor">
        <div class="form-group">
            <label for="title">Titel im Ablaufplan</label>
            <input class="form-control" v-model="editedElement.title" v-focus/>
        </div>
        <form-bible-reference-input v-model="editedElement.data.reference" :sources="textSources"/>
    </div>
</template>

<script>
import TimeFields from "./Elements/TimeFields";
import FormBibleReferenceInput from "../../Ui/forms/FormBibleReferenceInput";

export default {
    name: "ReadingEditor",
    components: {FormBibleReferenceInput, TimeFields},
    props: {
        element: Object,
        service: Object,
        agendaMode: {
            type: Boolean,
            default: false,
        }
    },
    data() {
        var e = this.element;
        if (undefined === e.data.reference) e.data.reference = '';

        let textSources = {};
        if (undefined !== this.service.liturgicalInfo.title) {
            textSources['Perikope für ' + this.service.liturgicalInfo.title] = this.service.liturgicalInfo.currentPerikope;
            for (let i = 1; i <= 6; i++) {
                textSources[this.service.liturgicalInfo.title + ' ' + this.romanize(i)] = this.service.liturgicalInfo['litTextsPerikope' + i];
            }
            textSources[this.service.liturgicalInfo.title + ' Psalm'] = this.service.liturgicalInfo['litTextsWeeklyPsalm'];
            textSources[this.service.liturgicalInfo.title + ' Wochenspruch'] = this.service.liturgicalInfo['litTextsWeeklyQuote'];
        }
        this.service.baptisms.forEach(baptism => {
            if (baptism.text) textSources['Taufspruch ' + baptism.candidate_name] = baptism.text;
        });
        this.service.funerals.forEach(funeral => {
            if (funeral.text) textSources['Beerdigungstext ' + funeral.buried_name] = funeral.text;
            if (funeral.confirmation_text) textSources['Denkspruch ' + funeral.buried_name] = funeral.confirmation_text;
            if (funeral.wedding_text) textSources['Trauspruch ' + funeral.buried_name] = funeral.wedding_text;
        });
        this.service.weddings.forEach(wedding => {
            if (wedding.text) textSources['Trauspruch ' + wedding.spouse1_name + ' & ' + wedding.spouse2_name] = wedding.text;
        });

        return {
            editedElement: e,
            textSources,
        };
    },
    methods: {
        save: function () {
            this.$inertia.patch(route('liturgy.item.update', {
                service: this.service.id,
                block: this.element.liturgy_block_id,
                item: this.element.id,
            }), this.element, {preserveState: false});
        },
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
.liturgy-item-reading-editor {
    padding: 5px;
}
</style>
